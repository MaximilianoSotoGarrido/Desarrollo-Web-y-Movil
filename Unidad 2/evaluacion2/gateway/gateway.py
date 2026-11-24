"""
API Gateway de OfiExpress.

Unico punto de entrada publico del backend. Responsabilidades:
  - Distinguir rutas publicas (catalogo) de rutas autenticadas y administrativas.
  - Validar el token del cliente por introspeccion contra el Auth Service.
  - Obtener los secretos desde el administrador de secretos (compatible con Vault).
  - Firmar cada llamada interna con X-Gateway-Secret y propagar la identidad
    verificada; descarta cualquier cabecera de identidad que venga del navegador.

Levantar:
    uvicorn gateway:app --host 0.0.0.0 --port 8000
"""
import os

import httpx
from fastapi import FastAPI, Depends, HTTPException, Request, Response
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials

app = FastAPI(
    title="API Gateway OfiExpress",
    description="Enrutamiento y control de acceso al backend de la papeleria OfiExpress.",
)

# CORS: el navegador solo deja que la pagina del frontend llame al gateway
# si el gateway autoriza ese origen. Solo se permite el frontend local.
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost",
        "http://127.0.0.1",
        "http://localhost:8080",
        "http://127.0.0.1:8080",
    ],
    allow_methods=["GET", "POST", "PUT", "DELETE"],
    allow_headers=["Authorization", "Content-Type"],
)

security = HTTPBearer(auto_error=False)

AUTH_SERVICE_URL = os.getenv("AUTH_SERVICE_URL", "http://127.0.0.1:8100")
VAULT_ADDR = os.getenv("VAULT_ADDR", "http://127.0.0.1:8200")
VAULT_TOKEN = os.getenv("VAULT_TOKEN", "dev-only-token")

# Destino interno de cada subsistema privado.
SERVICIOS = {
    "inventory": "http://127.0.0.1:9000",
    "orders": "http://127.0.0.1:9100",
    "clients": "http://127.0.0.1:9200",
    "payments": "http://127.0.0.1:9300",
}
GRAPHQL_URL = os.getenv("GRAPHQL_URL", "http://127.0.0.1:8090")

# Tabla de rutas: (metodo, servicio, prefijo) -> nivel de acceso.
# publico   = sin token (visitante)
# cliente   = token valido de cualquier rol
# operador  = token valido con rol operador
RUTAS = [
    ("GET", "inventory", "productos", "publico"),   # catalogo publico
    ("POST", "inventory", "productos", "operador"),  # alta de producto
    ("PUT", "inventory", "productos", "operador"),
    ("DELETE", "inventory", "productos", "operador"),
    ("POST", "inventory", "reservar", "cliente"),
    ("POST", "clients", "registro", "publico"),      # registro de cliente
    ("GET", "clients", "perfil", "cliente"),
    ("GET", "orders", "pedidos", "cliente"),
    ("POST", "orders", "pedidos", "cliente"),
    ("GET", "payments", "pagos", "cliente"),
]


async def get_gateway_secrets():
    """Obtiene los secretos del gateway desde el administrador de secretos."""
    url = f"{VAULT_ADDR}/v1/secret/data/gateway"
    headers = {"X-Vault-Token": VAULT_TOKEN}
    try:
        async with httpx.AsyncClient(timeout=5.0) as client:
            response = await client.get(url, headers=headers)
    except httpx.RequestError:
        print("[GATEWAY] Secret Manager no responde", flush=True)
        raise HTTPException(status_code=500, detail="No fue posible acceder al administrador de secretos")
    if response.status_code != 200:
        print(f"[GATEWAY] Secret Manager rechazo la solicitud ({response.status_code})", flush=True)
        raise HTTPException(status_code=500, detail="Secreto del gateway no disponible")
    print("[GATEWAY] Secret Manager entrego los secretos del gateway", flush=True)
    return response.json()["data"]["data"]


async def introspectar(token: str, introspection_secret: str):
    try:
        async with httpx.AsyncClient(timeout=5.0) as client:
            response = await client.post(
                f"{AUTH_SERVICE_URL}/introspect",
                json={"token": token},
                headers={"X-Gateway-Auth-Secret": introspection_secret},
            )
    except httpx.RequestError:
        raise HTTPException(status_code=502, detail="Error consultando Auth Service")
    identidad = response.json()
    if identidad.get("active"):
        print(f"[GATEWAY] Auth Service valido el token: usuario {identidad['username']}, roles {identidad['roles']}", flush=True)
    else:
        print("[GATEWAY] Auth Service informo token invalido o expirado", flush=True)
    return identidad


def clasificar(metodo: str, servicio: str, recurso: str):
    """Devuelve el nivel de acceso de la ruta o None si no esta publicada."""
    for m, s, prefijo, nivel in RUTAS:
        if m == metodo and s == servicio and recurso.startswith(prefijo):
            return nivel
    return None


async def construir_contexto(nivel: str, credentials, secrets_gw):
    """Verifica identidad segun el nivel y arma las cabeceras internas."""
    headers = {"X-Gateway-Secret": secrets_gw["backend_shared_secret"]}
    if nivel == "publico":
        print("[GATEWAY] Ruta publica, no requiere token", flush=True)
        return headers
    if credentials is None:
        print("[GATEWAY] Ruta protegida sin token, se rechaza con 401", flush=True)
        raise HTTPException(status_code=401, detail="Bearer token requerido")
    print("[GATEWAY] Ruta protegida, se pide validacion del token al Auth Service", flush=True)
    identidad = await introspectar(credentials.credentials, secrets_gw["auth_introspection_secret"])
    if not identidad.get("active", False):
        raise HTTPException(status_code=401, detail="Token invalido o expirado")
    if nivel == "operador" and "operador" not in identidad.get("roles", []):
        print("[GATEWAY] El usuario no tiene rol operador, se rechaza con 403", flush=True)
        raise HTTPException(status_code=403, detail="Requiere rol operador")
    headers["X-Authenticated-User"] = identidad["username"]
    headers["X-Authenticated-Client"] = identidad["user_id"]
    headers["X-Authenticated-Roles"] = ",".join(identidad["roles"])
    return headers


@app.get("/health")
def health():
    return {"status": "OK", "service": "api-gateway"}


@app.post("/api/auth/login")
async def login(request: Request):
    """Login publico: el gateway firma la llamada al Auth Service con su secreto."""
    print("[GATEWAY] POST /api/auth/login, se envia al Auth Service", flush=True)
    secrets_gw = await get_gateway_secrets()
    body = await request.body()
    try:
        async with httpx.AsyncClient(timeout=5.0) as client:
            upstream = await client.post(
                f"{AUTH_SERVICE_URL}/login",
                content=body,
                headers={
                    "content-type": request.headers.get("content-type", "application/json"),
                    "X-Gateway-Auth-Secret": secrets_gw["auth_introspection_secret"],
                },
            )
    except httpx.RequestError:
        raise HTTPException(status_code=502, detail="Auth Service no disponible")
    print(f"[GATEWAY] Respuesta del Auth Service al login: {upstream.status_code}", flush=True)
    return Response(content=upstream.content, status_code=upstream.status_code,
                    media_type=upstream.headers.get("content-type", "application/json"))


@app.api_route("/api/graphql", methods=["POST"])
async def graphql_proxy(request: Request, credentials: HTTPAuthorizationCredentials = Depends(security)):
    """Las consultas GraphQL requieren cliente autenticado."""
    secrets_gw = await get_gateway_secrets()
    headers = await construir_contexto("cliente", credentials, secrets_gw)
    body = await request.body()
    headers["content-type"] = request.headers.get("content-type", "application/json")
    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            upstream = await client.post(f"{GRAPHQL_URL}/graphql", content=body, headers=headers)
    except httpx.RequestError:
        raise HTTPException(status_code=502, detail="GraphQL Gateway no disponible")
    return Response(content=upstream.content, status_code=upstream.status_code,
                    media_type=upstream.headers.get("content-type", "application/json"))


@app.api_route("/api/{servicio}/{recurso:path}", methods=["GET", "POST", "PUT", "PATCH", "DELETE"])
async def proxy(servicio: str, recurso: str, request: Request,
                credentials: HTTPAuthorizationCredentials = Depends(security)):
    destino = SERVICIOS.get(servicio)
    if destino is None:
        raise HTTPException(status_code=404, detail="Servicio no publicado")

    nivel = clasificar(request.method, servicio, recurso)
    print(f"[GATEWAY] {request.method} /api/{servicio}/{recurso}, nivel de acceso: {nivel}", flush=True)
    if nivel is None:
        raise HTTPException(status_code=404, detail="Ruta no publicada")

    secrets_gw = await get_gateway_secrets()
    headers = await construir_contexto(nivel, credentials, secrets_gw)

    content_type = request.headers.get("content-type")
    if content_type:
        headers["content-type"] = content_type
    body = await request.body()

    print(f"[GATEWAY] Enrutando a {servicio}: {destino}/{recurso}", flush=True)
    try:
        async with httpx.AsyncClient(timeout=10.0) as client:
            upstream = await client.request(
                method=request.method,
                url=f"{destino}/{recurso}",
                params=request.query_params,
                content=body,
                headers=headers,
            )
    except httpx.RequestError:
        raise HTTPException(status_code=502, detail=f"Servicio {servicio} no disponible")

    print(f"[GATEWAY] Respuesta de {servicio}: {upstream.status_code}", flush=True)
    return Response(
        content=upstream.content,
        status_code=upstream.status_code,
        media_type=upstream.headers.get("content-type", "application/json"),
    )

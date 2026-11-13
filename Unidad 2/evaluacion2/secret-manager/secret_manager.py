"""
Administrador de secretos de OfiExpress.

Emula la API KV v2 de HashiCorp Vault que se uso en clases (ruta
/v1/secret/data/gateway y cabecera X-Vault-Token). Se implementa como un
servicio propio para que la entrega sea reproducible sin instalar Vault; en
produccion este contenedor se reemplaza por HashiCorp Vault sin cambiar el
contrato que consumen el gateway, el auth-service y payments.

Levantar:
    uvicorn secret_manager:app --host 127.0.0.1 --port 8200
"""
import os

from fastapi import FastAPI, Header, HTTPException

app = FastAPI(
    title="Administrador de secretos OfiExpress",
    description="Entrega secretos autorizados a consumidores validos (compatible con Vault KV v2)."
)

# Token de acceso al gestor de secretos (en clases: dev-only-token).
VAULT_TOKEN = os.getenv("VAULT_TOKEN", "dev-only-token")

# Bodega de secretos. Son valores de desarrollo; su rotacion se documenta en el
# informe. Nunca se publican en el codigo del gateway ni llegan al navegador.
SECRETOS = {
    "gateway": {
        "auth_introspection_secret": os.getenv("AUTH_INTROSPECTION_SECRET", "gateway-auth-secret-789"),
        "backend_shared_secret": os.getenv("BACKEND_SHARED_SECRET", "gateway-backend-secret-456"),
    },
    "payments": {
        "payment_provider_api_key": os.getenv("PAYMENT_PROVIDER_API_KEY", "pay-provider-key-321"),
    },
}


@app.get("/v1/secret/data/{nombre}")
def leer_secreto(nombre: str, x_vault_token: str = Header(default="")):
    """Devuelve el secreto solicitado si el token de acceso es valido."""
    if x_vault_token != VAULT_TOKEN:
        raise HTTPException(status_code=403, detail="Token de secretos invalido")
    datos = SECRETOS.get(nombre)
    if datos is None:
        raise HTTPException(status_code=404, detail="Secreto no encontrado")
    # Estructura identica a Vault KV v2: data.data contiene el mapa de secretos.
    return {"data": {"data": datos}}


@app.get("/health")
def health():
    return {"status": "OK", "service": "secret-manager"}

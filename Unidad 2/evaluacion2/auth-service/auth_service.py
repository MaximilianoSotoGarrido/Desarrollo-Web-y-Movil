"""
Auth Service de OfiExpress.

Autentica usuarios, emite tokens opacos de corta vida y los valida por
introspeccion. Solo responde a quien presente el secreto compartido con el
gateway (cabecera X-Gateway-Auth-Secret), de modo que el navegador nunca puede
llamar directamente a este servicio.

Levantar:
    uvicorn auth_service:app --host 127.0.0.1 --port 8100
"""
from datetime import datetime, timedelta, timezone
import os
import secrets

from fastapi import FastAPI, HTTPException, Header
from pydantic import BaseModel

app = FastAPI(
    title="Auth Service OfiExpress",
    description="Autenticacion de usuarios e introspeccion de tokens de sesion."
)

# Usuarios de prueba (datos ficticios). En el caso real residen en dbIdentity.
USERS = {
    "ana": {"password": "1234", "user_id": "USR-001", "roles": ["cliente"]},
    "pedro": {"password": "5678", "user_id": "USR-002", "roles": ["cliente"]},
    "carla": {"password": "oper123", "user_id": "USR-003", "roles": ["operador"]},
}

# Sesiones vigentes en memoria (token -> datos de sesion).
SESSIONS = {}

TOKEN_LIFETIME_MINUTES = 15

# Secreto compartido con el gateway; se obtiene del administrador de secretos.
AUTH_INTROSPECTION_SECRET = os.getenv("AUTH_INTROSPECTION_SECRET", "gateway-auth-secret-789")


class LoginRequest(BaseModel):
    username: str
    password: str


class IntrospectionRequest(BaseModel):
    token: str


def verificar_gateway(x_gateway_auth_secret: str):
    """Rechaza cualquier llamada que no provenga del gateway autorizado."""
    if not secrets.compare_digest(x_gateway_auth_secret, AUTH_INTROSPECTION_SECRET):
        raise HTTPException(status_code=403, detail="Gateway no autorizado")


@app.post("/login")
def login(request: LoginRequest, x_gateway_auth_secret: str = Header(default="")):
    verificar_gateway(x_gateway_auth_secret)
    user = USERS.get(request.username)
    if user is None or user["password"] != request.password:
        # Mismo mensaje para usuario inexistente o clave incorrecta.
        raise HTTPException(status_code=401, detail="Credenciales incorrectas")
    access_token = secrets.token_urlsafe(32)
    expira = datetime.now(timezone.utc) + timedelta(minutes=TOKEN_LIFETIME_MINUTES)
    SESSIONS[access_token] = {
        "user_id": user["user_id"],
        "username": request.username,
        "roles": user["roles"],
        "expires_at": expira,
    }
    return {
        "access_token": access_token,
        "token_type": "bearer",
        "expires_in": TOKEN_LIFETIME_MINUTES * 60,
    }


@app.post("/introspect")
def introspect(request: IntrospectionRequest, x_gateway_auth_secret: str = Header(default="")):
    verificar_gateway(x_gateway_auth_secret)
    session = SESSIONS.get(request.token)
    if session is None:
        return {"active": False}
    if datetime.now(timezone.utc) > session["expires_at"]:
        SESSIONS.pop(request.token, None)
        return {"active": False}
    return {
        "active": True,
        "user_id": session["user_id"],
        "username": session["username"],
        "roles": session["roles"],
        "expires_at": session["expires_at"].isoformat(),
    }


@app.post("/logout")
def logout(request: IntrospectionRequest, x_gateway_auth_secret: str = Header(default="")):
    verificar_gateway(x_gateway_auth_secret)
    SESSIONS.pop(request.token, None)
    return {"message": "Sesion finalizada"}


@app.get("/health")
def health():
    return {"status": "OK", "service": "auth-service"}

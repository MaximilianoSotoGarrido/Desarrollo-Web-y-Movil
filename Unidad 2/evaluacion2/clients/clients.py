"""
Servicio privado clients de OfiExpress.

Autoridad sobre los perfiles de los clientes. No guarda contrasenas: las
credenciales viven en el Auth Service. Solo acepta llamadas firmadas por el
gateway (X-Gateway-Secret) y el perfil solo se entrega al titular, identificado
por la cabecera de identidad verificada que agrega el gateway.

Levantar:
    uvicorn clients:app --host 127.0.0.1 --port 9200
"""
import os
import secrets as _s

from fastapi import FastAPI, Header, HTTPException, Depends
from pydantic import BaseModel, Field, EmailStr
from motor.motor_asyncio import AsyncIOMotorClient
from contextlib import asynccontextmanager

MONGODB_URI = os.getenv("MONGODB_URI", "mongodb://localhost:27017")
DB_NAME = "ofiexpress_clients"
INTERNAL_GATEWAY_SECRET = os.getenv("INTERNAL_GATEWAY_SECRET", "gateway-backend-secret-456")

client: AsyncIOMotorClient | None = None
coll = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    global client, coll
    client = AsyncIOMotorClient(MONGODB_URI)
    coll = client[DB_NAME]["clientes"]
    await coll.create_index("user_id", unique=True)
    yield
    client.close()


app = FastAPI(title="clients - OfiExpress", description="Perfiles de clientes de OfiExpress.", lifespan=lifespan)


def verify_gateway(x_gateway_secret: str = Header(default="")):
    if not _s.compare_digest(x_gateway_secret, INTERNAL_GATEWAY_SECRET):
        raise HTTPException(status_code=403, detail="Solicitud no autorizada; falta firma del gateway")


class RegistroIn(BaseModel):
    user_id: str = Field(description="Identificador de identidad emitido por el Auth Service")
    nombre: str = Field(min_length=1)
    email: EmailStr
    direccion: str = Field(min_length=1, description="Direccion de despacho")


@app.get("/health", dependencies=[Depends(verify_gateway)])
def health():
    return {"status": "OK", "service": "clients"}


@app.post("/registro", status_code=201, dependencies=[Depends(verify_gateway)])
async def registrar(perfil: RegistroIn):
    """Registro de cliente. Ruta publica: no exige sesion previa."""
    if await coll.find_one({"user_id": perfil.user_id}):
        raise HTTPException(status_code=409, detail="El cliente ya esta registrado")
    await coll.insert_one(perfil.model_dump())
    return {"user_id": perfil.user_id, "nombre": perfil.nombre, "mensaje": "Cliente registrado"}


@app.get("/perfil", dependencies=[Depends(verify_gateway)])
async def mi_perfil(x_authenticated_client: str | None = Header(default=None)):
    """Entrega solo el perfil del titular autenticado; no permite consultar otros."""
    if not x_authenticated_client:
        raise HTTPException(status_code=401, detail="Identidad requerida")
    doc = await coll.find_one({"user_id": x_authenticated_client})
    if not doc:
        raise HTTPException(status_code=404, detail="Perfil no encontrado")
    return {"user_id": doc["user_id"], "nombre": doc["nombre"], "email": doc["email"],
            "direccion": doc["direccion"]}

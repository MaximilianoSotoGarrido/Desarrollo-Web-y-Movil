"""
Servicio privado payments de OfiExpress.

Autoridad sobre el estado de los pagos. Coordina el cobro con la pasarela de
pago externa usando una credencial que obtiene del administrador de secretos
(nunca incrustada en el codigo ni enviada al navegador). La confirmacion del
pago es idempotente por pedido, para no cobrar dos veces ante un reintento.

Levantar:
    uvicorn payments:app --host 127.0.0.1 --port 9300
"""
import os
import secrets as _s

import httpx
from fastapi import FastAPI, Header, HTTPException, Depends
from pydantic import BaseModel, Field
from motor.motor_asyncio import AsyncIOMotorClient
from contextlib import asynccontextmanager

MONGODB_URI = os.getenv("MONGODB_URI", "mongodb://localhost:27017")
DB_NAME = "ofiexpress_payments"
INTERNAL_GATEWAY_SECRET = os.getenv("INTERNAL_GATEWAY_SECRET", "gateway-backend-secret-456")
VAULT_ADDR = os.getenv("VAULT_ADDR", "http://127.0.0.1:8200")
VAULT_TOKEN = os.getenv("VAULT_TOKEN", "dev-only-token")

client: AsyncIOMotorClient | None = None
coll = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    global client, coll
    client = AsyncIOMotorClient(MONGODB_URI)
    coll = client[DB_NAME]["pagos"]
    await coll.create_index("order_id", unique=True)
    yield
    client.close()


app = FastAPI(title="payments - OfiExpress", description="Estados de pago y cobro externo.", lifespan=lifespan)


def verify_gateway(x_gateway_secret: str = Header(default="")):
    if not _s.compare_digest(x_gateway_secret, INTERNAL_GATEWAY_SECRET):
        raise HTTPException(status_code=403, detail="Solicitud no autorizada; falta firma del gateway")


class PagoIn(BaseModel):
    order_id: str = Field(description="Pedido asociado al cobro")
    monto: int = Field(gt=0, description="Monto a cobrar en CLP")


async def obtener_api_key_pasarela() -> str:
    """Obtiene la credencial de la pasarela externa desde el administrador de secretos."""
    url = f"{VAULT_ADDR}/v1/secret/data/payments"
    try:
        async with httpx.AsyncClient(timeout=5.0) as c:
            r = await c.get(url, headers={"X-Vault-Token": VAULT_TOKEN})
    except httpx.RequestError:
        raise HTTPException(status_code=500, detail="No fue posible acceder al administrador de secretos")
    if r.status_code != 200:
        raise HTTPException(status_code=500, detail="Credencial de pagos no disponible")
    return r.json()["data"]["data"]["payment_provider_api_key"]


def cobrar_en_pasarela(api_key: str, order_id: str, monto: int) -> dict:
    """Simula el cobro en la pasarela externa (Servipag/transferencia).

    En produccion aqui iria la llamada HTTPS real a la pasarela usando api_key.
    Para la entrega se simula una aprobacion deterministica.
    """
    aprobado = bool(api_key) and monto > 0
    return {"aprobado": aprobado, "referencia_externa": f"EXT-{order_id}"}


@app.get("/health", dependencies=[Depends(verify_gateway)])
def health():
    return {"status": "OK", "service": "payments"}


@app.post("/pagos", dependencies=[Depends(verify_gateway)])
async def procesar_pago(pago: PagoIn):
    """Procesa el cobro de un pedido. Idempotente: un order_id se cobra una vez."""
    existente = await coll.find_one({"order_id": pago.order_id})
    if existente:
        # Reintento: devuelve el resultado ya registrado sin volver a cobrar.
        return {"order_id": existente["order_id"], "estado": existente["estado"],
                "payment_id": existente["payment_id"], "reintento": True}
    api_key = await obtener_api_key_pasarela()
    resultado = cobrar_en_pasarela(api_key, pago.order_id, pago.monto)
    estado = "aprobado" if resultado["aprobado"] else "rechazado"
    payment_id = f"PAY-{pago.order_id}"
    await coll.insert_one({
        "order_id": pago.order_id,
        "payment_id": payment_id,
        "monto": pago.monto,
        "estado": estado,
        "referencia_externa": resultado["referencia_externa"],
    })
    return {"order_id": pago.order_id, "estado": estado, "payment_id": payment_id, "reintento": False}


@app.get("/pagos/{order_id}", dependencies=[Depends(verify_gateway)])
async def consultar_pago(order_id: str):
    doc = await coll.find_one({"order_id": order_id})
    if not doc:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    return {"order_id": doc["order_id"], "estado": doc["estado"], "payment_id": doc["payment_id"],
            "monto": doc["monto"]}

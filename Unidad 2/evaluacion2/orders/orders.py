"""
Servicio privado orders de OfiExpress.

Autoridad sobre el ciclo de vida de los pedidos. Decide el estado oficial del
pedido, reserva stock en inventory y solicita el cobro en payments. Protege sus
operaciones con idempotencia (un reintento con la misma clave no crea un segundo
pedido) y con autorizacion por pertenencia (un cliente solo ve sus pedidos).

Levantar:
    uvicorn orders:app --host 127.0.0.1 --port 9100
"""
import os
import secrets as _s
import uuid
from typing import List

import httpx
from fastapi import FastAPI, Header, HTTPException, Depends
from pydantic import BaseModel, Field
from motor.motor_asyncio import AsyncIOMotorClient
from contextlib import asynccontextmanager

MONGODB_URI = os.getenv("MONGODB_URI", "mongodb://localhost:27017")
DB_NAME = "ofiexpress_orders"
INTERNAL_GATEWAY_SECRET = os.getenv("INTERNAL_GATEWAY_SECRET", "gateway-backend-secret-456")
INVENTORY_URL = os.getenv("INVENTORY_URL", "http://127.0.0.1:9000")
PAYMENTS_URL = os.getenv("PAYMENTS_URL", "http://127.0.0.1:9300")

client: AsyncIOMotorClient | None = None
coll = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    global client, coll
    client = AsyncIOMotorClient(MONGODB_URI)
    coll = client[DB_NAME]["pedidos"]
    await coll.create_index([("user_id", 1), ("idempotency_key", 1)], unique=True)
    yield
    client.close()


app = FastAPI(title="orders - OfiExpress", description="Ciclo de vida de los pedidos.", lifespan=lifespan)

# Cabecera firmada para llamar a otros servicios privados.
FIRMA = {"X-Gateway-Secret": INTERNAL_GATEWAY_SECRET}


def verify_gateway(x_gateway_secret: str = Header(default="")):
    if not _s.compare_digest(x_gateway_secret, INTERNAL_GATEWAY_SECRET):
        raise HTTPException(status_code=403, detail="Solicitud no autorizada; falta firma del gateway")


class LineaIn(BaseModel):
    sku: str
    cantidad: int = Field(gt=0)


class PedidoIn(BaseModel):
    items: List[LineaIn] = Field(min_length=1)
    idempotency_key: str = Field(min_length=6, description="Clave para evitar pedidos duplicados")


def doc_to_pedido(doc):
    return {"order_id": doc["order_id"], "user_id": doc["user_id"], "items": doc["items"],
            "total": doc["total"], "estado": doc["estado"]}


@app.get("/health", dependencies=[Depends(verify_gateway)])
def health():
    return {"status": "OK", "service": "orders"}


@app.post("/pedidos", dependencies=[Depends(verify_gateway)])
async def crear_pedido(pedido: PedidoIn, x_authenticated_client: str | None = Header(default=None)):
    if not x_authenticated_client:
        raise HTTPException(status_code=401, detail="Identidad requerida")

    # 1) Idempotencia: si ya existe (titular + clave), devuelve el pedido previo.
    previo = await coll.find_one({"user_id": x_authenticated_client, "idempotency_key": pedido.idempotency_key})
    if previo:
        return {**doc_to_pedido(previo), "reintento": True}

    # 2) Reserva de stock en inventory con compensacion ante fallo parcial.
    reservados = []
    total = 0
    detalle = []
    async with httpx.AsyncClient(timeout=10.0) as c:
        try:
            for linea in pedido.items:
                r = await c.post(f"{INVENTORY_URL}/reservar",
                                 json={"sku": linea.sku, "cantidad": linea.cantidad}, headers=FIRMA)
                if r.status_code != 200:
                    raise HTTPException(status_code=409,
                                        detail=f"No se pudo reservar {linea.sku}: {r.json().get('detail')}")
                data = r.json()
                reservados.append(linea)
                total += data["precio_unitario"] * linea.cantidad
                detalle.append({"sku": linea.sku, "cantidad": linea.cantidad,
                                "precio_unitario": data["precio_unitario"]})
        except HTTPException:
            # Compensa las reservas ya realizadas antes de abortar.
            for linea in reservados:
                await c.post(f"{INVENTORY_URL}/liberar",
                             json={"sku": linea.sku, "cantidad": linea.cantidad}, headers=FIRMA)
            raise

        # 3) Cobro en payments (idempotente por order_id).
        order_id = "ORD-" + uuid.uuid4().hex[:8].upper()
        try:
            rp = await c.post(f"{PAYMENTS_URL}/pagos",
                              json={"order_id": order_id, "monto": total}, headers=FIRMA)
        except httpx.RequestError:
            for linea in reservados:
                await c.post(f"{INVENTORY_URL}/liberar",
                             json={"sku": linea.sku, "cantidad": linea.cantidad}, headers=FIRMA)
            raise HTTPException(status_code=502, detail="Servicio de pagos no disponible")

    estado_pago = rp.json().get("estado", "rechazado")
    estado = "pagado" if estado_pago == "aprobado" else "pendiente_pago"

    # 4) Persiste el pedido con su clave de idempotencia.
    documento = {
        "order_id": order_id,
        "user_id": x_authenticated_client,
        "idempotency_key": pedido.idempotency_key,
        "items": detalle,
        "total": total,
        "estado": estado,
    }
    await coll.insert_one(documento)
    return {**doc_to_pedido(documento), "reintento": False}


@app.get("/pedidos", dependencies=[Depends(verify_gateway)])
async def listar_pedidos(x_authenticated_client: str | None = Header(default=None)):
    """Lista solo los pedidos del titular autenticado."""
    if not x_authenticated_client:
        raise HTTPException(status_code=401, detail="Identidad requerida")
    items = [doc_to_pedido(d) async for d in coll.find({"user_id": x_authenticated_client})]
    return {"pedidos": items}


@app.get("/pedidos/{order_id}", dependencies=[Depends(verify_gateway)])
async def obtener_pedido(order_id: str, x_authenticated_client: str | None = Header(default=None)):
    """Devuelve el pedido solo si pertenece al titular; si no, no revela el recurso ajeno."""
    if not x_authenticated_client:
        raise HTTPException(status_code=401, detail="Identidad requerida")
    doc = await coll.find_one({"order_id": order_id, "user_id": x_authenticated_client})
    if not doc:
        # Mismo 404 para inexistente y para ajeno: no confirma la existencia de pedidos de otros.
        raise HTTPException(status_code=404, detail="Pedido no encontrado")
    return doc_to_pedido(doc)

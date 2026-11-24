"""
Servicio privado inventory de OfiExpress.

Autoridad sobre el catalogo y la disponibilidad de utiles de oficina y
escolares. Solo acepta llamadas firmadas por el gateway o por otro servicio
interno (cabecera X-Gateway-Secret). Persiste en su propia base de datos
(ofiexpress_inventory), que ningun otro servicio modifica.

Levantar:
    uvicorn inventory:app --host 127.0.0.1 --port 9000
"""
import os
from typing import List

from fastapi import FastAPI, Header, HTTPException, Depends
from pydantic import BaseModel, Field
from motor.motor_asyncio import AsyncIOMotorClient
from contextlib import asynccontextmanager

MONGODB_URI = os.getenv("MONGODB_URI", "mongodb://localhost:27017")
DB_NAME = "ofiexpress_inventory"
INTERNAL_GATEWAY_SECRET = os.getenv("INTERNAL_GATEWAY_SECRET", "gateway-backend-secret-456")

client: AsyncIOMotorClient | None = None
coll = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    global client, coll
    client = AsyncIOMotorClient(MONGODB_URI)
    coll = client[DB_NAME]["productos"]
    await coll.create_index("sku", unique=True)
    yield
    client.close()


app = FastAPI(title="inventory - OfiExpress", description="Catalogo y disponibilidad de productos.", lifespan=lifespan)


def verify_gateway(x_gateway_secret: str = Header(default="")):
    import secrets as _s
    if not _s.compare_digest(x_gateway_secret, INTERNAL_GATEWAY_SECRET):
        print("[INVENTORY] Firma del gateway ausente o invalida, llamada rechazada con 403", flush=True)
        raise HTTPException(status_code=403, detail="Solicitud no autorizada; falta firma del gateway")
    print("[INVENTORY] Firma del gateway valida, llamada permitida", flush=True)


class ProductoIn(BaseModel):
    sku: str = Field(min_length=3, description="Codigo de producto, ej: SKU-001")
    nombre: str = Field(min_length=1)
    precio: int = Field(gt=0, description="Precio unitario en CLP")
    stock: int = Field(ge=0, description="Unidades disponibles")
    categoria: str = Field(default="general")


class ReservaIn(BaseModel):
    sku: str
    cantidad: int = Field(gt=0)


def doc_to_public(doc):
    return {"sku": doc["sku"], "nombre": doc["nombre"], "precio": doc["precio"],
            "stock": doc["stock"], "categoria": doc.get("categoria", "general"),
            "descripcion": doc.get("descripcion", ""), "icono": doc.get("icono", "fa-cube")}


@app.get("/health", dependencies=[Depends(verify_gateway)])
def health():
    return {"status": "OK", "service": "inventory"}


@app.get("/productos", dependencies=[Depends(verify_gateway)])
async def listar_productos():
    """Catalogo publico: solo campos permitidos del catalogo."""
    items = [doc_to_public(d) async for d in coll.find({})]
    print(f"[INVENTORY] MongoDB ofiexpress_inventory.productos: consulta del catalogo, {len(items)} productos", flush=True)
    return {"productos": items}


@app.get("/productos/{sku}", dependencies=[Depends(verify_gateway)])
async def obtener_producto(sku: str):
    doc = await coll.find_one({"sku": sku})
    if not doc:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return doc_to_public(doc)


@app.post("/productos", status_code=201, dependencies=[Depends(verify_gateway)])
async def crear_producto(producto: ProductoIn):
    if await coll.find_one({"sku": producto.sku}):
        raise HTTPException(status_code=409, detail="El SKU ya existe")
    await coll.insert_one(producto.model_dump())
    return doc_to_public(producto.model_dump())


@app.put("/productos/{sku}", dependencies=[Depends(verify_gateway)])
async def actualizar_producto(sku: str, producto: ProductoIn):
    res = await coll.update_one({"sku": sku}, {"$set": producto.model_dump()})
    if res.matched_count == 0:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return doc_to_public(producto.model_dump())


@app.delete("/productos/{sku}", status_code=204, dependencies=[Depends(verify_gateway)])
async def eliminar_producto(sku: str):
    res = await coll.delete_one({"sku": sku})
    if res.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return None


@app.post("/reservar", dependencies=[Depends(verify_gateway)])
async def reservar_stock(reserva: ReservaIn):
    """Descuenta stock de forma condicional; lo invoca el servicio orders."""
    doc = await coll.find_one({"sku": reserva.sku})
    if not doc:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    # Descuento atomico solo si hay stock suficiente.
    res = await coll.update_one(
        {"sku": reserva.sku, "stock": {"$gte": reserva.cantidad}},
        {"$inc": {"stock": -reserva.cantidad}},
    )
    if res.modified_count == 0:
        print(f"[INVENTORY] MongoDB ofiexpress_inventory.productos: stock insuficiente para {reserva.sku}", flush=True)
        raise HTTPException(status_code=409, detail="Stock insuficiente")
    print(f"[INVENTORY] MongoDB ofiexpress_inventory.productos: stock de {reserva.sku} descontado en {reserva.cantidad}", flush=True)
    return {"sku": reserva.sku, "reservado": reserva.cantidad, "precio_unitario": doc["precio"]}


@app.post("/liberar", dependencies=[Depends(verify_gateway)])
async def liberar_stock(reserva: ReservaIn):
    """Devuelve stock reservado; compensa una reserva previa que no se concreto."""
    res = await coll.update_one({"sku": reserva.sku}, {"$inc": {"stock": reserva.cantidad}})
    if res.matched_count == 0:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    print(f"[INVENTORY] MongoDB ofiexpress_inventory.productos: stock de {reserva.sku} devuelto en {reserva.cantidad}", flush=True)
    return {"sku": reserva.sku, "liberado": reserva.cantidad}

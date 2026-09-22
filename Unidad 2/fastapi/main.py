from typing import List, Optional
from fastapi import FastAPI, HTTPException, Query
from pydantic import BaseModel, Field
from motor.motor_asyncio import AsyncIOMotorClient
from bson import ObjectId
from contextlib import asynccontextmanager

# Configuracion de la Base de Datos MongoDB
# Visto en clases de la Semana 4: Uso de Motor para I/O asincrono con MongoDB
MONGODB_URI = "mongodb://localhost:27017"
DB_NAME = "ofiexpress_db"
COLL_NAME = "productos"

client: AsyncIOMotorClient | None = None
db = None
coll = None

# Manejo del ciclo de vida asincrono del servidor FastAPI (Lifespan - Visto en Semana 4)
@asynccontextmanager
async def lifespan(app: FastAPI):
    global client, db, coll
    client = AsyncIOMotorClient(MONGODB_URI)
    db = client[DB_NAME]
    coll = db[COLL_NAME]
    yield
    client.close()

app = FastAPI(
    title="API Backend OfiExpress - Semana 4",
    description="API REST de catalogo de productos de papeleria y utiles escolares (Caso 37)",
    version="1.0.0",
    lifespan=lifespan
)

# Modelo Pydantic de entrada de datos (Creacion / Actualizacion)
class ItemIn(BaseModel):
    nombre: str = Field(min_length=1, description="Nombre del producto escolar u oficina")
    precio: float = Field(gt=0, description="Precio unitario mayor a 0")
    tags: List[str] = Field(default_factory=list, description="Etiquetas de categoria, ej: papeleria, escritura")
    activo: bool = Field(default=True, description="Estado de disponibilidad en catalogo")

# Modelo Pydantic base
class Item(BaseModel):
    nombre: str = Field(min_length=1, description="Nombre del producto escolar u oficina")
    precio: float = Field(gt=0, description="Precio unitario mayor a 0")
    tags: List[str] = Field(default_factory=list)
    activo: bool = True

# Modelo Pydantic de salida con ID mapeado desde MongoDB BSON
class ItemOut(Item):
    id: str

# Funcion auxiliar de transformacion BSON -> ItemOut (Semana 4)
def doc_to_itemout(doc) -> ItemOut:
    return ItemOut(
        id=str(doc["_id"]),
        nombre=doc["nombre"],
        precio=doc["precio"],
        tags=doc.get("tags", []),
        activo=doc.get("activo", True)
    )

# --- ENDPOINTS REST ---

@app.get("/health", tags=["sistema"])
def health():
    """Endpoint de estado del servidor"""
    return {"status": "ok", "servicio": "OfiExpress Backend"}

@app.get("/items", response_model=List[ItemOut], tags=["productos"])
async def listar_items(
    q: Optional[str] = Query(None, description="Filtro por nombre que contenga q"),
    skip: int = Query(0, ge=0, description="Paginacion: registros a saltar"),
    limit: int = Query(50, ge=1, le=200, description="Paginacion: limite de registros"),
):
    """Consulta masiva de productos con filtro y paginacion"""
    query = {}
    if q:
        query["nombre"] = {"$regex": q, "$options": "i"}
    cursor = coll.find(query).skip(skip).limit(limit)
    items: List[ItemOut] = []
    async for doc in cursor:
        items.append(doc_to_itemout(doc))
    return items

@app.post("/items", response_model=ItemOut, status_code=201, tags=["productos"])
async def crear_item(item: ItemIn):
    """Creacion de un nuevo producto en la coleccion de MongoDB"""
    res = await coll.insert_one(item.model_dump())
    doc = await coll.find_one({"_id": res.inserted_id})
    return doc_to_itemout(doc)

@app.get("/items/{item_id}", response_model=ItemOut, status_code=200, tags=["productos"])
async def obtener_item(item_id: str):
    """Obtencion de un producto especifico por ObjectId"""
    if not ObjectId.is_valid(item_id):
        raise HTTPException(status_code=400, detail="ID invalido")
    doc = await coll.find_one({"_id": ObjectId(item_id)})
    if not doc:
        raise HTTPException(status_code=404, detail="Item no encontrado")
    return doc_to_itemout(doc)

@app.put("/items/{item_id}", response_model=ItemOut, tags=["productos"])
async def actualizar_item(item_id: str, item: ItemIn):
    """Actualizacion de un producto por ObjectId"""
    if not ObjectId.is_valid(item_id):
        raise HTTPException(status_code=400, detail="ID invalido")
    res = await coll.update_one(
        {"_id": ObjectId(item_id)},
        {"$set": item.model_dump()}
    )
    if res.matched_count == 0:
        raise HTTPException(status_code=404, detail="Item no encontrado")
    doc = await coll.find_one({"_id": ObjectId(item_id)})
    return doc_to_itemout(doc)

@app.delete("/items/{item_id}", status_code=204, tags=["productos"])
async def eliminar_item(item_id: str):
    """Eliminacion de un producto por ObjectId"""
    if not ObjectId.is_valid(item_id):
        raise HTTPException(status_code=400, detail="ID invalido")
    res = await coll.delete_one({"_id": ObjectId(item_id)})
    if res.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Item no encontrado")
    return None

from fastapi import FastAPI
import httpx

app = FastAPI(
    title="Local API Gateway - OfiExpress",
    description="API Gateway local para enrutamiento de microservicios de papeleria (Caso 37)"
)

BACKEND_URL = "http://localhost:9000"
BACKEND_URL2 = "http://localhost:9100"

@app.get("/api/products")
async def products():
    """Enruta peticiones de productos al Backend API 1 (ingles/catalogo general)"""
    async with httpx.AsyncClient() as client:
        response = await client.get(f"{BACKEND_URL}/products")
    return response.json()

@app.get("/api/productos")
async def productos():
    """Enruta peticiones de productos al Backend API 2 (español/sucursal)"""
    async with httpx.AsyncClient() as client:
        response = await client.get(f"{BACKEND_URL2}/productos")
    return response.json()

@app.get("/api/orders")
async def orders():
    """Enruta peticiones de ordenes de compra al Backend API 1"""
    async with httpx.AsyncClient() as client:
        response = await client.get(f"{BACKEND_URL}/orders")
    return response.json()

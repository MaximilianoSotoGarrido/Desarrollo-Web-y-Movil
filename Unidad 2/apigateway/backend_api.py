from fastapi import FastAPI

app = FastAPI(
    title="Backend API OfiExpress - Catalogo",
    description="API REST de productos de papeleria y utiles escolares enrutada por API Gateway (Caso 37)"
)

@app.get("/health")
def health():
    """Endpoint de estado del servicio backend 1"""
    return {"status": "OK", "service": "Backend API OfiExpress 1"}

@app.get("/products")
def products():
    """Retorna listado de productos de papeleria"""
    return {
        "products": [
            {"id": 1, "name": "Cuaderno Universitario 100 Hojas Matte", "price": 1890},
            {"id": 2, "name": "Set de Lapices de Colores 12 Unidades", "price": 2490}
        ]
    }

@app.get("/orders")
def orders():
    """Retorna ordenes de compra de utiles de oficina"""
    return {
        "orders": [
            {"id": 1001, "status": "paid"},
            {"id": 1002, "status": "pending"}
        ]
    }

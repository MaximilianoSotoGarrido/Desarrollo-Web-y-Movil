from fastapi import FastAPI

app = FastAPI(
    title="Backend API OfiExpress - Sucursal",
    description="API REST secundaria en español para enrutamiento por API Gateway (Caso 37)"
)

@app.get("/health")
def health():
    """Endpoint de estado del servicio backend 2"""
    return {"status": "OK", "service": "Backend API OfiExpress 2"}

@app.get("/productos")
def productos():
    """Retorna listado de productos escolares y papeleria"""
    return {
        "productos": [
            {"id": 1, "nombre": "Resma de Papel Carta 500 Hojas", "precio": 4290},
            {"id": 2, "nombre": "Corchetera de Escritorio Metalica", "precio": 3190}
        ]
    }

@app.get("/ordenes")
def ordenes():
    """Retorna ordenes de pedidos de papeleria"""
    return {
        "ordenes": [
            {"id": 1001, "status": "paid"},
            {"id": 1002, "status": "pending"}
        ]
    }

"""
Carga de datos de prueba de OfiExpress.

Inserta en MongoDB el catalogo (datos tomados del frontend del caso 37) y los
perfiles de cliente que corresponden a los usuarios del Auth Service. Cada
servicio tiene su propia base de datos; este script respeta esa separacion.

Ejecutar (con los servicios detenidos o encendidos, da igual):
    python seed.py
"""
from pymongo import MongoClient

MONGODB_URI = "mongodb://localhost:27017"

# Catalogo de OfiExpress (mismos productos, precios, descripciones e iconos del frontend, caso 37).
PRODUCTOS = [
    {"sku": "SKU-001", "nombre": "Cuaderno Universitario", "precio": 1890, "stock": 50, "categoria": "escolar",
     "descripcion": "100 Hojas matemática 7mm tapa dura", "icono": "fa-book"},
    {"sku": "SKU-002", "nombre": "Resma Papel Carta", "precio": 4290, "stock": 40, "categoria": "oficina",
     "descripcion": "500 Hojas 75g multicopia", "icono": "fa-file-text-o"},
    {"sku": "SKU-003", "nombre": "Set Lápices Pasta", "precio": 2490, "stock": 60, "categoria": "escritura",
     "descripcion": "Pack 10 unidades azul/negro/rojo", "icono": "fa-pencil"},
    {"sku": "SKU-004", "nombre": "Corchetera Escritorio", "precio": 3190, "stock": 30, "categoria": "oficina",
     "descripcion": "Metálica ergonómica para 25 hojas", "icono": "fa-scissors"},
    {"sku": "SKU-005", "nombre": "Archivador Tamaño Oficio", "precio": 2890, "stock": 35, "categoria": "oficina",
     "descripcion": "Lomo ancho reforzado para oficina", "icono": "fa-folder-open"},
    {"sku": "SKU-006", "nombre": "Calculadora Científica", "precio": 8990, "stock": 20, "categoria": "escolar",
     "descripcion": "240 Funciones para estudiantes y profesionales", "icono": "fa-calculator"},
]

# Perfiles de cliente; user_id coincide con los usuarios del Auth Service.
CLIENTES = [
    {"user_id": "USR-001", "nombre": "Ana Rivas", "email": "ana@correo.cl", "direccion": "Av. Maipu 1234, Maipu"},
    {"user_id": "USR-002", "nombre": "Pedro Soto", "email": "pedro@correo.cl", "direccion": "Los Olmos 456, Maipu"},
]


def main():
    cliente = MongoClient(MONGODB_URI)

    inv = cliente["ofiexpress_inventory"]["productos"]
    inv.delete_many({})
    inv.create_index("sku", unique=True)
    inv.insert_many([dict(p) for p in PRODUCTOS])
    print(f"inventory: {inv.count_documents({})} productos cargados")

    cli = cliente["ofiexpress_clients"]["clientes"]
    cli.delete_many({})
    cli.create_index("user_id", unique=True)
    cli.insert_many([dict(c) for c in CLIENTES])
    print(f"clients: {cli.count_documents({})} perfiles cargados")

    # Limpia pedidos y pagos de corridas anteriores para dejar un estado conocido.
    cliente["ofiexpress_orders"]["pedidos"].delete_many({})
    cliente["ofiexpress_payments"]["pagos"].delete_many({})
    print("orders y payments: colecciones vaciadas")

    cliente.close()
    print("Datos de prueba cargados.")


if __name__ == "__main__":
    main()

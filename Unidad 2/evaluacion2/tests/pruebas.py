"""
Pruebas de integracion de OfiExpress contra el API Gateway.

Ejecuta los flujos principales (catalogo publico, autenticacion, pedido con
idempotencia, autorizacion por pertenencia, control de rol y agregacion GraphQL)
e imprime PASS/FAIL por escenario. Sirve como evidencia reproducible.

Requisitos: todos los servicios levantados y `python seed.py` ejecutado.
Ejecutar:
    python tests/pruebas.py
"""
import httpx

GW = "http://127.0.0.1:8000"
resultados = []


def check(nombre, condicion, detalle=""):
    estado = "PASS" if condicion else "FALLA"
    resultados.append(condicion)
    print(f"[{estado}] {nombre}" + (f" -> {detalle}" if detalle else ""))


def login(usuario, clave):
    r = httpx.post(f"{GW}/api/auth/login", json={"username": usuario, "password": clave}, timeout=10)
    return r


def bearer(token):
    return {"Authorization": f"Bearer {token}"}


def main():
    cli = httpx.Client(timeout=10)

    # F01 - Catalogo publico sin token.
    r = cli.get(f"{GW}/api/inventory/productos")
    check("F01 catalogo publico sin token", r.status_code == 200 and len(r.json()["productos"]) == 6,
          f"status={r.status_code}")

    # Ruta protegida sin token.
    r = cli.get(f"{GW}/api/orders/pedidos")
    check("Ruta protegida sin token rechazada", r.status_code == 401, f"status={r.status_code}")

    # Login correcto e incorrecto.
    r = login("ana", "1234")
    check("Login correcto (ana)", r.status_code == 200, f"status={r.status_code}")
    token_ana = r.json().get("access_token", "")
    rbad = login("ana", "clave-mala")
    check("Login con clave incorrecta rechazado", rbad.status_code == 401, f"status={rbad.status_code}")

    # Perfil propio.
    r = cli.get(f"{GW}/api/clients/perfil", headers=bearer(token_ana))
    check("Perfil propio con token", r.status_code == 200 and r.json()["user_id"] == "USR-001",
          f"status={r.status_code}")

    # F02 - Crear pedido (SKU-001 x2 = 3780).
    pedido = {"items": [{"sku": "SKU-001", "cantidad": 2}], "idempotency_key": "ana-key-0001"}
    r = cli.post(f"{GW}/api/orders/pedidos", json=pedido, headers=bearer(token_ana))
    ok = r.status_code == 200 and r.json()["total"] == 3780 and r.json()["estado"] == "pagado"
    check("F02 crear pedido: total y estado correctos", ok, f"status={r.status_code} body={r.text[:120]}")
    order_id = r.json().get("order_id")

    # F02 - Reintento con la misma clave: no duplica.
    r2 = cli.post(f"{GW}/api/orders/pedidos", json=pedido, headers=bearer(token_ana))
    check("F02 reintento idempotente: mismo order_id",
          r2.status_code == 200 and r2.json().get("order_id") == order_id and r2.json().get("reintento") is True,
          f"reintento={r2.json().get('reintento')}")

    # Listar pedidos propios.
    r = cli.get(f"{GW}/api/orders/pedidos", headers=bearer(token_ana))
    check("Listado de pedidos propios incluye el pedido",
          any(p["order_id"] == order_id for p in r.json()["pedidos"]))

    # F03 - Pedido propio vs ajeno.
    r = cli.get(f"{GW}/api/orders/pedidos/{order_id}", headers=bearer(token_ana))
    check("F03 consulta pedido propio", r.status_code == 200, f"status={r.status_code}")
    rp = login("pedro", "5678")
    token_pedro = rp.json()["access_token"]
    r = cli.get(f"{GW}/api/orders/pedidos/{order_id}", headers=bearer(token_pedro))
    check("F03 pedido ajeno no se revela (404)", r.status_code == 404, f"status={r.status_code}")

    # Control de rol: cliente no crea productos, operador si.
    nuevo = {"sku": "SKU-900", "nombre": "Set Marcadores", "precio": 3500, "stock": 10, "categoria": "escritura"}
    r = cli.post(f"{GW}/api/inventory/productos", json=nuevo, headers=bearer(token_ana))
    check("Cliente NO puede crear producto (403)", r.status_code == 403, f"status={r.status_code}")
    rc = login("carla", "oper123")
    token_carla = rc.json()["access_token"]
    r = cli.post(f"{GW}/api/inventory/productos", json=nuevo, headers=bearer(token_carla))
    check("Operador SI puede crear producto (201)", r.status_code in (201, 409), f"status={r.status_code}")

    # F04 - Agregacion GraphQL.
    q = {"query": "{ resumen { productos { sku } pedidos { order_id estado } } }"}
    r = cli.post(f"{GW}/api/graphql", json=q, headers=bearer(token_ana))
    datos = r.json().get("data", {}).get("resumen", {}) if r.status_code == 200 else {}
    check("F04 GraphQL agrega productos y pedidos",
          r.status_code == 200 and len(datos.get("productos", [])) >= 6 and len(datos.get("pedidos", [])) >= 1,
          f"status={r.status_code}")

    # Stock insuficiente.
    r = cli.post(f"{GW}/api/orders/pedidos",
                 json={"items": [{"sku": "SKU-006", "cantidad": 99999}], "idempotency_key": "ana-key-stock"},
                 headers=bearer(token_ana))
    check("Pedido con stock insuficiente rechazado (409)", r.status_code == 409, f"status={r.status_code}")

    cli.close()
    total = len(resultados)
    ok = sum(resultados)
    print(f"\nResumen: {ok}/{total} escenarios PASS")


if __name__ == "__main__":
    main()

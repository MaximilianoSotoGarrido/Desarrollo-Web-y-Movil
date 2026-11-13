"""
Productor STOMP de OfiExpress (mensajeria asincrona para la integracion).

Publica eventos de pedido en la cola /queue/pedidos de un broker MQ. Permite
cuatro formatos de mensaje (texto, ancho fijo, XML y JSON) para demostrar la
interoperabilidad entre sistemas. Es el mecanismo de integracion asincrona del
caso; no reemplaza los contratos HTTP de los microservicios.

Ejemplos:
    python productor.py --formato json --id 1001 --cliente "Ana Rivas" --monto 13360
    python productor.py --formato xml  --id 1002 --cliente "Pedro Soto" --monto 8990
    python productor.py --formato fijo --id 1003 --cliente "Ana Rivas" --monto 4290
    python productor.py --formato texto --mensaje "Pedido 1004 confirmado"
"""
import argparse
import json
import xml.etree.ElementTree as ET
from decimal import Decimal, InvalidOperation

import stomp

DESTINO = "/queue/pedidos"


def construir_mensaje(formato: str, pedido: dict, texto: str):
    if formato == "texto":
        return texto, "text/plain; charset=utf-8"
    if formato == "fijo":
        # Contrato de ancho fijo 36: ID[6] + CLIENTE[20] + MONTO[10].
        identificador = str(pedido["id"])
        cliente = str(pedido["cliente"])
        monto = Decimal(pedido["monto"])
        monto_campo = f"{monto:010.2f}"
        if not (identificador.isascii() and identificador.isdigit() and len(identificador) <= 6):
            raise ValueError("En formato fijo, --id debe tener hasta 6 digitos ASCII")
        if len(cliente) > 20 or any(ord(c) > 127 for c in cliente):
            raise ValueError("En formato fijo, --cliente admite hasta 20 caracteres ASCII")
        if monto < 0 or len(monto_campo) != 10:
            raise ValueError("En formato fijo, --monto debe estar entre 0 y 99999999.99")
        return f"{identificador.zfill(6)}{cliente:<20}{monto_campo}", "text/plain; charset=utf-8"
    if formato == "json":
        return json.dumps(pedido, ensure_ascii=False), "application/json; charset=utf-8"
    if formato == "xml":
        raiz = ET.Element("pedido")
        for clave, valor in pedido.items():
            ET.SubElement(raiz, clave).text = str(valor)
        return ET.tostring(raiz, encoding="unicode"), "application/xml; charset=utf-8"
    raise ValueError(f"Formato desconocido: {formato}")


def main():
    parser = argparse.ArgumentParser(description="Productor STOMP de pedidos OfiExpress")
    parser.add_argument("--formato", choices=["texto", "fijo", "xml", "json"], required=True)
    parser.add_argument("--mensaje", default="Pedido confirmado en OfiExpress")
    parser.add_argument("--id", default="1001")
    parser.add_argument("--cliente", default="Ana Rivas")
    parser.add_argument("--monto", default="13360")
    parser.add_argument("--host", default="127.0.0.1")
    parser.add_argument("--port", default=61613)
    parser.add_argument("--usuario", default="demo")
    parser.add_argument("--clave", default="demo123")
    args = parser.parse_args()

    try:
        monto = Decimal(args.monto)
        if not monto.is_finite():
            raise ValueError("El monto debe ser un numero finito")
    except InvalidOperation:
        parser.error(f"Monto no valido: {args.monto}")

    pedido = {"id": args.id, "cliente": args.cliente, "monto": str(monto)}
    try:
        cuerpo, mime = construir_mensaje(args.formato, pedido, args.mensaje)
    except ValueError as exc:
        parser.error(str(exc))

    conexion = stomp.Connection12([(args.host, args.port)], vhost="/")
    try:
        conexion.connect(args.usuario, args.clave, wait=True)
        conexion.send(
            destination=DESTINO,
            body=cuerpo,
            headers={"content-type": mime, "x-formato": args.formato, "persistent": "true"},
        )
        print(f"Mensaje enviado a {DESTINO} (formato={args.formato}):\n{cuerpo}")
    finally:
        if conexion.is_connected():
            conexion.disconnect()


if __name__ == "__main__":
    main()

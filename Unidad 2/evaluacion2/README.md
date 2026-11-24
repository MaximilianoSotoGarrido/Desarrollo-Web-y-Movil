# Backend OfiExpress — Evaluación 2 (Unidad 2)

Backend seguro del caso 37 (Papelería **OfiExpress**): API Gateway, Auth Service,
administrador de secretos, GraphQL Gateway y cuatro microservicios privados
(`inventory`, `orders`, `clients`, `payments`), más mensajería asíncrona STOMP.
El frontend de `Unidad 1/Semana 2` está integrado: llama solo al API Gateway, que tiene CORS
habilitado únicamente para el origen local del frontend.

## Requisitos

- Python 3.11+
- Node.js 18+ (para el GraphQL Gateway)
- MongoDB Community corriendo en `mongodb://localhost:27017`
- (Opcional) Un broker STOMP, p. ej. ActiveMQ, en `127.0.0.1:61613` para la mensajería

## Instalación

```bash
# Desde esta carpeta (Unidad 2/evaluacion2)
pip install -r requirements.txt

cd graphql-gateway
npm.cmd install          # en Windows use npm.cmd (npm.ps1 suele estar bloqueado)
cd ..
```

> **Windows / PowerShell:** no hace falta entorno virtual. Si PowerShell bloquea
> los scripts (`activate`, `npm`), use `python -m uvicorn ...` y `npm.cmd ...`
> como se muestra abajo, o lance todo de una vez con `run_all.ps1`.

## Puertos y servicios

| Servicio            | Carpeta           | Puerto | Acceso                         |
|---------------------|-------------------|--------|--------------------------------|
| Administrador de secretos | `secret-manager`  | 8200   | Interno (token de Vault)       |
| Auth Service        | `auth-service`    | 8100   | Interno (secreto del gateway)  |
| inventory           | `inventory`       | 9000   | Interno (firma del gateway)    |
| orders              | `orders`          | 9100   | Interno (firma del gateway)    |
| clients             | `clients`         | 9200   | Interno (firma del gateway)    |
| payments            | `payments`        | 9300   | Interno (firma del gateway)    |
| GraphQL Gateway     | `graphql-gateway` | 8090   | Interno (firma del gateway)    |
| **API Gateway**     | `gateway`         | 8000   | **Público (único expuesto)**   |

Solo el API Gateway (`:8000`) se expone al navegador. El resto son privados.

## Orden de arranque

### Opción A — todo de una vez (Windows)

Desde esta carpeta:

```powershell
powershell -ExecutionPolicy Bypass -File .\run_all.ps1
```

Abre una ventana por servicio en el orden correcto. Espere unos segundos a que
el API Gateway (`:8000`) termine de cargar.

### Opción B — una terminal por servicio

Abra una terminal por servicio y, dentro de cada carpeta, ejecute (`python -m uvicorn`
funciona aunque `uvicorn` no esté en el PATH):

```bash
# 1) Secret manager
cd secret-manager && python -m uvicorn secret_manager:app --host 127.0.0.1 --port 8200

# 2) Auth service
cd auth-service && python -m uvicorn auth_service:app --host 127.0.0.1 --port 8100

# 3) Microservicios privados
cd inventory && python -m uvicorn inventory:app --host 127.0.0.1 --port 9000
cd orders    && python -m uvicorn orders:app    --host 127.0.0.1 --port 9100
cd clients   && python -m uvicorn clients:app   --host 127.0.0.1 --port 9200
cd payments  && python -m uvicorn payments:app  --host 127.0.0.1 --port 9300

# 4) GraphQL gateway
cd graphql-gateway && node server.js

# 5) API Gateway (último)
cd gateway && python -m uvicorn gateway:app --host 0.0.0.0 --port 8000
```

Con todo arriba, cargue los datos de prueba (una vez):

```bash
python seed.py
```

## Pruebas

```bash
python tests/pruebas.py
```

Imprime PASS/FALLA por escenario (catálogo público, login, pedido con
idempotencia, pedido ajeno rechazado, control de rol y agregación GraphQL).
Guarde esa salida como evidencia.

## Usuarios de prueba

| Usuario | Clave     | Rol      | user_id |
|---------|-----------|----------|---------|
| ana     | 1234      | cliente  | USR-001 |
| pedro   | 5678      | cliente  | USR-002 |
| carla   | oper123   | operador | USR-003 |

## Registros de cada servicio

Cada ventana muestra líneas con su etiqueta: `[GATEWAY]`, `[SECRET-MANAGER]`, `[AUTH]`,
`[INVENTORY]` y `[ORDERS]`. Ahí se ve la validación de secretos, la autenticación, el
enrutamiento y cada consulta o escritura en MongoDB.

## Mensajería STOMP (integración asíncrona)

Con un broker STOMP activo, en dos terminales:

```bash
python stomp/consumidor.py
python stomp/productor.py --formato json --id 1001 --cliente "Ana Rivas" --monto 3780
```

## Notas de seguridad

- Los microservicios solo aceptan llamadas firmadas con `X-Gateway-Secret`.
- El Auth Service solo responde a quien presenta `X-Gateway-Auth-Secret`.
- Los secretos se obtienen del administrador de secretos (compatible con Vault
  KV v2), nunca se escriben en el código ni llegan al navegador.
- El gateway descarta cualquier cabecera de identidad que venga del navegador y
  propaga solo la identidad que él mismo verificó.

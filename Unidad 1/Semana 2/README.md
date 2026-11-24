# OfiExpress — Evaluación 1 (Front-End responsivo)

Sitio de la papelería **OfiExpress** (Caso 37): venta en línea de útiles de oficina y escolares. Cinco páginas con Bootstrap 5 y un flujo de compra dinámico en `productos.php`, integrado con el backend de la Unidad 2 a través del API Gateway.

#### Caso, actor y flujo
* **Caso:** 37 — Papelería OfiExpress.
* **Actor:** cliente que compra útiles en línea.
* **Objetivo:** encontrar productos y enviar un pedido.
* **Flujo (en `productos.php`):** cargar catálogo → buscar → agregar al pedido (y ajustar cantidades) → iniciar sesión → confirmar pedido.

#### Requisitos abordados
* Catálogo de productos con nombre, descripción y precio.
* Búsqueda de productos por nombre.
* Pedido con varias líneas, cantidades y total calculado.
* Confirmación del pedido con respuesta del servicio.
* Navegación entre Inicio, Empresa, Servicios, Productos y Contacto, y modal Acceder.

#### Integración con el backend
Todas las llamadas van al API Gateway (`http://localhost:8000`), nunca directo a los servicios.
1. **Catálogo:** `GET /api/inventory/productos`, ruta pública.
2. **Login:** `POST /api/auth/login`. El token recibido se guarda en `sessionStorage`.
3. **Pedido:** `POST /api/orders/pedidos` con el token en la cabecera `Authorization`. El gateway valida el token con el Auth Service y envía el pedido a orders, que reserva stock en inventory y guarda el pedido en MongoDB.

Estados de la interfaz: **carga** (spinner), **éxito**, **vacío** (pedido vacío o búsqueda sin resultados) y **error** (catálogo con botón Reintentar; pedido sin sesión, sin stock o con el servicio caído, que se conserva para volver a confirmar).

#### Cómo ejecutar
Primero levantar el backend (`Unidad 2/evaluacion2`, ver su README) y cargar los datos con `python seed.py`. `fetch` necesita un servidor web; no se debe abrir el archivo con doble clic.
* **WAMP / Apache (como en clases):** copiar la carpeta en el directorio del servidor y abrir `http://localhost:8080/.../Semana 2/index.php`.
* **PHP instalado:** desde esta carpeta, `php -S localhost:8080` y abrir `http://localhost:8080/index.php`.

Bootstrap 5.3.3 y FontAwesome 4.7 se cargan por CDN (requiere internet).

##### Cómo probar
1. Entrar a **Productos**: aparece `"Cargando productos..."` y luego las 6 tarjetas.
2. Escribir en el buscador: las tarjetas se filtran; con un texto sin coincidencias se muestra el mensaje de búsqueda vacía.
3. Presionar **Agregar**: el panel *Mi pedido*, el contador y el total se actualizan sin recargar la página. Los botones − / + cambian la cantidad.
4. **Confirmar pedido** sin iniciar sesión: el gateway responde 401 y aparece el aviso para iniciar sesión. El pedido se mantiene.
5. **Acceder** con `ana` / `1234` y confirmar: mensaje `"Enviando pedido..."` y luego el número de pedido.

#### Estructura
```text
Semana 2/
├── index.php            # Inicio (carrusel de promociones)
├── empresa.php          # Quiénes somos, equipo, misión
├── servicios.php
├── productos.php        # catálogo dinámico + pedido
├── contacto.php
├── css/estilos.css      # Box Model y Flexbox
├── js/productos.js      # DOM, fetch al gateway, filtro, login, pedido y estados
└── views/               # diseño previo y evidencia responsiva
```

#### Grid, Box Model y Flexbox
* **Grid (Bootstrap):** catálogo en `col-lg-8` y pedido en `col-lg-4`; las tarjetas usan `col-12 col-sm-6 col-md-4 col-xl-3` (1 / 2 / 3 / 4 por fila).
* **Box Model (`estilos.css`):** `box-sizing`, `padding`, `border`, `border-radius`, `min-height` y `max-width` en tarjetas y panel del pedido.
* **Flexbox:** tarjetas (`d-flex flex-column`), líneas del pedido, controles − / +, total y mensajes de estado.

#### Evidencia (carpeta `views/`)
* `wireframe.png`: diseño previo anotado (mapa del sitio, flujo y decisiones por pantalla).
* Capturas de `productos.php` en cuatro anchos:

| Archivo | Ancho | Qué cambia |
| :--- | :--- | :--- |
| `viewport-movil.png` | 375 px | 1 tarjeta por fila, navbar en ☰, pedido al final |
| `viewport-tablet.png` | 768 px | 3 tarjetas por fila, navbar completa, pedido bajo el catálogo |
| `viewport-tablet-horizontal.png` | 1024 px | catálogo y pedido lado a lado, 3 tarjetas por fila |
| `viewport-escritorio.png` | 1440 px | 4 tarjetas por fila y pedido a la derecha |

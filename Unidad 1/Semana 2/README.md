# OfiExpress — Evaluación 1 (Front-End responsivo)

Sitio de la papelería **OfiExpress** (Caso 37): venta en línea de útiles de oficina y escolares. Cinco páginas con Bootstrap 5 y un flujo de compra dinámico en `productos.php` que consume servicios simulados (sin backend real).

#### Caso, actor y flujo
* **Caso:** 37 — Papelería OfiExpress.
* **Actor:** cliente que compra útiles en línea.
* **Objetivo:** encontrar productos y enviar un pedido.
* **Flujo (en `productos.php`):** cargar catálogo → buscar → agregar al pedido (y ajustar cantidades) → confirmar pedido.

#### Requisitos abordados
* Catálogo de productos con nombre, descripción y precio.
* Búsqueda de productos por nombre.
* Pedido con varias líneas, cantidades y total calculado.
* Confirmación del pedido con respuesta del servicio.
* Navegación entre Inicio, Empresa, Servicios, Productos y Contacto, y modal Acceder.

#### Servicios simulados
1. **Consulta:** `js/productos.js` obtiene el catálogo con `fetch("data/productos.json")`. El JSON tiene la forma `{ status, message, data }`, igual que una respuesta de API.
2. **Acción:** al confirmar, `enviarPedidoAPI()` simula un envío con una demora de 1 segundo y responde éxito o error. La casilla **"Simular error del servicio"** permite ver el estado de error.

Estados de la interfaz: **carga** (spinner), **éxito**, **vacío** (pedido vacío o búsqueda sin resultados) y **error** (catálogo con botón Reintentar; pedido que se conserva para volver a confirmar).

#### Cómo ejecutar
`fetch` necesita un servidor web; no se debe abrir el archivo con doble clic.
* **WAMP / Apache (como en clases):** copiar la carpeta en el directorio del servidor y abrir `http://localhost:8080/.../Semana 2/index.php`.
* **PHP instalado:** desde esta carpeta, `php -S localhost:8080` y abrir `http://localhost:8080/index.php`.

Bootstrap 5.3.3 y FontAwesome 4.7 se cargan por CDN (requiere internet).

##### Cómo probar
1. Entrar a **Productos**: aparece `"Cargando productos..."` y luego las 6 tarjetas.
2. Escribir en el buscador: las tarjetas se filtran; con un texto sin coincidencias se muestra el mensaje de búsqueda vacía.
3. Presionar **Agregar**: el panel *Mi pedido*, el contador y el total se actualizan sin recargar la página. Los botones − / + cambian la cantidad.
4. **Confirmar pedido**: mensaje `"Enviando pedido..."` y luego el número de pedido.
5. Marcar **Simular error del servicio** y confirmar: aparece el error y el pedido se mantiene.

#### Estructura
```text
Semana 2/
├── index.php            # Inicio (carrusel de promociones)
├── empresa.php          # Quiénes somos, equipo, misión
├── servicios.php
├── productos.php        # catálogo dinámico + pedido
├── contacto.php
├── css/estilos.css      # Box Model y Flexbox
├── js/productos.js      # DOM, fetch, filtro, pedido y estados
├── data/productos.json  # servicio simulado del catálogo
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

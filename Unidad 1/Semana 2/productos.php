<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OfiExpress - Productos de Papelería</title>
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 4 CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <!-- Estilos propios: Box Model y Flexbox -->
  <link href="css/estilos.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

  <!-- Navbar Responsivo Unificado -->
  <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold text-warning" href="index.php">
          <i class="fa fa-book me-2"></i>OfiExpress
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Inicio</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Empresa
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="empresa.php">Quiénes Somos</a></li>
                <li><a class="dropdown-item" href="empresa.php#equipo">Nuestro Equipo</a></li>
                <li><a class="dropdown-item" href="empresa.php#mision">Misión</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="servicios.php">Servicios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="productos.php">Productos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contacto.php">Contacto</a>
            </li>
          </ul>
          <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fa fa-user me-1"></i> Acceder
          </button>
        </div>
      </div>
    </nav>
  </header>

  <!-- Contenido Principal - Catálogo de Productos -->
  <main class="container-fluid py-4 flex-grow-1">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-dark">Catálogo de Productos - OfiExpress</h2>
      <p class="text-muted">Artículos escolares y de oficina de alta calidad (Caso 37)</p>
    </div>

    <!-- Buscador Dinámico con JS -->
    <div class="container mb-4">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6">
          <div class="input-group">
            <input type="text" id="inputBuscar" class="form-control" placeholder="Buscar útil de oficina o papel...">
            <button class="btn btn-warning" type="button" id="btnBuscar"><i class="fa fa-search me-1"></i> Buscar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Columna catálogo: las tarjetas se generan con JS desde el servicio simulado -->
      <div class="col-12 col-lg-8">
        <div class="row g-3" id="contenedorProductos"></div>
      </div>

      <!-- Columna pedido -->
      <div class="col-12 col-lg-4">
        <div class="panel-pedido">
          <h5 class="fw-bold mb-3"><i class="fa fa-shopping-cart text-warning me-2"></i>Mi pedido
            <span class="badge bg-warning text-dark ms-1" id="contadorPedido">0</span></h5>

          <!-- Estado vacío del pedido -->
          <div id="pedidoVacio" class="estado-ui">
            <i class="fa fa-cart-plus"></i>
            <p class="mb-0">Tu pedido está vacío.<br>Agrega productos del catálogo.</p>
          </div>

          <!-- Líneas del pedido (se generan con JS) -->
          <div id="lineasPedido"></div>

          <!-- Total y confirmación -->
          <div id="piePedido" class="d-none">
            <div class="total-pedido">
              <span>Total</span>
              <span id="totalPedido">$0</span>
            </div>
            <div class="form-check my-2">
              <input class="form-check-input" type="checkbox" id="simularError">
              <label class="form-check-label small text-muted" for="simularError">Simular error del servicio</label>
            </div>
            <button class="btn btn-dark w-100" type="button" id="btnConfirmar">
              <i class="fa fa-check me-1"></i> Confirmar pedido
            </button>
          </div>

          <!-- Resultado de la confirmación: carga, éxito o error -->
          <div id="resultadoPedido" class="mt-2"></div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer Unificado -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
      <p class="mb-0"><strong>OfiExpress@2026</strong> - Venta de útiles de oficina y escolares (Caso 37)</p>
    </div>
  </footer>

  <!-- Modal de Autenticacion Unificado -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="loginModalLabel"><i class="fa fa-user-circle me-2"></i>Autenticación de Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <form action="empresa.php" method="POST" id="formLogin">
          <div class="modal-body">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Correo Electrónico:</label>
              <input type="email" class="form-control" id="loginEmail" required placeholder="usuario@ofiexpress.cl">
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Contraseña:</label>
              <input type="password" class="form-control" id="loginPassword" required placeholder="••••••••">
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Recordar sesión</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-warning fw-bold">Iniciar Sesión</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts al final del documento (Regla Critica JS DOM) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/productos.js"></script>
</body>
</html>

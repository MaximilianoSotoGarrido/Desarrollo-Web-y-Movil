<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OfiExpress - Servicios Corporativos</title>
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 4 CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
              <a class="nav-link active" href="servicios.php">Servicios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="productos.php">Productos</a>
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

  <!-- Contenido Principal - Servicios -->
  <main class="container py-5 flex-grow-1">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-dark">Servicios Integrales de Papelería</h2>
      <p class="text-muted">Soluciones a la medida para empresas, colegios e instituciones (Caso 37)</p>
    </div>

    <div class="row g-4">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 text-center border-0 shadow-sm p-3">
          <div class="card-body">
            <i class="fa fa-truck fa-3x text-warning mb-3"></i>
            <h5 class="card-title fw-bold">Despacho Exprés</h5>
            <p class="card-text text-secondary small">Entrega rápida en 24 horas para pedidos de papelería y suministros de oficina prioritarios.</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 text-center border-0 shadow-sm p-3">
          <div class="card-body">
            <i class="fa fa-print fa-3x text-warning mb-3"></i>
            <h5 class="card-title fw-bold">Impresión y Anillado</h5>
            <p class="card-text text-secondary small">Servicio corporativo de fotocopiado, anillado, termolaminado y encuadernación de documentos.</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 text-center border-0 shadow-sm p-3">
          <div class="card-body">
            <i class="fa fa-briefcase fa-3x text-warning mb-3"></i>
            <h5 class="card-title fw-bold">Convenio Empresa</h5>
            <p class="card-text text-secondary small">Crédito a 30 días y facturación mensual unificada para compras recurrentes de oficina.</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 text-center border-0 shadow-sm p-3">
          <div class="card-body">
            <i class="fa fa-graduation-cap fa-3x text-warning mb-3"></i>
            <h5 class="card-title fw-bold">Listas Escolares</h5>
            <p class="card-text text-secondary small">Armado de listas útiles escolares completas con envío directo al domicilio o establecimiento.</p>
          </div>
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

  <!-- Scripts al final del documento -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

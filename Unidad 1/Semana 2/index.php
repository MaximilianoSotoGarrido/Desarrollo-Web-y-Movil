<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OfiExpress - Papelería y Útiles de Oficina</title>
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
              <a class="nav-link active" href="index.php">Inicio</a>
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

  <!-- Carrusel de Promociones (Jumbotron / Banner principal) -->
  <section class="container my-4">
    <div id="promoCarousel" class="carousel slide shadow rounded overflow-hidden" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1"></button>
      </div>
      <div class="carousel-inner bg-secondary text-white text-center py-5">
        <div class="carousel-item active">
          <div class="p-4">
            <h2 class="fw-bold">Especial Temporada Escolar y Oficina</h2>
            <p class="lead">Todo en artículos de papelería, cuadernos y resmas con despacho exprés.</p>
            <a href="productos.php" class="btn btn-warning fw-bold"><i class="fa fa-shopping-cart me-1"></i> Ver Catálogo</a>
          </div>
        </div>
        <div class="carousel-item">
          <div class="p-4">
            <h2 class="fw-bold">Convenios Corporativos OfiExpress</h2>
            <p class="lead">Descuentos por volumen e insumos de oficina para empresas.</p>
            <a href="servicios.php" class="btn btn-light fw-bold"><i class="fa fa-briefcase me-1"></i> Ver Servicios</a>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </section>

  <!-- Seccion Principales Categorias con Grid Responsivo -->
  <main class="container-fluid py-4 flex-grow-1">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-dark">Categorías Destacadas de Papelería</h3>
      <p class="text-muted">Encuentra los insumos esenciales para tu colegio, universidad o lugar de trabajo</p>
    </div>

    <!-- Application of Exact Responsive Breakpoints -->
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-pencil fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Escritura</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-file-text-o fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Papeles</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-folder-open fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Archivadores</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-book fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Cuadernos</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-calculator fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Oficina</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <i class="fa fa-scissors fa-2x text-warning mb-2"></i>
            <h6 class="card-title fw-bold">Corte y Pegado</h6>
            <a href="productos.php" class="btn btn-sm btn-outline-dark mt-2">Ver más</a>
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

  <!-- Scripts al final del documento (Regla Critica DOM) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script JS ES6+ para validacion de formulario modal (Semana 3)
    document.addEventListener("DOMContentLoaded", () => {
      const formLogin = document.getElementById("formLogin");
      if(formLogin) {
        formLogin.addEventListener("submit", (e) => {
          const email = document.getElementById("loginEmail").value;
          console.log(`Intento de inicio de sesion: ${email}`);
        });
      }
    });
  </script>
</body>
</html>

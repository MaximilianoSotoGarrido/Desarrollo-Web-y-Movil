<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MiEmpresa - Productos</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 4 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- Navbar Responsivo -->
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">
        <i class="fa fa-car me-2"></i>MiEmpresa
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
              <li><a class="dropdown-item" href="empresa.php">Nuestro Equipo</a></li>
              <li><a class="dropdown-item" href="empresa.php">Misión</a></li>
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
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
          Acceder
        </button>
      </div>
    </div>
  </nav>

  <!-- Contenido Principal: Productos -->
  <main class="my-4 flex-grow-1">
    
    <!-- Carrusel de Imágenes en Contenedor Normal (Como enseñó el profesor para evitar que ocupe toda la pantalla) -->
    <div class="container mb-5">
      <h2 class="text-center mb-3">Galería de Lanzamientos</h2>
      <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="img/la.jpg" class="d-block w-100" alt="Los Angeles">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
              <h5>Lanzamiento 1</h5>
              <p>Descripción de nuestro primer gran lanzamiento tecnológico.</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="img/ny.jpg" class="d-block w-100" alt="New York">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
              <h5>Lanzamiento 2</h5>
              <p>Descripción de nuestro segundo gran lanzamiento tecnológico.</p>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>

    <!-- Sección de Grilla de Tarjetas en un contenedor amarillo de ancho completo sin bordes -->
    <div class="container-fluid bg-warning py-5">
      <div class="px-4">
        <h2 class="text-center mb-4">Catálogo de Productos</h2>
        <div class="row g-3 justify-content-center">
          
          <!-- Breakpoints responsivos requeridos por el profesor -->
          <!-- Tarjeta 1 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 1</h6>
                <p class="card-text text-muted small mb-0">ID: #01</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 2 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 2</h6>
                <p class="card-text text-muted small mb-0">ID: #02</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 3 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 3</h6>
                <p class="card-text text-muted small mb-0">ID: #03</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 4 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 4</h6>
                <p class="card-text text-muted small mb-0">ID: #04</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 5 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 5</h6>
                <p class="card-text text-muted small mb-0">ID: #05</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 6 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 6</h6>
                <p class="card-text text-muted small mb-0">ID: #06</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 7 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 7</h6>
                <p class="card-text text-muted small mb-0">ID: #07</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 8 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 8</h6>
                <p class="card-text text-muted small mb-0">ID: #08</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 9 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 9</h6>
                <p class="card-text text-muted small mb-0">ID: #09</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 10 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 10</h6>
                <p class="card-text text-muted small mb-0">ID: #10</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 11 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 11</h6>
                <p class="card-text text-muted small mb-0">ID: #11</p>
              </div>
            </div>
          </div>
          <!-- Tarjeta 12 -->
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
            <div class="card h-100 text-center">
              <img src="img/img_avatar1.png" class="card-img-top" alt="Avatar">
              <div class="card-body p-2">
                <h6 class="card-title fw-bold mb-1">Prod 12</h6>
                <p class="card-text text-muted small mb-0">ID: #12</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- Footer Oscuro -->
  <footer class="bg-dark text-white text-center py-3">
    <strong>MiEmpresa@2026</strong>
  </footer>

  <!-- Modal de Autenticación -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Autenticación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="empresa.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email:</label>
              <input type="email" class="form-control" id="loginEmail" required placeholder="ejemplo@correo.com">
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password:</label>
              <input type="password" class="form-control" id="loginPassword" required>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MiEmpresa - Contacto</title>
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
            <a class="nav-link" href="productos.php">Productos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="contacto.php">Contacto</a>
          </li>
        </ul>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
          Acceder
        </button>
      </div>
    </div>
  </nav>

  <!-- Contenido Principal: Formulario de Contacto (De ancho completo bg-warning, sin bordes definidos como la captura) -->
  <main class="container-fluid bg-warning py-5 flex-grow-1">
    <div class="row justify-content-center">
      <div class="col-12">
        <form action="empresa.php" method="POST" class="p-3">
          <!-- Email -->
          <div class="mb-3">
            <label for="contactEmail" class="form-label fw-bold">Email:</label>
            <input type="email" class="form-control" id="contactEmail" name="email" required placeholder="Enter email">
          </div>
          <!-- Comentarios -->
          <div class="mb-3">
            <label for="contactComments" class="form-label fw-bold">Comentarios:</label>
            <textarea class="form-control" id="contactComments" name="comments" rows="5" placeholder="Escribe tus comentarios aquí..." required></textarea>
          </div>
          <!-- Botón de Enviar con Margen Superior mt-1 -->
          <button type="submit" class="btn btn-primary mt-1">Enviar</button>
        </form>
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

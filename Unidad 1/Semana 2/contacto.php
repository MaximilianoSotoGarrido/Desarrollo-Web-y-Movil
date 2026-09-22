<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OfiExpress - Contacto y Cotizaciones</title>
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
              <a class="nav-link" href="servicios.php">Servicios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="productos.php">Productos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="contacto.php">Contacto</a>
            </li>
          </ul>
          <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fa fa-user me-1"></i> Acceder
          </button>
        </div>
      </div>
    </nav>
  </header>

  <!-- Contenido Principal - Formulario de Contacto -->
  <main class="container py-5 flex-grow-1">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-dark text-white text-center py-3">
            <h4 class="mb-0"><i class="fa fa-envelope me-2 text-warning"></i>Contacto y Cotizaciones</h4>
          </div>
          <div class="card-body p-4">
            <form action="empresa.php" method="POST" id="formContacto">
              <div class="mb-3">
                <label for="nombreContacto" class="form-label">Nombre Completo:</label>
                <input type="text" class="form-control" id="nombreContacto" name="nombre" required placeholder="Juan Pérez">
              </div>
              <div class="mb-3">
                <label for="emailContacto" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="emailContacto" name="email" required placeholder="contacto@empresa.cl">
              </div>
              <div class="mb-3">
                <label for="asuntoContacto" class="form-label">Asunto:</label>
                <select class="form-select" id="asuntoContacto" name="asunto">
                  <option value="cotizacion">Cotización por Volumen</option>
                  <option value="convenio">Convenio Empresa</option>
                  <option value="consulta">Consulta sobre Productos</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="comentario" class="form-label">Mensaje o Detalle del Pedido:</label>
                <textarea class="form-control" id="comentario" name="text" rows="4" required placeholder="Indique la lista de útiles o requerimiento de papelería..."></textarea>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning fw-bold"><i class="fa fa-paper-plane me-1"></i> Enviar Mensaje</button>
              </div>
            </form>
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

  <!-- Scripts al final del documento (Regla Critica JS DOM) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // JS ES6+ - Validacion de formulario de contacto (Semana 3)
    document.addEventListener("DOMContentLoaded", () => {
      const formContacto = document.getElementById("formContacto");
      if(formContacto) {
        formContacto.addEventListener("submit", (e) => {
          const email = document.getElementById("emailContacto").value;
          console.log(`Envio de mensaje de contacto desde: ${email}`);
        });
      }
    });
  </script>
</body>
</html>

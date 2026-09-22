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

    <!-- Rejilla de Tarjetas (Cards) con Breakpoints Responsivos Exactos -->
    <div class="row g-3" id="contenedorProductos">
      
      <!-- Producto 1 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-book fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Cuaderno Universitario</h6>
            <p class="card-text text-secondary small flex-grow-1">100 Hojas matemática 7mm tapa dura</p>
            <p class="fw-bold text-success mb-2">$1.890</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="1"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
          </div>
        </div>
      </div>

      <!-- Producto 2 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-file-text-o fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Resma Papel Carta</h6>
            <p class="card-text text-secondary small flex-grow-1">500 Hojas 75g multicopia</p>
            <p class="fw-bold text-success mb-2">$4.290</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="2"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
          </div>
        </div>
      </div>

      <!-- Producto 3 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-pencil fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Set Lápices Pasta</h6>
            <p class="card-text text-secondary small flex-grow-1">Pack 10 unidades azul/negro/rojo</p>
            <p class="fw-bold text-success mb-2">$2.490</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="3"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
          </div>
        </div>
      </div>

      <!-- Producto 4 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-scissors fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Corchetera Escritorio</h6>
            <p class="card-text text-secondary small flex-grow-1">Metálica ergonómica para 25 hojas</p>
            <p class="fw-bold text-success mb-2">$3.190</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="4"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
          </div>
        </div>
      </div>

      <!-- Producto 5 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-folder-open fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Archivador Tamaño Oficio</h6>
            <p class="card-text text-secondary small flex-grow-1">Lomo ancho reforzado para oficina</p>
            <p class="fw-bold text-success mb-2">$2.890</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="5"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
          </div>
        </div>
      </div>

      <!-- Producto 6 -->
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1 item-producto">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center d-flex flex-column">
            <i class="fa fa-calculator fa-3x text-warning my-2"></i>
            <h6 class="card-title fw-bold text-dark">Calculadora Científica</h6>
            <p class="card-text text-secondary small flex-grow-1">240 Funciones para estudiantes y profesionales</p>
            <p class="fw-bold text-success mb-2">$8.990</p>
            <button class="btn btn-sm btn-dark btn-agregar" data-id="6"><i class="fa fa-cart-plus me-1"></i> Agregar</button>
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
    // JS ES6+ - Filtrado dinámico de productos y manejo de carrito (Semana 3)
    document.addEventListener("DOMContentLoaded", () => {
      const inputBuscar = document.getElementById("inputBuscar");
      const btnBuscar = document.getElementById("btnBuscar");
      const productos = document.querySelectorAll(".item-producto");

      const filtrarProductos = () => {
        const texto = inputBuscar.value.toLowerCase();
        productos.forEach(prod => {
          const titulo = prod.querySelector(".card-title").textContent.toLowerCase();
          if(titulo.includes(texto)) {
            prod.style.display = "block";
          } else {
            prod.style.display = "none";
          }
        });
      };

      if(btnBuscar) btnBuscar.addEventListener("click", filtrarProductos);
      if(inputBuscar) inputBuscar.addEventListener("keyup", filtrarProductos);

      const btnsAgregar = document.querySelectorAll(".btn-agregar");
      btnsAgregar.forEach(btn => {
        btn.addEventListener("click", (e) => {
          const prodId = e.target.getAttribute("data-id");
          console.log(`Producto agregado al carrito ID: ${prodId}`);
        });
      });
    });
  </script>
</body>
</html>

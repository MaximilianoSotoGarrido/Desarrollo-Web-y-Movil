// JS ES6+ - Catálogo dinámico, filtro y pedido de OfiExpress (Caso 37)
// Integración: todas las llamadas van al API Gateway, nunca directo a los servicios
// Consulta: catálogo público desde inventory
// Acción:   login en el Auth Service y confirmación del pedido en orders

const API_GATEWAY = "http://localhost:8000";

let productos = [];   // datos recibidos del gateway
let pedido = [];      // productos agregados: { sku, nombre, precio, cantidad }
let claveIdempotencia = null;  // evita registrar dos veces el mismo pedido

// Formato de precio chileno: 1890 -> $1.890
const formatoPrecio = (valor) => `$${valor.toLocaleString("es-CL")}`;

// Muestra un estado en un contenedor (carga, vacío o error)
const mostrarEstado = (contenedor, icono, mensaje, claseExtra = "") => {
  contenedor.innerHTML = `
    <div class="col-12 estado-ui ${claseExtra}">
      <i class="fa ${icono}"></i>
      <p class="mb-2">${mensaje}</p>
    </div>`;
};

// ---------- 1) Consulta del catálogo al gateway ----------
const cargarProductos = async () => {
  const contenedor = document.getElementById("contenedorProductos");
  mostrarEstado(contenedor, "fa-spinner fa-spin", "Cargando productos...");

  try {
    const respuesta = await fetch(`${API_GATEWAY}/api/inventory/productos`);
    if (!respuesta.ok) {
      throw new Error(`Respuesta ${respuesta.status}`);
    }
    const respuestaAPI = await respuesta.json();
    productos = respuestaAPI.productos;
    mostrarProductos(productos);
  } catch (err) {
    // Estado de error visible para el usuario, con opción de reintentar
    mostrarEstado(contenedor, "fa-exclamation-triangle",
      "No se pudo cargar el catálogo.", "error");
    const btnReintentar = document.createElement("button");
    btnReintentar.className = "btn btn-sm btn-outline-dark";
    btnReintentar.innerText = "Reintentar";
    btnReintentar.addEventListener("click", cargarProductos);
    contenedor.querySelector(".estado-ui").appendChild(btnReintentar);
    console.log(`Error ${err}`);
  }
};

// ---------- 2) Generación de tarjetas con el DOM ----------
const mostrarProductos = (lista) => {
  const contenedor = document.getElementById("contenedorProductos");
  contenedor.innerHTML = "";

  if (lista.length === 0) {
    mostrarEstado(contenedor, "fa-search", "No hay productos que coincidan con la búsqueda.");
    return;
  }

  lista.forEach((prod) => {
    const columna = document.createElement("div");
    columna.className = "col-12 col-sm-6 col-md-4 col-xl-3 item-producto";
    columna.innerHTML = `
      <div class="card card-producto h-100 shadow-sm border-0">
        <div class="card-body text-center d-flex flex-column">
          <i class="fa ${prod.icono} fa-3x text-warning my-2"></i>
          <h6 class="card-title fw-bold text-dark">${prod.nombre}</h6>
          <p class="card-text text-secondary small flex-grow-1">${prod.descripcion}</p>
          <p class="fw-bold text-success mb-2">${formatoPrecio(prod.precio)}</p>
          <button class="btn btn-sm btn-dark btn-agregar" data-sku="${prod.sku}">
            <i class="fa fa-cart-plus me-1"></i> Agregar
          </button>
        </div>
      </div>`;
    contenedor.appendChild(columna);
  });

  // Evento de cada botón Agregar
  const btnsAgregar = document.querySelectorAll(".btn-agregar");
  btnsAgregar.forEach((btn) => {
    btn.addEventListener("click", () => {
      agregarAlPedido(btn.getAttribute("data-sku"));
    });
  });
};

// ---------- 3) Filtro por nombre ----------
const filtrarProductos = () => {
  const texto = document.getElementById("inputBuscar").value.toLowerCase();
  const filtrados = productos.filter((prod) => prod.nombre.toLowerCase().includes(texto));
  mostrarProductos(filtrados);
};

// ---------- 4) Pedido: agregar, cambiar cantidad y quitar ----------
const agregarAlPedido = (sku) => {
  const existente = pedido.find((item) => item.sku === sku);
  if (existente) {
    existente.cantidad++;
  } else {
    const prod = productos.find((p) => p.sku === sku);
    pedido.push({ sku: prod.sku, nombre: prod.nombre, precio: prod.precio, cantidad: 1 });
  }
  document.getElementById("resultadoPedido").innerHTML = "";
  mostrarPedido();
};

const cambiarCantidad = (sku, cambio) => {
  const item = pedido.find((p) => p.sku === sku);
  item.cantidad += cambio;
  if (item.cantidad <= 0) {
    pedido = pedido.filter((p) => p.sku !== sku);
  }
  mostrarPedido();
};

const mostrarPedido = () => {
  const lineas = document.getElementById("lineasPedido");
  const vacio = document.getElementById("pedidoVacio");
  const pie = document.getElementById("piePedido");
  lineas.innerHTML = "";

  let total = 0;
  let unidades = 0;

  pedido.forEach((item) => {
    total += item.precio * item.cantidad;
    unidades += item.cantidad;

    const fila = document.createElement("div");
    fila.className = "linea-pedido";
    fila.innerHTML = `
      <div>
        <div class="fw-bold small">${item.nombre}</div>
        <div class="text-muted small">${formatoPrecio(item.precio)} c/u</div>
      </div>
      <div class="controles">
        <button class="btn btn-sm btn-outline-secondary btn-menos">-</button>
        <span class="fw-bold">${item.cantidad}</span>
        <button class="btn btn-sm btn-outline-secondary btn-mas">+</button>
      </div>`;
    fila.querySelector(".btn-menos").addEventListener("click", () => cambiarCantidad(item.sku, -1));
    fila.querySelector(".btn-mas").addEventListener("click", () => cambiarCantidad(item.sku, 1));
    lineas.appendChild(fila);
  });

  document.getElementById("contadorPedido").innerText = unidades;
  document.getElementById("totalPedido").innerText = formatoPrecio(total);

  // Cambio de clases según el estado del pedido
  if (pedido.length === 0) {
    vacio.classList.remove("d-none");
    pie.classList.add("d-none");
  } else {
    vacio.classList.add("d-none");
    pie.classList.remove("d-none");
  }
};

// ---------- 5) Login: el gateway lo envía al Auth Service ----------
const iniciarSesion = async (evento) => {
  evento.preventDefault();
  const mensaje = document.getElementById("loginMensaje");
  const datos = {
    username: document.getElementById("loginUsuario").value,
    password: document.getElementById("loginPassword").value
  };

  try {
    const respuesta = await fetch(`${API_GATEWAY}/api/auth/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(datos)
    });
    if (!respuesta.ok) {
      mensaje.innerHTML = `<div class="alert alert-danger py-2 mb-0">Usuario o contraseña incorrectos.</div>`;
      return;
    }
    const respuestaAPI = await respuesta.json();
    sessionStorage.setItem("token", respuestaAPI.access_token);
    sessionStorage.setItem("usuario", datos.username);
    mensaje.innerHTML = "";
    mostrarSesion();
    bootstrap.Modal.getInstance(document.getElementById("loginModal")).hide();
  } catch (err) {
    mensaje.innerHTML = `<div class="alert alert-danger py-2 mb-0">No se pudo conectar con el servidor.</div>`;
    console.log(`Error ${err}`);
  }
};

const mostrarSesion = () => {
  const usuario = sessionStorage.getItem("usuario");
  if (usuario) {
    document.getElementById("btnAcceder").innerHTML = `<i class="fa fa-user me-1"></i> ${usuario}`;
  }
};

// ---------- 6) Confirmar pedido: el gateway valida el token y lo envía a orders ----------
const confirmarPedido = async () => {
  const resultado = document.getElementById("resultadoPedido");
  const btnConfirmar = document.getElementById("btnConfirmar");
  btnConfirmar.disabled = true;
  resultado.innerHTML = `<div class="alert alert-secondary mb-0"><i class="fa fa-spinner fa-spin me-2"></i>Enviando pedido...</div>`;

  if (!claveIdempotencia) {
    claveIdempotencia = `WEB-${Date.now()}`;
  }
  const datos = {
    items: pedido.map((item) => ({ sku: item.sku, cantidad: item.cantidad })),
    idempotency_key: claveIdempotencia
  };

  // El token solo se envía si el usuario inició sesión
  const cabeceras = { "Content-Type": "application/json" };
  const token = sessionStorage.getItem("token");
  if (token) {
    cabeceras["Authorization"] = `Bearer ${token}`;
  }

  try {
    const respuesta = await fetch(`${API_GATEWAY}/api/orders/pedidos`, {
      method: "POST",
      headers: cabeceras,
      body: JSON.stringify(datos)
    });
    const respuestaAPI = await respuesta.json();

    if (respuesta.status === 401) {
      throw new Error("Debes iniciar sesión para confirmar el pedido");
    }
    if (!respuesta.ok) {
      throw new Error(respuestaAPI.detail || "El servicio de pedidos no respondió");
    }

    resultado.innerHTML = `
      <div class="alert alert-success mb-0">
        <i class="fa fa-check-circle me-2"></i>Pedido ${respuestaAPI.order_id} registrado
        por ${formatoPrecio(respuestaAPI.total)}, estado ${respuestaAPI.estado}
      </div>`;
    pedido = [];
    claveIdempotencia = null;
    mostrarPedido();
  } catch (err) {
    // El pedido se conserva para que el usuario pueda reintentar
    resultado.innerHTML = `
      <div class="alert alert-danger mb-0">
        <i class="fa fa-times-circle me-2"></i>${err.message}. Tu pedido se mantiene, intenta nuevamente.
      </div>`;
  }
  btnConfirmar.disabled = false;
};

// ---------- Inicio ----------
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("btnBuscar").addEventListener("click", filtrarProductos);
  document.getElementById("inputBuscar").addEventListener("keyup", filtrarProductos);
  document.getElementById("btnConfirmar").addEventListener("click", confirmarPedido);
  document.getElementById("formLogin").addEventListener("submit", iniciarSesion);
  mostrarSesion();
  cargarProductos();
});

// JS ES6+ - Catálogo dinámico, filtro y pedido de OfiExpress (Caso 37)
// Consulta: el catálogo se obtiene de un servicio simulado (data/productos.json)
// Acción:   la confirmación del pedido simula un envío a una API

let productos = [];   // datos recibidos del servicio simulado
let pedido = [];      // productos agregados: { id, nombre, precio, cantidad }

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

// ---------- 1) Consulta al servicio simulado ----------
const cargarProductos = async () => {
  const contenedor = document.getElementById("contenedorProductos");
  mostrarEstado(contenedor, "fa-spinner fa-spin", "Cargando productos...");

  try {
    const respuesta = await fetch("data/productos.json");
    if (!respuesta.ok) {
      throw new Error(`Respuesta ${respuesta.status}`);
    }
    const respuestaAPI = await respuesta.json();
    productos = respuestaAPI.data;
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
          <button class="btn btn-sm btn-dark btn-agregar" data-id="${prod.id}">
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
      agregarAlPedido(Number(btn.getAttribute("data-id")));
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
const agregarAlPedido = (id) => {
  const existente = pedido.find((item) => item.id === id);
  if (existente) {
    existente.cantidad++;
  } else {
    const prod = productos.find((p) => p.id === id);
    pedido.push({ id: prod.id, nombre: prod.nombre, precio: prod.precio, cantidad: 1 });
  }
  document.getElementById("resultadoPedido").innerHTML = "";
  mostrarPedido();
};

const cambiarCantidad = (id, cambio) => {
  const item = pedido.find((p) => p.id === id);
  item.cantidad += cambio;
  if (item.cantidad <= 0) {
    pedido = pedido.filter((p) => p.id !== id);
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
    fila.querySelector(".btn-menos").addEventListener("click", () => cambiarCantidad(item.id, -1));
    fila.querySelector(".btn-mas").addEventListener("click", () => cambiarCantidad(item.id, 1));
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

// ---------- 5) Acción simulada: confirmar pedido ----------
const enviarPedidoAPI = (datos) => {
  // Simula la respuesta de una API con una demora de 1 segundo
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (document.getElementById("simularError").checked) {
        reject(new Error("El servicio de pedidos no respondió"));
      } else {
        resolve({ status: 201, message: "Pedido registrado", data: { numero: Math.floor(Math.random() * 9000) + 1000, total: datos.total } });
      }
    }, 1000);
  });
};

const confirmarPedido = async () => {
  const resultado = document.getElementById("resultadoPedido");
  const btnConfirmar = document.getElementById("btnConfirmar");
  btnConfirmar.disabled = true;
  resultado.innerHTML = `<div class="alert alert-secondary mb-0"><i class="fa fa-spinner fa-spin me-2"></i>Enviando pedido...</div>`;

  let total = 0;
  pedido.forEach((item) => { total += item.precio * item.cantidad; });

  try {
    const respuestaAPI = await enviarPedidoAPI({ items: pedido, total: total });
    resultado.innerHTML = `
      <div class="alert alert-success mb-0">
        <i class="fa fa-check-circle me-2"></i>${respuestaAPI.message}: N° ${respuestaAPI.data.numero}
        por ${formatoPrecio(respuestaAPI.data.total)}
      </div>`;
    pedido = [];
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
  cargarProductos();
});

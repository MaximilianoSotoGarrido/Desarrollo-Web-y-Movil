// --- PARTE 1: Manipulación del DOM en el Navegador ---

// Creación dinámica de un elemento de texto e inserción en el body
let texto = document.createElement("input"); 
texto.setAttribute("type","text"); 
texto.setAttribute("value", "hola"); 
document.body.innerHTML = ""; 
document.body.appendChild(texto);

// Simulación de respuesta de API local
const responseAPI = { 
  "status": 200, 
  "message": "Productos Obtenidos", 
  "data": [{"id":"1","nombre":"Martillo"},{"id":"2","nombre":"Tijera"}] 
};

// Creación dinámica de un combo selector (select) y sus opciones (option)
let cmbComuna = document.createElement("select"); 
cmbComuna.setAttribute("name","cmbComuna"); 

responseAPI.data.forEach((com) => { 
  let optionAux = document.createElement("option"); 
  optionAux.setAttribute("value", com.id); 
  optionAux.innerText = com.nombre; 
  cmbComuna.appendChild(optionAux); 
}); 

document.body.appendChild(cmbComuna);


// --- PARTE 2: Operaciones con Objetos y Propagación (Spread Operator) ---

const objeto = { propiedad1 : 1, propiedad2 : 2}; 
delete objeto.propiedad1; // Eliminar propiedad de forma directa
console.log(objeto); 

// Desempaquetado y ordenamiento usando el operador de propagación (...)
const ordenado = { propiedad1: 3, ...objeto}; 
console.log(ordenado); 

const objeto2 = { "tipo":"IOT"}; 

// Fusión de objetos usando Object.assign
const composicion = Object.assign({}, ordenado, objeto2); 
console.log(composicion); 

// Composición real de objetos jerárquicos
const composicion2 = {ordenado, objeto2}; 
console.log(composicion2); 
console.log(composicion2.objeto2); 

// Recorridos de propiedades usando for...in
for(let prop in composicion2){ 
  console.log(prop); 
} 

// Recorridos estructurados con Object.entries() y Object.values()
Object.entries(composicion2).forEach(([key, value]) => { 
  console.log(`key: ${key}, value: ${value.propiedad1}`); 
}); 

Object.values(composicion2).forEach(value => { 
  console.log(`value: ${value}`); 
}); 

// Desempaquetado de objetos anidados de forma iterativa
Object.entries(composicion2).forEach(([key, value]) => { 
  Object.entries(value).forEach(([key, value]) => { 
    console.log(`key: ${key}, value: ${value}`); 
  }); 
});
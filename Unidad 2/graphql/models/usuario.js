const mongoose = require('mongoose');

// Esquema de Mongoose para Usuarios de OfiExpress (Caso 37)
const usuarioSchema = mongoose.Schema({
  nombre: String,
  pass: String
});

module.exports = mongoose.model('Usuario', usuarioSchema);

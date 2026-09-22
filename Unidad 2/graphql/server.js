const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const { ApolloServer, gql } = require('apollo-server-express');
const Usuario = require('./models/usuario');

// Conexion a MongoDB - Visto en la Semana 6 de la Unidad 2 (Caso 37 - OfiExpress)
mongoose.connect('mongodb://localhost:27017/ofiexpress_db');

// Definicion de Esquemas GraphQL (TypeDefs)
const typeDefs = gql`
  type Usuario {
    id: ID!
    nombre: String!
    pass: String!
  }

  input UsuarioInput {
    nombre: String!
    pass: String!
  }

  type Alert {
    message: String
  }

  type Query {
    getUsuarios: [Usuario]
    getUsuariosById(id: ID!): Usuario
  }

  type Mutation {
    addUsuario(input: UsuarioInput): Usuario
    updUsuario(id: ID!, input: UsuarioInput): Usuario
    delUsuario(id: ID!): Alert
  }
`;

// Resolvers de Consultas y Mutaciones con Mongoose
const resolvers = {
  Query: {
    async getUsuarios() {
      const usuarios = await Usuario.find();
      return usuarios;
    },
    async getUsuariosById(obj, { id }) {
      const usuarioBus = await Usuario.findById(id);
      if (usuarioBus == null) {
        return null;
      }
      return usuarioBus;
    }
  },
  Mutation: {
    async addUsuario(obj, { input }) {
      const usuario = new Usuario(input);
      await usuario.save();
      return usuario;
    },
    async updUsuario(obj, { id, input }) {
      const usuario = await Usuario.findByIdAndUpdate(id, input, { new: true });
      return usuario;
    },
    async delUsuario(obj, { id }) {
      await Usuario.deleteOne({ _id: id });
      return { message: "Usuario Eliminado" };
    }
  }
};

// 1. Inicializamos Express primero
const app = express();
app.use(cors());

let apolloServer = null;
const corsOption = {
  origin: "http://localhost:8090",
  credentials: false
};

async function startServer() {
  apolloServer = new ApolloServer({ typeDefs, resolvers, corsOption });
  await apolloServer.start();
  
  // Ahora "app" ya existe y puede recibir el middleware
  apolloServer.applyMiddleware({ app, cors: false });

  // 2. Iniciamos el servidor SOLAMENTE después de que Apollo esté montado
  app.listen(8090, function () {
    console.log("Servidor GraphQL OfiExpress iniciado en puerto 8090");
  });
}

// Ejecutamos la función de arranque
startServer();
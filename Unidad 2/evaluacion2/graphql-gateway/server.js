// GraphQL Gateway de OfiExpress.
//
// Expone una API de consulta que AGREGA informacion de dos subsistemas privados
// (inventory y orders) sin acceder a sus bases de datos: los consulta por sus
// contratos HTTP internos, firmando con el secreto compartido y propagando la
// identidad verificada que entrega el API Gateway. Aplica un limite de
// profundidad a las consultas antes de resolver.
//
// Levantar:  node server.js   (escucha en el puerto 8090)

const express = require('express');
const { ApolloServer, gql } = require('apollo-server-express');
const depthLimit = require('graphql-depth-limit');

const INVENTORY_URL = process.env.INVENTORY_URL || 'http://127.0.0.1:9000';
const ORDERS_URL = process.env.ORDERS_URL || 'http://127.0.0.1:9100';
const INTERNAL_GATEWAY_SECRET = process.env.INTERNAL_GATEWAY_SECRET || 'gateway-backend-secret-456';
const PORT = process.env.PORT || 8090;

// Esquema: el tipo ResumenCliente combina datos de inventory y orders.
const typeDefs = gql`
  type Producto {
    sku: ID!
    nombre: String!
    precio: Int!
    stock: Int!
    categoria: String
  }

  type LineaPedido {
    sku: ID!
    cantidad: Int!
    precio_unitario: Int!
  }

  type Pedido {
    order_id: ID!
    total: Int!
    estado: String!
    items: [LineaPedido!]!
  }

  type ResumenCliente {
    productos: [Producto!]!
    pedidos: [Pedido!]!
  }

  type Query {
    catalogo: [Producto!]!
    misPedidos: [Pedido!]!
    resumen: ResumenCliente!
  }
`;

// Llama a un servicio privado firmando con el secreto y propagando identidad.
async function llamarServicio(url, identidad) {
  const headers = { 'X-Gateway-Secret': INTERNAL_GATEWAY_SECRET };
  if (identidad) headers['X-Authenticated-Client'] = identidad;
  const resp = await fetch(url, { headers });
  if (!resp.ok) {
    throw new Error(`Servicio respondio ${resp.status}`);
  }
  return resp.json();
}

async function obtenerCatalogo() {
  const data = await llamarServicio(`${INVENTORY_URL}/productos`, null);
  return data.productos;
}

async function obtenerPedidos(identidad) {
  if (!identidad) throw new Error('Identidad requerida para consultar pedidos');
  const data = await llamarServicio(`${ORDERS_URL}/pedidos`, identidad);
  return data.pedidos;
}

const resolvers = {
  Query: {
    catalogo: async () => obtenerCatalogo(),
    misPedidos: async (_p, _a, ctx) => obtenerPedidos(ctx.identidad),
    // Agregacion real: una sola consulta combina catalogo (inventory) y pedidos (orders).
    resumen: async (_p, _a, ctx) => {
      const [productos, pedidos] = await Promise.all([
        obtenerCatalogo(),
        obtenerPedidos(ctx.identidad),
      ]);
      return { productos, pedidos };
    },
  },
};

async function main() {
  const app = express();
  const server = new ApolloServer({
    typeDefs,
    resolvers,
    validationRules: [depthLimit(5)], // limite de profundidad de consulta
    context: ({ req }) => ({
      // Identidad verificada que inyecta el API Gateway (nunca el navegador).
      identidad: req.headers['x-authenticated-client'] || null,
    }),
  });
  await server.start();
  server.applyMiddleware({ app, path: '/graphql' });
  app.listen(PORT, () => {
    console.log(`GraphQL Gateway en http://127.0.0.1:${PORT}${server.graphqlPath}`);
  });
}

main();

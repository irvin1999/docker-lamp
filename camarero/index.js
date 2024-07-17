// camarero/index.js

const socketIoClient = require('socket.io-client');

const administradorSocket = socketIoClient('http://administrador:8001');
const cocineroSocket = socketIoClient('http://cocinero:8003');

administradorSocket.on('databaseChange', (data) => {
  console.log('Cambio en la base de datos desde administrador:', data.message);
  // Actualiza los datos del camarero según sea necesario
});

// Agrega lógica específica del camarero aquí


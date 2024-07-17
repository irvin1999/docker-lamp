// cocinero/index.js

const socketIoClient = require('socket.io-client');

const administradorSocket = socketIoClient('http://administrador:8001');
const camareroSocket = socketIoClient('http://camarero:8002');

administradorSocket.on('databaseChange', (data) => {
  console.log('Cambio en la base de datos desde administrador:', data.message);
  // Actualiza los datos del cocinero según sea necesario
});

// Agrega lógica específica del cocinero aquí

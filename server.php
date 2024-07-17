<?php
error_reporting(E_ALL);

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamos obteniendo mientras llega. */
ob_implicit_flush();

// Definir el host y el puerto del servidor WebSocket
$host = '192.168.1.147';
$port = 8080;

// Crear un socket TCP/IP
if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
} else {
    // Permitir la reutilización del puerto
    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

    // Enlazar el socket al host y puerto especificados
    if (socket_bind($socket, $host, $port) === false) {
        echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($socket)) . "\n";
    }

    // Escuchar en el puerto para conexiones entrantes
    if (socket_listen($socket, 5) === false) {
        echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($socket)) . "\n";
    }

    echo "Server status: on\n";

    // Crear un array para almacenar los clientes conectados
    $clients = array($socket);

    // Loop principal para manejar las conexiones entrantes y los mensajes
    while (true) {
        // Copiar el array de clientes para usar con socket_select
        $changed = $clients;

        // Seleccionar los sockets que tienen datos para leer
        socket_select($changed, $null, $null, 0, 10);

        // Comprobar si hay nuevas conexiones
        if (in_array($socket, $changed)) {
            $socket_new = socket_accept($socket); // Aceptar nueva conexión
            $clients[] = $socket_new; // Agregar el nuevo cliente al array de clientes

            // Eliminar el socket de escucha de los sockets cambiados
            $key = array_search($socket, $changed);
            unset($changed[$key]);
        }

        // Recorrer todos los clientes conectados para manejar los mensajes
        foreach ($changed as $changed_socket) {
            // Leer los datos del socket
            $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);

            // Comprobar si el cliente se ha desconectado
            if ($buf === false) {
                // Quitar el cliente del array de clientes
                $key = array_search($changed_socket, $clients);
                socket_getpeername($changed_socket, $ip);
                unset($clients[$key]);
                // Notificar a todos los clientes sobre la desconexión
                $response = "Usuario desconectado: $ip";
                send_message($response);
            } else {
                // Procesar el mensaje recibido
                $response = "Mensaje recibido: $buf";
                send_message($response);
            }
        }
    }
}

// Función para enviar un mensaje a todos los clientes conectados
function send_message($msg)
{
    global $clients;
    foreach ($clients as $client) {
        @socket_write($client, $msg, strlen($msg));
    }
    return true;
}
?>

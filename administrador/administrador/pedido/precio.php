<?php
// get_precio_plato.php

// Verificar si se proporciona un ID válido
if (isset($_GET['id'])) {
    $platoId = $_GET['id'];

    // Incluir el archivo de conexión a la base de datos
    include "permisos/conexion.php";

    try {
        // Consultar el precio del plato por ID
        $query = $pdo->prepare("SELECT precio FROM platos WHERE id = :id");
        $query->bindParam(':id', $platoId, PDO::PARAM_INT);
        $query->execute();
        $precio = $query->fetchColumn();

        // Devolver el precio como respuesta
        echo $precio;
    } catch (PDOException $e) {
        // Manejar errores en la consulta
        echo "Error en la consulta: " . $e->getMessage();
    }
} else {
    // Manejar la falta de ID en la solicitud
    echo "ID de plato no proporcionado.";
}
?>

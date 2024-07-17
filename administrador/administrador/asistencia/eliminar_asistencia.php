<?php
// eliminar_asistencia.php

// Incluir el archivo de conexión
include '../permisos/conexion.php';

// Iniciar la sesión
session_start();

// Verificar si se ha enviado el ID por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Obtener el ID a eliminar
    $id = intval($_POST['id']);

    try {
        // Preparar la sentencia SQL de eliminación
        $sql = "DELETE FROM asistencia WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta con el parámetro id
        if ($stmt->execute([':id' => $id])) {
            // Configurar el mensaje de éxito en la sesión
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Asistencia eliminada correctamente'
            ];
        } else {
            // Configurar el mensaje de error en la sesión
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Error al eliminar el registro'
            ];
        }

        // Redirigir a la página principal
        header("Location: ../asistencia.php");
        exit;
    } catch (PDOException $e) {
        // Configurar el mensaje de error en la sesión
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ];

        // Redirigir a la página principal
        header("Location: ../asistencia.php");
        exit;
    }
} else {
    // Configurar el mensaje de solicitud no válida en la sesión
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Solicitud no válida'
    ];

    // Redirigir a la página principal
    header("Location: ../asistencia.php");
    exit;
}

// Cerrar la conexión (PDO se cierra automáticamente al final del script)
$pdo = null;
?>

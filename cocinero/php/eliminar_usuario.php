<?php
session_start();
date_default_timezone_set('Europe/Madrid');
// Incluye el archivo de conexión a la base de datos con PDO
include('../administrador/permisos/conexion.php');

// Verifica si se recibió el ID del usuario a eliminar
if (isset($_GET['id'])) {
    $idUsuario = $_GET['id'];

    try {
        // Consulta SQL para eliminar al usuario por su ID
        $sql = "DELETE FROM usuarios WHERE id = :id";

        // Prepara la consulta
        $stmt = $pdo->prepare($sql);

        // Vincula los parámetros
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Éxito: Usuario eliminado correctamente
            $_SESSION['alert'] = array(
                'type' => 'success',
                'message' => 'Usuario eliminado correctamente'
            );
            // Éxito: Usuario eliminado correctamente
            header("Location: ../administrador/agregar.php"); // Redirige de nuevo a la lista de usuarios
            exit;
        } else {
            // Error: No se pudo eliminar al usuario
            echo "Error al eliminar el usuario.";
        }
    } catch (PDOException $e) {
        // Manejo de excepciones en caso de error
        echo "Error en la conexión: " . $e->getMessage();
    }
} else {
    // Error: No se proporcionó el ID del usuario a eliminar
    echo "ID de usuario no proporcionado.";
}

// Cierra la conexión a la base de datos
$pdo = null;
?>
<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include '../permisos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_categoria = $_GET['id'];

    // Verificar que el ID sea un entero válido
    if (!ctype_digit($id_categoria)) {
        echo "Error: ID de categoría no válido";
        exit();
    }

    // Verificar si hay platos asociados a la categoría
    $sql_check = "SELECT COUNT(*) as count FROM platos WHERE id_categoria = :id_categoria";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
    $stmt_check->execute();
    $resultado = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($resultado['count'] > 0) {
        // Si hay platos asociados, enviar un mensaje de error
        $_SESSION['alert'] = array(
            'type' => 'error',
            'message' => 'No se puede eliminar la categoría porque existen platos asociados.'
        );
        header("Location: ../registroCategoria.php");
        exit();
    }

    // Eliminar la categoría de la base de datos
    $sql_delete = "DELETE FROM categorias WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $id_categoria, PDO::PARAM_INT);

    if ($stmt_delete->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Categoría eliminada correctamente'
        );
        header("Location: ../registroCategoria.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['alert'] = array(
            'type' => 'error',
            'message' => 'Error al eliminar la categoría'
        );
        header("Location: ../registroCategoria.php");
        exit();
    }
} else {
    header("Location: ../registroCategoria.php");
    exit();
}
?>

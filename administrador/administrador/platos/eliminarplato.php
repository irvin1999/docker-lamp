<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include '../permisos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_plato = $_GET['id'];

    // Verificar que el ID sea un entero válido
    if (!ctype_digit($id_plato)) {
        echo "Error: ID de plato no válido";
        exit();
    }

    // Obtener la ruta de la imagen antes de eliminar el plato
    $sql_select = "SELECT imagen FROM platos WHERE id = :id";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id', $id_plato, PDO::PARAM_INT);
    $stmt_select->execute();

    $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);
    $ruta_imagen = $resultado['imagen'];

    // Eliminar el plato de la base de datos
    $sql_delete = "DELETE FROM platos WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id', $id_plato, PDO::PARAM_INT);

    if ($stmt_delete->execute()) {
        // Eliminar la imagen del servidor si existe
        if (!empty($ruta_imagen) && file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'plato eliminado correctamente'
        );
        header("Location: ../registroplatos.php");
    } else {
        header("Location: ../registroplatos.php");
    }
} else {
    header("Location: ../registroplatos.php");
}
?>
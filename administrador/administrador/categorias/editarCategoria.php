<?php
include('../permisos/conexion.php');
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

// Verificar si se han enviado los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCategoria = $_POST['id_categoria'];
    $nombre = $_POST['nombre'];

    // Validar los datos
    if (empty($nombre)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'El nombre de la categoría no puede estar vacío.'
        ];
        header("Location: ../editarCategoria.php?id=$idCategoria");
        exit();
    }

    // Actualizar la categoría en la base de datos
    $sql = "UPDATE categorias SET nombre = :nombre WHERE id = :idCategoria";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Categoría actualizada exitosamente.'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al actualizar la categoría.'
        ];
    }
    header("Location: ../editarCategoria.php?id=$idCategoria");
    exit();
}
?>

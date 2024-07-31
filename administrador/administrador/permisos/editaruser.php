<?php
session_start();
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idUsuario = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = strtoupper($_POST['dni']);
    $rol = $_POST['rol'];
    $nuevaContrasena = $_POST['contrasena'];

    try {
        $sqlBase = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, dni = :dni, rol = :rol";

        // Solo actualizar la contraseña si se proporciona una nueva
        if (!empty($nuevaContrasena)) {
            $contrasenaHash = md5($nuevaContrasena);
            $sqlBase .= ", contrasena = :contrasena";
        }

        $sqlBase .= " WHERE id = :id";

        $stmt = $pdo->prepare($sqlBase);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_INT);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        if (!empty($nuevaContrasena)) {
            $stmt->bindParam(':contrasena', $contrasenaHash, PDO::PARAM_STR);
        }

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Usuario editado correctamente'];
        } else {
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'No se realizaron cambios.'];
        }

        header("Location: ../agregar.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Error al editar usuario: ' . $e->getMessage()];
        header("Location: ../agregar.php");
        exit();
    }
} else {
    header("Location: ../agregar.php");
    exit();
}

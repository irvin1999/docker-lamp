<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $idUsuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $rol = $_POST['rol'];
    $dni = $_POST['dni']; // Nuevo campo para el DNI
    $nuevaContrasena = $_POST['nueva_contrasena']; // Agregar esta línea si es necesario

    try {
        // Construir la consulta SQL base
        $sqlBase = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, rol = :rol, dni = :dni";

        // Verificar si se proporcionó una nueva contraseña
        if (!empty($nuevaContrasena)) {
            $contrasenaHash = md5($nuevaContrasena);
            $sql = $sqlBase . ", contrasena = :contrasena WHERE id = :id";
        } else {
            // No se proporcionó una nueva contraseña
            $sql = $sqlBase . " WHERE id = :id";
        }

        // Verificar si el DNI ya está registrado en otro usuario
        $sqlCheckDNI = "SELECT COUNT(*) AS count FROM usuarios WHERE dni = :dni AND id != :id";
        $stmtCheckDNI = $pdo->prepare($sqlCheckDNI);
        $stmtCheckDNI->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmtCheckDNI->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtCheckDNI->execute();
        $row = $stmtCheckDNI->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'];

        if ($count > 0) {
            // El DNI ya está registrado en otro usuario
            $_SESSION['alert'] = array(
                'type' => 'error',
                'message' => 'El DNI ya está registrado en otro usuario'
            );
            header("Location: ../agregar.php");
            exit();
        }

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);

        // Asignar los valores a los parámetros
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_INT);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR); // Asignar el valor del DNI al parámetro
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        // Verificar si se proporcionó una nueva contraseña y asignar el valor correspondiente
        if (!empty($nuevaContrasena)) {
            $stmt->bindParam(':contrasena', $contrasenaHash, PDO::PARAM_STR);
        }

        // Ejecutar la consulta
        $stmt->execute();
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Usuario editado correctamente'
        );
        // Redireccionar a la página de lista de usuarios después de la edición
        header("Location: ../agregar.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo " Código de error: " . $e->getCode();
    }
} else {
    // Si no se envió el formulario de edición, redirigir a la página de lista de usuarios
    header("Location: ../agregar.php");
    exit();
}
?>

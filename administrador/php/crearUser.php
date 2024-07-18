<?php
session_start();
date_default_timezone_set('Europe/Madrid');

include('../administrador/permisos/conexion.php');

// Verificar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los valores del formulario HTML
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
    $dni = isset($_POST['dni']) ? $_POST['dni'] : ''; // Nuevo campo DNI
    $contrasena = isset($_POST['contrasena']) ? md5($_POST['contrasena']) : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    // Convertir el DNI a mayúsculas
    $dni = strtoupper($dni);

    // Verificar que los campos requeridos no estén vacíos
    if (!empty($nombre) && !empty($apellido) && !empty($dni) && !empty($contrasena) && !empty($rol)) {
        // Verificar si el usuario ya existe en la base de datos
        $sql_check_user = "SELECT id FROM usuarios WHERE dni = :dni";
        $stmt_check_user = $pdo->prepare($sql_check_user);
        $stmt_check_user->bindParam(':dni', $dni);
        $stmt_check_user->execute();
        $existing_user = $stmt_check_user->fetch();

        if ($existing_user) {
            // Si el usuario ya existe, mostrar una alerta y salir del script
            $_SESSION['alert'] = array(
                'type' => 'error',
                'message' => 'Ya existe un usuario con el mismo DNI.'
            );
            header("location: ../administrador/agregar.php");
            exit();
        }

        // Crear la consulta SQL para insertar un nuevo usuario en la tabla "usuarios"
        $sql = "INSERT INTO usuarios (nombre, apellido, dni, contrasena, rol) VALUES (:nombre, :apellido, :dni, :contrasena, :rol)";

        try {
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);

            // Vincular los parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':dni', $dni); // Vincular el campo DNI
            $stmt->bindParam(':contrasena', $contrasena);
            $stmt->bindParam(':rol', $rol);

            // Ejecutar la consulta
            $stmt->execute();

            // Después de ejecutar la consulta con éxito
            $_SESSION['alert'] = array(
                'type' => 'success',
                'message' => 'Usuario agregado correctamente'
            );

            // Redirigir al usuario a una página de inicio de sesión
            header("location: ../administrador/agregar.php");
            exit();
        } catch (PDOException $e) {
            // Si la creación falla, mostrar un mensaje de error en la página del formulario de creación de usuario
            echo "Error al crear el usuario: " . $e->getMessage();
        }
    } else {
        echo "Todos los campos son obligatorios. Por favor, completa el formulario.";
    }
} else {
    echo "La solicitud no es válida.";
}

<?php
date_default_timezone_set('Europe/Madrid');

// Incluir el archivo de conexión
include('../administrador/permisos/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni_usuario = strtoupper($_POST["dni"]);
    $password = $_POST["passw"];

    try {
        // Consulta SQL para buscar el usuario por DNI
        $sql = "SELECT u.*, c.nombre as nombre_cargo FROM usuarios u JOIN cargo c ON u.rol = c.id_cargo WHERE u.dni = :dni_usuario";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':dni_usuario', $dni_usuario);
        $stmt->execute();

        // Comprobar si el usuario existe en la base de datos
        if ($stmt->rowCount() > 0) {
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $nombre = $fila["nombre"];
            $password_encriptada = $fila["contrasena"];
            $idusuario = $fila["id"];
            $nombre_cargo = $fila["nombre_cargo"]; // Nombre del cargo

            // Verificar si la contraseña es correcta
            if (md5($password) == $password_encriptada) {
                // Iniciar sesión y redireccionar al usuario según su cargo
                session_start();
                $_SESSION["idusuario"] = $idusuario;
                $_SESSION["usuario"] = $nombre; // Cambiar a nombre del usuario
                $_SESSION["nombre"] = $nombre;
                $_SESSION["rol"] = $nombre_cargo;
                $_SESSION["dni"] = $dni_usuario; // Almacenar el DNI del usuario en la sesión
                if ($fila["activo"] == 0) {
                    // Si el usuario está inactivo, redirigir a una página de error o mostrar un mensaje
                    header("location: ../index.php?error=inactivo");
                } else {
                    // Redireccionar según el cargo del usuario
                    if ($nombre_cargo == 'cocinero') {
                        header("location: ../cocinero/cocinero.php");
                    } else {
                        // Redirigir a una página por defecto o mostrar un mensaje de error
                        header("location: ../index.php?error=permisos");
                    }
                    exit;
                }
            } else {
                // Contraseña incorrecta
                header("location: ../index.php?error=contrasena");
            }
        } else {
            // Usuario no encontrado
            header("location: ../index.php?error=usuario");
        }
    } catch (PDOException $e) {
        // Error en la consulta
        die("Error en la consulta: " . $e->getMessage());
    }
}
?>

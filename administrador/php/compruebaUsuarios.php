<?php
date_default_timezone_set('Europe/Madrid');

// Incluir el archivo de conexión
include ('../administrador/permisos/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni_usuario = strtoupper($_POST["dni"]);
    $password = $_POST["passw"];

    // Verificar si el usuario es el administrador predeterminado
    $usuario_admin = "admin";
    $dni_admin = "admin"; // DNI del administrador predeterminado
    $password_admin = "admin"; // Contraseña del administrador predeterminado

    if ($dni_usuario == $dni_admin && md5($password) == md5($password_admin)) {
        // Iniciar sesión y redireccionar al usuario administrador predeterminado
        session_start();
        $_SESSION["idusuario"] = 1;
        $_SESSION["usuario"] = $usuario_admin;
        $_SESSION["nombre"] = "Administrador";
        $_SESSION["rol"] = "administrador";
        $_SESSION["es_admin_predeterminado"] = true;
        $_SESSION["dni"] = $dni_admin; // Almacenar el DNI del usuario en la sesión
        header("location: ../administrador/inicio.php");
        exit;
    }

    try {
        // Consulta SQL para buscar el usuario por DNI y con rol de administrador
        $sql = "SELECT u.*, c.nombre as nombre_cargo FROM usuarios u JOIN cargo c ON u.rol = c.id_cargo WHERE u.dni = :dni_usuario AND c.nombre = 'administrador'";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':dni_usuario', $dni_usuario);
        $stmt->execute();

        // Comprobar si el usuario existe en la base de datos y tiene el rol de administrador
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
                    if ($nombre_cargo == 'administrador' || ($_SESSION["es_admin_predeterminado"] && $nombre_cargo == 'administrador')) {
                        header("location: ../administrador/inicio.php");
                    }
                }
            } else {
                // Contraseña incorrecta
                header("location: ../index.php?error=contrasena");
            }
        } else {
            // Usuario no encontrado
            header("location: ../index.php?error=permisos");
        }
    } catch (PDOException $e) {
        // Error en la consulta
        die ("Error en la consulta: " . $e->getMessage());
    }
}
?>

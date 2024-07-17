<?php
date_default_timezone_set('Europe/Madrid');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idusuario']) || !isset($_SESSION['dni'])) {
    // Redireccionar al usuario a la página de inicio de sesión si no está autenticado
    header("Location: ../usuarios.php");
    exit(); // Terminar el script
}

// Incluir el archivo de conexión a la base de datos
include '../permisos/conexion.php';

// Verificar si se ha enviado un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener el tipo de registro y la fecha actual
        $tipo_registro = $_POST['radio'];
        $fecha_actual = date("Y-m-d H:i:s");
        $id_usuario = $_SESSION['idusuario']; // Obtener el ID del usuario de la sesión

        // Obtener el DNI del usuario autenticado
        $dni_usuario = $_SESSION['dni']; // Agregar esta línea si tienes el DNI del usuario en la sesión

        // Verificar si el DNI proporcionado en el formulario coincide con el DNI del usuario autenticado
        if ($dni_usuario !== $_POST['codigo']) {
            // Mostrar un mensaje de error y redirigir al usuario
            $_SESSION['alert'] = array(
                'type' => 'error',
                'message' => 'Solo puede registrar su propia asistencia.'
            );
            header("Location: ../usuarios.php");
            exit(); // Terminar el script
        }

        // Verificar si ya hay un registro de asistencia para el usuario en el día actual
        $sql_verificar_asistencia = "SELECT COUNT(*) as count FROM asistencia WHERE id_usuario = :id_usuario AND fecha = CURDATE()";
        $stmt_verificar_asistencia = $pdo->prepare($sql_verificar_asistencia);
        $stmt_verificar_asistencia->bindParam(':id_usuario', $id_usuario);
        $stmt_verificar_asistencia->execute();
        $registro_existente = $stmt_verificar_asistencia->fetch(PDO::FETCH_ASSOC)['count'];

        if ($registro_existente == 0) {
            // No hay registro existente para el usuario en el día actual
            if ($tipo_registro == "entrada") {
                // Insertar un nuevo registro de entrada en la tabla de asistencias
                $sql_registro = "INSERT INTO asistencia (ingreso, fecha, id_usuario) VALUES (:ingreso, :fecha, :id_usuario)";
                $stmt_registro = $pdo->prepare($sql_registro);
                $stmt_registro->bindParam(':ingreso', $fecha_actual);
                $stmt_registro->bindParam(':fecha', $fecha_actual);
                $stmt_registro->bindParam(':id_usuario', $id_usuario);
                $stmt_registro->execute();
                // Después de ejecutar la consulta con éxito
                $_SESSION['alert'] = array(
                    'type' => 'success',
                    'message' => 'Registro de entrada exitoso'
                );
                header("location: ../usuarios.php");
            } elseif ($tipo_registro == "salida") {
                // Aún no ha registrado su entrada para el día actual
                $_SESSION['alert'] = array(
                    'type' => 'error',
                    'message' => 'Primero debe registrar su entrada.'
                );
                header("location: ../usuarios.php");
            }
        } else {
            // Ya hay registro existente para el usuario en el día actual
            if ($tipo_registro == "entrada") {
                // Ya ha registrado su entrada para el día actual
                $_SESSION['alert'] = array(
                    'type' => 'error',
                    'message' => 'Ya ha registrado su entrada hoy.'
                );
                header("location: ../usuarios.php");
            } elseif ($tipo_registro == "salida") {
                // Verificar si ya ha registrado su salida para el día actual
                $sql_verificar_salida = "SELECT COUNT(*) as count FROM asistencia WHERE id_usuario = :id_usuario AND fecha = CURDATE() AND salida IS NOT NULL";
                $stmt_verificar_salida = $pdo->prepare($sql_verificar_salida);
                $stmt_verificar_salida->bindParam(':id_usuario', $id_usuario);
                $stmt_verificar_salida->execute();
                $salida_existente = $stmt_verificar_salida->fetch(PDO::FETCH_ASSOC)['count'];

                if ($salida_existente > 0) {
                    // Ya ha registrado su salida para el día actual
                    $_SESSION['alert'] = array(
                        'type' => 'error',
                        'message' => 'Ya ha registrado su salida hoy.'
                    );
                    header("location: ../usuarios.php");
                    exit;
                } else {
                    // Actualizar el registro de entrada con la salida correspondiente
                    $sql_update_registro = "UPDATE asistencia SET salida=:salida WHERE id_usuario=:id_usuario AND fecha=CURDATE()";
                    $stmt_update_registro = $pdo->prepare($sql_update_registro);
                    $stmt_update_registro->bindParam(':salida', $fecha_actual);
                    $stmt_update_registro->bindParam(':id_usuario', $id_usuario);
                    $stmt_update_registro->execute();
                    // Después de ejecutar la consulta con éxito
                    $_SESSION['alert'] = array(
                        'type' => 'success',
                        'message' => 'Registro de salida exitoso'
                    );

                    header("location: ../usuarios.php");
                }
            }
        }
    } catch (PDOException $e) {
        echo "Error al registrar la asistencia: " . $e->getMessage();
    }
}
?>

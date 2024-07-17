<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include '../permisos/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];

    // Procesar la imagen
    $imagen = $_FILES['imagen'];

    $ruta_imagen = ''; // Inicializar la ruta

    if (!empty($imagen['name'])) {
        // Subir la imagen si se proporciona
        $fecha = date('YmdHis');
        $ruta_imagen = '../../images/' . $fecha . '.jpg';
        move_uploaded_file($imagen['tmp_name'], $ruta_imagen);
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO platos (nombre, id_categoria, precio, imagen) VALUES (:nombre, :id_categoria, :precio, :imagen)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':id_categoria', $categoria);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':imagen', $ruta_imagen);

    if ($stmt->execute()) {
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'plato creado correctamente'
        );
        header("Location: ../registroplatos.php"); // Redireccionar después de registrar
        exit();
    } else {
        echo "Error al registrar el plato.";
    }
}
?>

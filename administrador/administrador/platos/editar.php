<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

include('../permisos/conexion.php');

// Obtener el ID del plato a editar
$idPlatoEditar = isset($_POST['id_plato']) ? $_POST['id_plato'] : null;

if ($idPlatoEditar) {
    // Obtener el plato actual y sus detalles
    $sql = "SELECT * FROM platos WHERE id = :idPlato";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idPlato', $idPlatoEditar, PDO::PARAM_INT);
    $stmt->execute();
    $platoEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener todas las categorías para el formulario
$consultaCategorias = $pdo->query("SELECT * FROM categorias");
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $idPlato = $_POST['id_plato'];
    $nombrePlato = $_POST['nombre'];
    $precioPlato = $_POST['precio'];
    $idCategoria = $_POST['categoria']; // Nueva categoría

    // Verificar si se proporcionó una nueva imagen
    if ($_FILES['imagen']['size'] > 0) {
        $imagen = $_FILES['imagen'];

        // Subir la nueva imagen
        $fecha = date('YmdHis');
        $rutaImagen = '../../images/' . $fecha . '.jpg';
        move_uploaded_file($imagen['tmp_name'], $rutaImagen);
    } else {
        // Si no se proporcionó una nueva imagen, mantener la imagen existente
        $rutaImagen = $_POST['imagen_actual'];
    }

    try {
        // Construir la consulta SQL base
        $sqlBase = "UPDATE platos SET nombre = :nombre, precio = :precio, imagen = :imagen, id_categoria = :idCategoria WHERE id = :id";

        // Preparar la consulta
        $stmt = $pdo->prepare($sqlBase);

        // Asignar los valores a los parámetros
        $stmt->bindParam(':nombre', $nombrePlato, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precioPlato, PDO::PARAM_STR);
        $stmt->bindParam(':imagen', $rutaImagen, PDO::PARAM_STR);
        $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
        $stmt->bindParam(':id', $idPlato, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();
        
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Plato editado correctamente'
        );

        // Redireccionar a la página de lista de platos después de la edición
        header("Location: ../registroplatos.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo " Código de error: " . $e->getCode();
    }
} else {
    // Si no se envió el formulario de edición, redirigir a la página de lista de platos
    header("Location: ../registroplatos.php");
    exit();
}
?>

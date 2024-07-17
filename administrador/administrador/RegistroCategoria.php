<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

// Obtener categorías registradas
include ('permisos/conexion.php');
$consultaCategorias = $pdo->query("SELECT * FROM categorias");
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

// Manejar el registro de nuevas categorías
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];

    // Insertar nueva categoría
    $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);

    if ($stmt->execute()) {
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'Categoría creada correctamente'
        );
        header("Location: categorias.php");
        exit();
    } else {
        echo "Error al registrar la categoría.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Registrar Categorías</title>
    <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
    <link href="../css2/style.css" rel="stylesheet" />
    <link href="../css2/responsive.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <style>
        .form-container {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            position: fixed;
            top: 0px;
            right: -20px;
            width: 300px;
        }
    </style>
</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?>
    </div>
    <div class="container-fluid">
        <div class="row">
            <form action="categorias/crearCategoria.php" method="post" class="col-md-4 p-5 form-container">
                <h1 class="text-center p-3">Agregar Categoría</h1>
                <div class="form-group">
                    <label for="nombre">Nombre de la Categoría:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Registrar Categoría</button>
                </div>
            </form>
            <!-- Sección de la lista de categorías -->
            <div class="col-md-8 p-5">
                <div class="col-12 p-3">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Buscar por nombre de categoría">
                </div>
                <h1 class="text-center p-3">Lista de Categorías</h1>
                <div class="col-12 p-3 form-container">
                    <div class="categorias-container" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?php echo $categoria['nombre']; ?></td>
                                        <td>
                                            <a href="editarCategoria.php?id=<?php echo $categoria['id']; ?>"><img
                                                    src="../images/modificar.png" alt="Editar"></a>
                                            <a href="categorias/eliminarCategoria.php?id=<?php echo $categoria['id']; ?>"><img
                                                    src="../images/eliminar.png" alt="Eliminar"></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (isset($_SESSION['alert'])) {
        $alertType = $_SESSION['alert']['type'];
        $alertMessage = $_SESSION['alert']['message'];
        unset($_SESSION['alert']);

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
        <script>
            Swal.fire({
                icon: '{$alertType}',
                title: '{$alertMessage}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Función para filtrar la tabla de categorías según el texto de búsqueda
            $('#searchInput').on('keyup', function () {
                var searchText = $(this).val().toLowerCase();

                $('.table tbody tr').each(function () {
                    var nombre = $(this).find('td:nth-child(1)').text().toLowerCase();
                    $(this).toggle(nombre.indexOf(searchText) !== -1);
                });
            });
        });
    </script>
    <script src="../js2/bootstrap.js"></script>
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>
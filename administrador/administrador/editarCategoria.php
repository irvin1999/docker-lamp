<?php
include('permisos/conexion.php');

session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

// Obtener el ID de la categoría a editar
$idCategoriaEditar = isset($_GET['id']) ? $_GET['id'] : null;

// Verificar si se proporcionó un ID de categoría
if ($idCategoriaEditar === null) {
    die("ID de categoría a editar no especificado.");
}

// Realizar la consulta SQL para obtener los detalles de la categoría a editar
$sql = "SELECT * FROM categorias WHERE id = :idCategoria";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idCategoria', $idCategoriaEditar, PDO::PARAM_INT);
$stmt->execute();
$categoriaEditar = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener todas las categorías para listarlas
$consultaCategorias = $pdo->query("SELECT * FROM categorias");
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Editar Categorías</title>

    <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <link href="../css2/style.css" rel="stylesheet" />
    <link href="../css2/responsive.css" rel="stylesheet" />

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
    <br />
    <div class="container-fluid row">
        <form action="categorias/editarCategoria.php" method="post" class="col-md-4 p-5 form-container">
            <h1 class="text-center p-3">Editar Categoría</h1>
            <div class="form-group">
                <input type="hidden" name="id_categoria" value="<?php echo $categoriaEditar['id']; ?>">
            </div>
            <div class="form-group">
                <label for="nombre">Nombre de la Categoría:</label>
                <input type="text" id="nombre" class="form-control" name="nombre"
                       value="<?php echo $categoriaEditar['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Guardar Cambios">
            </div>
        </form>

        <!-- Sección de la lista de categorías -->
        <div class="col-md-8 p-5">
            <div class="col-12 p-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre de categoría">
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
                                    <td>
                                        <?php echo $categoria['nombre']; ?>
                                    </td>
                                    <td>
                                        <a href="editarCategoria.php?id=<?php echo $categoria['id']; ?>"><img src="../images/modificar.png" alt="Editar"></a>
                                        <a href="categorias/eliminarCategoria.php?id=<?php echo $categoria['id']; ?>"><img src="../images/eliminar.png" alt="Eliminar"></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_SESSION['alert'])) {
        $alertType = $_SESSION['alert']['type'];
        $alertMessage = $_SESSION['alert']['message'];
        unset($_SESSION['alert']); // Limpiar la sesión para evitar que la alerta se muestre nuevamente

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
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

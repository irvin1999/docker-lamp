<?php
// Verificar si el usuario es administrador predeterminado
function verificarSiEsAdminPredeterminado()
{
    if (isset ($_SESSION["usuario"]) && $_SESSION["usuario"] == "admin" && isset ($_SESSION["es_admin_predeterminado"]) && $_SESSION["es_admin_predeterminado"]) {
        return true;
    }
    return false;
}

session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion = '') {
    header("location: ../index.php");
    die();
}

// Obtener platos registrados
include ('../administrador/permisos/conexion.php');
$consultaPlatos = $pdo->query("SELECT * FROM platos");
$platos = $consultaPlatos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Lista-Platos</title>

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,500,700&display=swap"
        rel="stylesheet">
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
    <!-- Custom styles for this template -->
    <link href="../css2/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="../css2/responsive.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <style>
        .form-container {
            background-color: #f8f9fa;
            /* Color de fondo */
            border: 1px solid #ced4da;
            /* Borde */
            border-radius: 10px;
            /* Bordes redondos */
            padding: 20px;
            /* Espacio interno */
            margin-top: 20px;
            /* Sombra */
            position: fixed;
            /* Posición fija */
            top: 0px;
            /* Espacio superior */
            right: -20px;
            /* Alineado a la derecha */
            width: 300px;
            /* Ancho del formulario */
        }
    </style>
</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include '../administrador/menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center"> <!-- Centra el contenido horizontalmente -->
            <div class="col-10 p-3">
                <div class="col-12 p-3 form-container">
            	<div class="col-12 p-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre de plato">
            	</div>
                <h1 class="text-center p-3">Lista de Platos</h1>
                <div class="platos-container" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($platos as $plato): ?>
                            <tr>
                                <td>
                                    <?php echo $plato['nombre']; ?>
                                </td>
                                <td>$
                                    <?php echo $plato['precio']; ?>
                                </td>
                                <td>
                                    <?php echo '<img src="../../administrador/images' . $plato['imagen'] . '" alt="Plato" width="100" height="100">'; ?>
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
    if (isset ($_SESSION['alert'])) {
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
    <!-- Agrega este código en la sección head de tu página HTML -->
    <script>
        $(document).ready(function () {
            // Función para filtrar la tabla de platos según el texto de búsqueda
            $('#searchInput').on('keyup', function () {
                var searchText = $(this).val().toLowerCase();
                var searchWords = searchText.split(/\s+/); // Dividir el texto de búsqueda por espacios

                $('.table tbody tr').each(function () {
                    var nombre = $(this).find('td:nth-child(1)').text().toLowerCase();
                    var match = true;

                    // Verificar si todas las palabras de búsqueda están presentes en el nombre del plato
                    for (var i = 0; i < searchWords.length; i++) {
                        var word = searchWords[i];
                        if (nombre.indexOf(word) === -1) {
                            match = false;
                            break;
                        }
                    }

                    // Mostrar u ocultar la fila según si hay coincidencias
                    $(this).toggle(match);
                });
            });
        });
    </script>
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

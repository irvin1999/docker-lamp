<?php
// Verificar si el usuario es administrador predeterminado
function verificarSiEsAdminPredeterminado()
{
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"] == "admin" && isset($_SESSION["es_admin_predeterminado"]) && $_SESSION["es_admin_predeterminado"]) {
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

// Obtener registros de asistencia
include ('permisos/conexion.php');
$query = $pdo->prepare("SELECT a.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, u.dni
FROM asistencia a
INNER JOIN usuarios u ON a.id_usuario = u.id");
$query->execute();
$asistencias = $query->fetchAll(PDO::FETCH_ASSOC);

// Función para limpiar la tabla de asistencia
if (isset($_POST['limpiar_tabla'])) {
    // Realizar el TRUNCATE en la tabla de asistencia
    $query = $pdo->prepare("TRUNCATE TABLE asistencia");
    $query->execute();
    // Establecer un mensaje de alerta en la sesión
    $_SESSION['alert'] = array('type' => 'success', 'message' => 'La tabla de asistencia ha sido limpiada exitosamente.');
    // Redireccionar para mostrar la alerta
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

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

    <title>Marcar-Asistencia</title>

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,500,700&display=swap"
        rel="stylesheet">
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />

    <!-- Custom styles for this template -->
    <link href="../css2/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="../css2/responsive.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
    <link rel="stylesheet" href="permisos/estado.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shCk+0U0kLFIz1gWxlpeX2zc+5u90EZ2GJL2n" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="sub_page">
    <div>
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <br />
    <div class="container">
        <div>
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por DNI o NIE">
        </div>
        </br>
        <div class="card">
            <div class="card-header">
                Historial de asistencia
            </div>
            <div class="card-body">
                <?php
                // Mostrar la alerta si está presente en la sesión
                if (isset($_SESSION['alert'])) {
                    $alertType = $_SESSION['alert']['type'];
                    $alertMessage = $_SESSION['alert']['message'];
                    unset($_SESSION['alert']); // Limpiar la sesión para evitar que la alerta se muestre nuevamente

                    echo "
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
		<div class="table-responsive">
    		    <table class="table table-hover">
        		<thead>
            		    <tr>
                		<th scope="col">Ingreso</th>
                		<th scope="col">Salida</th>
                		<th scope="col">Fecha</th>
                		<th scope="col">Nombre y Apellido</th>
                                <th scope="col">DNI O NIE</th>
                		<th scope="col">Acciones</th>
            		    </tr>
        		</thead>
                        <tbody>
                            <?php foreach ($asistencias as $row) { ?>
                                <tr data-dni="<?php echo strtolower($row['dni']); ?>"> <!-- Agregar atributo data-dni -->
                                    <td><?php echo $row['ingreso']; ?></td>
                                    <td><?php echo $row['salida']; ?></td>
                                    <td><?php echo $row['fecha']; ?></td>
                                    <td><?php echo $row['nombre_completo']; ?></td>
                                    <td><?php echo $row['dni']; ?></td>
                                    <td>
                                        <form action="asistencia/eliminar_asistencia.php" method="post">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
    		    </table>
		</div>
                <div class="btn-group">
                    <a href="fpdf/PruebaV.php" id="export-pdf-btn" target="_blank" class="btn btn-primary"
                        style="margin-right: 5px;">
                        <i class="bi bi-file-pdf-fill"></i> Exportar
                    </a>
                    <form method="post">
                        <button id="limpiar-tabla-btn" name="limpiar_tabla" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Limpiar Tabla
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Función para recargar la página cada 5 segundos
        function recargarPagina() {
            location.reload();
        }

        // Llamar a la función para recargar la página cada 5 segundos
        setInterval(recargarPagina, 10000); // Intervalo de 5 segundos (ajustable según sea necesario)
    </script>
    <script>
        $(document).ready(function() {
            // Función para filtrar la tabla de usuarios según el DNI
            $('#searchInput').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();

                $('.table tbody tr').each(function() {
                    var dni = $(this).data('dni'); // Obtener el valor de data-dni
                    var match = dni.indexOf(searchText) !== -1;

                    // Mostrar u ocultar la fila según si hay coincidencias en el DNI
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

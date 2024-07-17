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

// Obtener platos registrados
include ('permisos/conexion.php');
$query = $pdo->prepare("SELECT p.*, s.nombre AS sala, u.nombre, p.observacion 
FROM pedidos p 
INNER JOIN salas s ON p.id_sala = s.id 
INNER JOIN usuarios u ON p.id_usuario = u.id");
$query->execute();
$pedidos = $query->fetchAll(PDO::FETCH_ASSOC);
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

    <title>Historial-Venta</title>

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

    <link rel="stylesheet" href="permisos/estado.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shCk+0U0kLFIz1gWxlpeX2zc+5u90EZ2GJL2n" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Historial pedidos
            </div>
            <div class="card-body">
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Sala</th>
                                <th scope="col">Mesa</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Observación</th>
                                <th scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $row) {
                                $estado = "";
                                if ($row['estado'] == 'PENDIENTE') {
                                    $estado = '<a href="mesas.php?id_sala=' . $row['id_sala'] . '&mesa=' . $row['num_mesa'] . '" class="btn btn-link"><span class="badge badge-warning">Pendiente</span></a>';
                                } elseif ($row['estado'] == 'COMPLETADO') {
                                    $estado = '<span class="badge badge-success">Completado</span>';
                                } elseif ($row['estado'] == 'ELIMINADO') {
                                    $estado = '<span class="badge badge-danger">Eliminado</span>';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['sala']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['num_mesa']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['fecha']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['total']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['nombre']; ?>
                                    </td>
                                    <td style="max-width: 200px; overflow-wrap: break-word;">
                                        <?php echo $row['observacion']; ?>
                                    </td>
                                    <td>
                                        <?php echo $estado; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Función para verificar nuevos pedidos y actualizar la página si es necesario
        function verificarNuevosPedidos() {
            $.ajax({
                url: 'verificarpedido.php',
                type: 'GET',
                success: function (data) {
                    // Comprobar si el total de pedidos ha cambiado
                    var totalPedidosActual = parseInt(data);
                    var totalPedidosAnterior = parseInt('<?php echo count($pedidos); ?>');
                    if (totalPedidosActual !== totalPedidosAnterior) {
                        // Si hay nuevos pedidos, recargar la página
                        location.reload();
                    }
                }
            });
        }

        // Llamar a la función para verificar nuevos pedidos cada cierto tiempo
        setInterval(verificarNuevosPedidos, 5000); // Intervalo de 5 segundos (ajustable según sea necesario)
    </script>
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

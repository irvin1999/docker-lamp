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
include('permisos/conexion.php');
$query = $pdo->prepare("SELECT p.*, s.nombre AS sala, u.nombre, p.observacion 
FROM pedidos p 
INNER JOIN salas s ON p.id_sala = s.id 
INNER JOIN usuarios u ON p.id_usuario = u.id");
$query->execute();
$pedidos = $query->fetchAll(PDO::FETCH_ASSOC);


function limpiarTablaPedidos($pdo)
{
    // Comenzar una transacción
    $pdo->beginTransaction();

    try {
        // Verificar si hay pedidos pendientes
        $query = $pdo->prepare("
            SELECT COUNT(*) AS count
            FROM pedidos
            WHERE estado IN ('COMPLETADO', 'PENDIENTE')
        ");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // Si hay pedidos pendientes, mostrar un aviso y salir de la función
            $_SESSION['alert'] = array('type' => 'warning', 'message' => 'No se puede limpiar la tabla deben estar en estado de COBRADO o ELIMINADO.');
            $pdo->rollBack();
            return false;
        }

        // Eliminar los registros en facturas relacionados con los pedidos
        $query = $pdo->prepare("DELETE FROM facturas WHERE id_pedido IN (SELECT id FROM pedidos)");
        $query->execute();

        // Eliminar registros de la tabla detalle_pedidos
        $query = $pdo->prepare("DELETE FROM detalle_pedidos WHERE id_pedido IN (SELECT id FROM pedidos)");
        $query->execute();

        // Eliminar registros de la tabla de pedidos
        $query = $pdo->prepare("DELETE FROM pedidos");
        $query->execute();

        // Confirmar la transacción
        $pdo->commit();

        // Mostrar un mensaje de éxito
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'La tabla de pedidos ha sido limpiada exitosamente.');
        return true; // Éxito
    } catch (PDOException $e) {
        // Si hay un error, revertir la transacción
        $pdo->rollBack();

        // Mostrar un mensaje de error
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Error al limpiar la tabla de pedidos: ' . $e->getMessage());
        return false; // Error
    }
}



// Variable para almacenar el mensaje
$mensaje = '';

// Verificar si se ha enviado el formulario para limpiar la tabla
if (isset($_POST['limpiar_tabla'])) {
    // Intentar limpiar la tabla de pedidos
    limpiarTablaPedidos($pdo);
    // Redireccionar de nuevo a la página para mostrar el mensaje de alerta
    header("Location: historialventa.php");
    exit();
}

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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,500,700&display=swap" rel="stylesheet">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shCk+0U0kLFIz1gWxlpeX2zc+5u90EZ2GJL2n" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Tu CSS personalizado -->
    <style>
        /* Añadir al CSS de tu proyecto */
        .hero_area {
            position: relative;
            z-index: 10;
            /* Ajusta este valor según tus necesidades */
        }

        .container {
            position: relative;
            z-index: 1;
            /* Asegúrate de que este valor sea menor que el del menú */
        }
    </style>
</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <br />
    <div class="container">
        <!-- Agregar la barra de búsqueda -->
        <div class="col-12 p-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por sala o usuario">
        </div>
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
                                <th scope="col">Factura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $row) {
                                $estado = "";
                                $accion = ""; // Nueva variable para la acción
                                if ($row['estado'] == 'PENDIENTE') {
                                    $estado = '<a href="mesas.php?id_sala=' . $row['id_sala'] . '&mesa=' . $row['num_mesa'] . '" class="btn btn-link"><span class="badge badge-warning">Pendiente</span></a>';
                                } elseif ($row['estado'] == 'COMPLETADO') {
                                    $estado = '<span class="badge badge-success">Completado</span>';
                                    $accion = '<a href="#" class="btn btn-info btn-factura2" data-id="' . $row['id'] . '"><i class="bi bi-file-earmark-text"></i></a>'; // Ícono de documento
                                } elseif ($row['estado'] == 'ELIMINADO') {
                                    $estado = '<span class="badge badge-danger">Eliminado</span>';
                                } elseif ($row['estado'] == 'COBRADO') { // Añadir esto
                                    $estado = '<span class="badge badge-primary">Cobrado</span>';
                                    $accion = '<a href="#" class="btn btn-light btn-factura" data-id="' . $row['id'] . '" data-toggle="modal" data-target="#pdfModal"><i class="bi bi-eye"></i></a>'; // Ícono de ojo
                                }
                            ?>
                                <tr>
                                    <td><?php echo $row['sala']; ?></td>
                                    <td><?php echo $row['num_mesa']; ?></td>
                                    <td><?php echo $row['fecha']; ?></td>
                                    <td><?php echo $row['total']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td style="max-width: 200px; overflow-wrap: break-word;"><?php echo $row['observacion']; ?></td>
                                    <td><?php echo $estado; ?></td>
                                    <td><?php echo $accion; ?></td>
                                    <td>
                                        <form action="pedido/eliminarhistorial.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este pedido?');">
                                            <input type="hidden" name="id_pedido" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                // Verificar si el usuario es administrador o administrador predeterminado
                if ($_SESSION['rol'] == 'administrador' || ($_SESSION['rol'] == 'administrador' && $_SESSION['es_admin_predeterminado'])) {
                    // Si el usuario es administrador o administrador predeterminado, mostrar el botón
                    echo '<form method="post">
                    <button id="limpiar-tabla-btn" name="limpiar_tabla" class="btn btn-danger">
                <i class="bi bi-trash"></i> Limpiar Tabla
                    </button>
              </form>';
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Modal de Detalles de Factura -->
    <div class="modal fade" id="facturaModal" tabindex="-1" aria-labelledby="facturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="facturaModalLabel">Detalles de la Factura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Sección de Factura -->
                    <div id="facturaDetalles">
                        <!-- El contenido de esta sección será reemplazado por JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Ver PDF de la Factura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Mostrar el PDF en un iframe -->
                    <iframe id="pdfFrame" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar detalles de la factura en el modal de detalles
            $('.btn-factura2').on('click', function() {
                var idPedido = $(this).data('id');
                $.ajax({
                    url: 'factura/factura_detalle.php',
                    type: 'GET',
                    data: {
                        id_pedido: idPedido
                    },
                    success: function(response) {
                        $('#facturaDetalles').html(response);
                        var myModal = new bootstrap.Modal(document.getElementById('facturaModal'));
                        myModal.show();
                    },
                    error: function() {
                        $('#facturaDetalles').html('<p>Error al cargar los detalles de la factura.</p>');
                    }
                });
            });

            // Mostrar el PDF en el modal de PDF
            $('.btn-factura').on('click', function() {
                var idPedido = $(this).data('id');
                var pdfUrl = 'RECEIPT-main/factura.php?id_pedido=' + idPedido;
                document.getElementById('pdfFrame').src = pdfUrl;
                var myModal = new bootstrap.Modal(document.getElementById('pdfModal'));
                myModal.show();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Función para filtrar la tabla de pedidos según la Sala o el Usuario
            $('#searchInput').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();

                // Recorrer cada fila de la tabla
                $('.table tbody tr').each(function() {
                    // Obtener el texto de la Sala y el Usuario
                    var sala = $(this).find('td:nth-child(1)').text().toLowerCase();
                    var usuario = $(this).find('td:nth-child(5)').text().toLowerCase();

                    // Verificar si el texto de búsqueda coincide con la Sala o el Usuario
                    var match = sala.indexOf(searchText) !== -1 || usuario.indexOf(searchText) !== -1;

                    // Mostrar u ocultar la fila según si hay coincidencias
                    $(this).toggle(match);
                });
            });
        });
    </script>
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS Bundle (incluye Popper) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>
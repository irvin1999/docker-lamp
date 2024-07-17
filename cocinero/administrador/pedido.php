<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la variable de conexión no está definida e incluir el archivo de conexión
if (!isset($pdo)) {
    include "permisos/conexion.php";
}

if (!isset($_SESSION['rol'])) {
    header('Location: ../index.php');
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['realizar_pedido'])) {
    // Procesar el formulario de pedido

    // Obtener datos del formulario
    $id_sala = $_POST['id_sala'] ?? null;
    $num_mesa = $_POST['mesa'] ?? null;
    $observacion = $_POST['observacion'] ?? '';
    $platosSeleccionados = $_POST['platos'] ?? [];

    // Verificar si se han seleccionado platos
    if (empty($platosSeleccionados)) {
        $_SESSION['alert'] = array(
            'type' => 'danger',
            'message' => 'No se ha seleccionado ningún plato'
        );
        header('Location: mesas.php?id_sala=' . $id_sala . '&mesa=' . $num_mesa);
        exit;
    }
    // Calcular el total del pedido
    $total = 0;
    foreach ($platosSeleccionados as $platoId => $cantidad) {
        // Consultar el precio del plato
        $query = $pdo->prepare("SELECT precio FROM platos WHERE id = :id");
        $query->bindParam(':id', $platoId, PDO::PARAM_INT);
        $query->execute();
        $precioPlato = $query->fetchColumn();

        // Sumar al total
        $total += $precioPlato * $cantidad;
    }

    // Insertar el pedido en la base de datos
    try {
        $pdo->beginTransaction();

        // Insertar el pedido
        $stmtPedido = $pdo->prepare("INSERT INTO pedidos (id_sala, num_mesa, total, observacion, estado, id_usuario) VALUES (:id_sala, :num_mesa, :total, :observacion, 'PENDIENTE', :id_usuario)");
        $stmtPedido->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmtPedido->bindParam(':num_mesa', $num_mesa, PDO::PARAM_INT);
        $stmtPedido->bindParam(':total', $total, PDO::PARAM_STR);
        $stmtPedido->bindParam(':observacion', $observacion, PDO::PARAM_STR);
        $stmtPedido->bindParam(':id_usuario', $_SESSION['idusuario'], PDO::PARAM_INT); // Corregido el nombre de la variable
        $stmtPedido->execute();

        // Obtener el ID del pedido insertado
        $id_pedido = $pdo->lastInsertId();

        // Insertar los detalles del pedido
        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedidos (nombre, precio, cantidad, id_pedido) VALUES (:nombre, :precio, :cantidad, :id_pedido)");
        foreach ($platosSeleccionados as $platoId => $cantidad) {
            // Consultar los datos del plato
            $queryPlato = $pdo->prepare("SELECT nombre, precio FROM platos WHERE id = :id");
            $queryPlato->bindParam(':id', $platoId, PDO::PARAM_INT);
            $queryPlato->execute();
            $plato = $queryPlato->fetch(PDO::FETCH_ASSOC);

            // Insertar detalle del pedido
            $stmtDetalle->bindParam(':nombre', $plato['nombre'], PDO::PARAM_STR);
            $stmtDetalle->bindParam(':precio', $plato['precio'], PDO::PARAM_STR);
            $stmtDetalle->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmtDetalle->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmtDetalle->execute();
        }

        $pdo->commit();
        // Después de ejecutar la consulta con éxito
        $_SESSION['alert'] = array(
            'type' => 'success',
            'message' => 'pedido realizado correctamente'
        );
        // Redirigir o mostrar mensaje de éxito
        header('Location: mesas.php?id_sala=' . $id_sala . '&mesa=' . $num_mesa);
        exit;
    } catch (PDOException $e) {
        // Manejar error
        $pdo->rollBack();
        echo "Error en la transacción: " . $e->getMessage();
    }
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

    <title>Joice</title>

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
    <style>
        .plato-imagen {
            max-width: 100px;
            /* Ajusta el ancho máximo según tus necesidades */
            height: auto;
            display: block;
            margin: 0 auto;
            /* Centra la imagen */
        }

        .plato-detalle {
            text-align: center;
        }
    </style>

</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <div class="container">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Platos
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="id_sala" value="<?php echo $_GET['id_sala'] ?>">
                    <input type="hidden" name="mesa" value="<?php echo $_GET['mesa'] ?>">
                    <input type="hidden" name="total" id="total" value="0.00"> <!-- Campo oculto para el total -->
                    <div class="row">
                        <?php
                        include "permisos/conexion.php";

                        try {
                            $query = $pdo->query("SELECT * FROM platos WHERE estado = 1");
                            $result = $query->rowCount();

                            if ($result > 0) {
                                while ($data = $query->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="col-md-3">
                                        <div class="col-12">
                                            <img src="../images<?php echo $data['imagen']; ?>" class="product-image"
                                                alt="Product Image" style="max-width: 100%; height: auto;">
                                        </div>
                                        <h6 class="my-3">
                                            <?php echo $data['nombre']; ?>
                                        </h6>

                                        <div class="bg-gray py-2 px-3 mt-4">
                                            <h2 class="mb-0">
                                                $
                                                <?php echo $data['precio']; ?>
                                            </h2>
                                        </div>

                                        <div class="mt-4">
                                            <button type="button" class="btn btn-primary btn-block btn-flat addDetalle"
                                                data-id="<?php echo $data['id']; ?>">
                                                <i class="fas fa-cart-plus mr-2"></i>
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                <?php }
                            }
                        } catch (PDOException $e) {
                            echo "Error en la consulta: " . $e->getMessage();
                        }
                        ?>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="observacion">Observaciones</label>
                        <textarea id="observacion" name="observacion" class="form-control" rows="3"
                            placeholder="Observaciones"></textarea>
                    </div>
                    <div id="detalle_pedido"></div>
                    <button type="submit" class="btn btn-primary" name="realizar_pedido">Realizar
                        pedido</button>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <!-- Agrega SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $(".addDetalle").on("click", function (e) {
                e.preventDefault();

                var platoId = $(this).data("id");

                // Buscar el plato en la lista
                var platoExistente = $("#detalle_pedido").find(".cantidad-input[data-id='" + platoId + "']");
                if (platoExistente.length > 0) {
                    // Incrementar la cantidad del plato existente
                    var cantidadInput = platoExistente;
                    cantidadInput.val(parseInt(cantidadInput.val()) + 1);
                } else {
                    // Obtener datos del plato
                    var platoNombre = $(this).closest(".col-md-3").find("h6").text();
                    var platoPrecio = parseFloat($(this).closest(".col-md-3").find(".bg-gray h2").text().replace('$', ''));
                    var platoImagen = $(this).closest(".col-md-3").find("img").attr("src");

                    // Agregar el plato a la lista
                    var detalleHtml = '<div class="row">';
                    detalleHtml += '<div class="col-md-3">';
                    detalleHtml += '<img src="' + platoImagen + '" alt="Plato Image" class="plato-imagen">';
                    detalleHtml += '</div>';
                    detalleHtml += '<div class="col-md-6">';
                    detalleHtml += '<h6 class="my-3">' + platoNombre + '</h6>';
                    detalleHtml += '<div class="bg-gray py-2 px-3 mt-4">';
                    detalleHtml += '<h2 class="mb-0">$' + platoPrecio.toFixed(2) + '</h2>';
                    detalleHtml += '</div>';
                    detalleHtml += '</div>';
                    detalleHtml += '<div class="col-md-3">';
                    detalleHtml += '<input type="number" class="form-control cantidad-input" data-id="' + platoId + '" name="platos[' + platoId + ']" placeholder="Cantidad" min="1" value="1"></br>';
                    detalleHtml += '<button class="btn btn-danger btn-sm eliminar-plato" data-id="' + platoId + '">Eliminar</button>';
                    detalleHtml += '</div>';
                    detalleHtml += '</div>';

                    $("#detalle_pedido").append(detalleHtml);
                }

                // Actualizar el total al agregar un nuevo plato
                actualizarTotal();
            });

            // Agrega la lógica para eliminar un plato
            $("#detalle_pedido").on("click", ".eliminar-plato", function () {
                $(this).closest(".row").remove();

                // Actualizar el total al eliminar un plato
                actualizarTotal();
            });

            // Función para actualizar el total
            function actualizarTotal() {
                var total = 0;

                // Recorrer los platos en la lista
                $(".cantidad-input").each(function () {
                    var platoId = $(this).data("id");
                    var cantidad = parseInt($(this).val());

                    // Consultar el precio del plato
                    var query = $.ajax({
                        url: "precio.php", // Cambia esto al archivo que obtiene el precio del plato por ID
                        method: "GET",
                        data: { id: platoId },
                        async: false
                    });

                    query.done(function (precio) {
                        total += cantidad * parseFloat(precio);
                    });
                });

                // Actualizar el valor del campo total en el formulario
                $("#total").val(total.toFixed(2));
            }
        });
    </script>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

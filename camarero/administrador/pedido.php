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

    // Verificar si el usuario actual es el administrador predeterminado
    if ($_SESSION["usuario"] == "admin" && $_SESSION["es_admin_predeterminado"]) {
        // Manejar el caso especial del administrador predeterminado
        $_SESSION['alert'] = array(
            'type' => 'danger',
            'message' => 'El administrador predeterminado no puede realizar pedidos.'
        );
        header('Location: mesas.php?id_sala=' . $id_sala . '&mesa=' . $num_mesa);
        exit;
    }

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

    <title>Pedido</title>

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

    #menu-categorias {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .categoria-btn {
        flex: 1;
        text-align: center;
        margin: 0 5px;
    }

    .plato-imagen {
        max-width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .plato-detalle {
        text-align: center;
    }
    </style>

</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?>
        <!-- Incluir el menú -->
    </div>
    </br>
    <div class="container">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Platos
                </h3>
            </div>
            <div class="card-body">
                <!-- Menú de categorías -->
                <div class="mb-3">
                    <h5>Selecciona una categoría:</h5>
                    <div id="menu-categorias" class="btn-group btn-group-toggle" data-toggle="buttons">
                        <?php
                    $queryCategorias = $pdo->query("SELECT * FROM categorias");
                    while ($categoria = $queryCategorias->fetch(PDO::FETCH_ASSOC)) {
                        echo '<label class="btn btn-secondary">
                                <input type="radio" name="categoria" class="categoria-btn" data-id="' . $categoria['id'] . '"> ' . $categoria['nombre'] . '
                              </label>';
                    }
                    ?>
                    </div>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="id_sala" value="<?php echo $_GET['id_sala'] ?>">
                    <input type="hidden" name="mesa" value="<?php echo $_GET['mesa'] ?>">
                    <input type="hidden" name="total" id="total" value="0.00"> <!-- Campo oculto para el total -->
                    <!-- Contenedor de platos, inicialmente vacío -->
                    <div id="platos-container" class="row mt-4">
                        <!-- Los platos se cargarán aquí con JavaScript -->
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
    <script type="text/javascript">
    $(document).ready(function() {
        // Cargar platos al seleccionar una categoría
        $(".categoria-btn").on("change", function() {
            var categoriaId = $(this).data("id");

            // Limpiar el contenedor de platos
            $("#platos-container").empty();

            // Obtener platos de la categoría seleccionada
            $.ajax({
                url: "pedido/get_platos.php", // Archivo que devolverá los platos por categoría
                method: "POST",
                data: {
                    id_categoria: categoriaId
                },
                success: function(data) {
                    $("#platos-container").html(data);
                }
            });
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $(".categoria-btn").on("change", function() {
            var categoriaId = $(this).data("id");

            // Mostrar solo los platos que pertenecen a la categoría seleccionada
            $(".plato-item").each(function() {
                if ($(this).data("categoria") == categoriaId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>
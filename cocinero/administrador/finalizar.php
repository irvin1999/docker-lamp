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

        <title>Finalizar-Pedido</title>

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

        <link rel="stylesheet" href="../css/eyes.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
            integrity="sha384-xrRvR7L2CTEn8e4zB1mgPx1iJl3iL+4U3IJKpx5foMz9ATbQ5PzQDMEzfuGbe5KG" crossorigin="anonymous">
    </head>

    <body class="sub_page">
        <div class="hero_area">
            <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
        </div>
        <br />
        <div class="container">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Platos
                    </h3>
                </div>
                <div class="card-body">
                    <input type="hidden" id="id_sala" value="<?php echo $_GET['id_sala']; ?>">
                    <input type="hidden" id="mesa" value="<?php echo $_GET['mesa']; ?>">
                    <div class="row">
                        <?php
                        include "permisos/conexion.php";
                        try {
                            $consulta = $pdo->prepare("SELECT * FROM pedidos WHERE id_sala = :id_sala AND num_mesa = :mesa AND estado = 'PENDIENTE'");
                            $consulta->bindParam(":id_sala", $_GET['id_sala'], PDO::PARAM_INT);
                            $consulta->bindParam(":mesa", $_GET['mesa'], PDO::PARAM_INT);
                            $consulta->execute();
                            $result = $consulta->fetch(PDO::FETCH_ASSOC);

                            if (!empty($result)) {
                                ?>
                                <div class="col-md-12 text-center">
                                    <div class="col-12">
                                        Fecha:
                                        <?php echo $result['fecha']; ?>
                                        <hr>
                                        Mesa:
                                        <?php echo $_GET['mesa']; ?>
                                    </div>

                                    <div class="bg-gray py-2 px-3 mt-4">
                                        <h2 class="mb-0">
                                            $
                                            <?php echo $result['total']; ?>
                                        </h2>
                                    </div>
                                    <hr>
                                    <h3>Platos</h3>
                                    <div class="row">
                                        <?php
                                        $id_pedido = $result['id'];
                                        $consultaPlatos = $pdo->prepare("SELECT * FROM detalle_pedidos WHERE id_pedido = :id_pedido");
                                        $consultaPlatos->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);
                                        $consultaPlatos->execute();

                                        while ($data1 = $consultaPlatos->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <div class="col-md-4 card card-widget widget-user">
                                                <div class="widget-user-header bg-warning">
                                                    <h3 class="widget-user-username">Precio</h3>
                                                    <h5 class="widget-user-desc">
                                                        <?php
                                                        // Multiplica el precio por la cantidad
                                                        $precio_total = $data1['precio'] * $data1['cantidad'];
                                                        echo $precio_total;
                                                        ?>
                                                    </h5>
                                                </div>
                                                <div class="widget-user-image">
                                                    <img class="img-circle elevation-2 img-fluid" src="../images/mesa.jpg"
                                                        alt="User Avatar">
                                                </div>
                                                <div class="card-footer">
                                                    <div class="description-block">
                                                        <span>
                                                            <?php echo $data1['nombre']; ?> - Cantidad:
                                                            <?php echo $data1['cantidad']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mt-4">
                                        <a class="btn btn-primary btn-block btn-flat finalizarPedido"
                                            href="finalizar_pedido.php?id_pedido=<?php echo $id_pedido; ?>&id_sala=<?php echo $_GET['id_sala']; ?>&mesa=<?php echo $_GET['mesa']; ?>">
                                            <i class="fas fa-cart-plus mr-2"></i>
                                            Finalizar
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                        } catch (PDOException $e) {
                            echo "Error en la consulta: " . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="../js2/bootstrap.js"></script>
	</br>
        <?php include '../footer/footer.php'; ?>
</body>

</html>

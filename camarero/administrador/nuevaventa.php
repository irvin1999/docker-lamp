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

        <title>Venta</title>

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
            <?php
            include_once "menuadmin.php";
            ?>
        </div>
        <br/>
        <div class="container">
            <div class="card">
                <div class="card-header text-center">
                    Salas
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        // Utilizar la variable $pdo en tus consultas
                        try {
                            $query = $pdo->query("SELECT * FROM salas WHERE estado = 1");
                            $result = $query->rowCount();
                            if ($result > 0) {
                                while ($data = $query->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <div class="col-md-3 shadow-lg">
                                        <div class="text-center">
                                            <img src="../images/salas.jpg" class="product-image img-fluid" alt="Product Image"
                                                style="max-width: 100%;">
                                        </div>
                                        <h6 class="my-3 text-center"><span class="badge badge-info">
                                                <?php echo $data['nombre']; ?>
                                            </span></h6>

                                        <div class="mt-4">
                                            <a class="btn btn-primary btn-block btn-flat"
                                                href="mesas.php?id_sala=<?php echo $data['id']; ?>&mesas=<?php echo $data['mesas']; ?>">
                                                <img src="../images/eye-solid.svg" alt="Nueva Venta" class="eye-icon mr-2">
                                                Nueva Venta
                                            </a>
                                            </br>
                                        </div>
                                    </div>
                                <?php }
                            }
                        } catch (PDOException $e) {
                            echo "Error en la consulta: " . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="../js2/bootstrap.js"></script>
	</br>
        <?php include '../footer/footer.php'; ?>
</body>

</html>

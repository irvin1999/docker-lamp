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

    <title>Mesas</title>

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

    <!-- Agrega SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        // Espera a que el DOM esté completamente cargado
        document.addEventListener("DOMContentLoaded", function () {
            // Obtén todos los elementos con la clase 'atender-btn' y 'finalizar-btn'
            var atenderButtons = document.querySelectorAll('.atender-btn');
            var finalizarButtons = document.querySelectorAll('.finalizar-btn');

            // Itera sobre los botones de 'Atender' y agrega un listener
            atenderButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Aquí puedes agregar lógica adicional si es necesario
                    console.log('Atendiendo a mesa ' + button.getAttribute('data-num-mesa'));
                });
            });

            // Itera sobre los botones de 'Finalizar' y agrega un listener
            finalizarButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Aquí puedes agregar lógica adicional si es necesario
                    console.log('Finalizando pedido de mesa ' + button.getAttribute('data-num-mesa'));

                    // Ejemplo de alerta usando SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Pedido Finalizado',
                        text: 'El pedido de la mesa ' + button.getAttribute('data-num-mesa') + ' ha sido finalizado con éxito.'
                    });
                });
            });
        });
    </script>
</head>

<body class="sub_page">
    <div class="hero_area">
        <?php
        include_once "menuadmin.php";
        ?>
    </div>
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                Mesas
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    include "permisos/conexion.php";
                    try {
                        // Obtener el valor de id de la sala desde la URL
                        $id = isset($_GET['id_sala']) ? $_GET['id_sala'] : null;

                        $query = $pdo->prepare("SELECT * FROM salas WHERE id = :id");
                        $query->bindParam(":id", $id, PDO::PARAM_INT);
                        $query->execute();
                        $result = $query->rowCount();

                        if ($result > 0) {
                            $data = $query->fetch(PDO::FETCH_ASSOC);

                            // Obtener el número de mesas desde la base de datos
                            $mesas = $data['mesas'];

                            if ($mesas > 0) {
                                $item = 1;  // Inicializar $item aquí
                                ?>
                                <div class="row">
                                    <?php
                                    for ($i = 0; $i < $mesas; $i++) {
                                        $consulta = $pdo->prepare("SELECT * FROM pedidos WHERE id_sala = :id_sala AND num_mesa = :num_mesa AND estado = 'PENDIENTE'");
                                        $consulta->bindParam(":id_sala", $id, PDO::PARAM_INT);
                                        $consulta->bindParam(":num_mesa", $item, PDO::PARAM_INT);
                                        $consulta->execute();
                                        $resultPedido = $consulta->fetch(PDO::FETCH_ASSOC);
                                        // Obtener el rol del usuario de la sesión
                                        $rolUsuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
                                        ?>
                                        <div class="col-md-3 ">
                                            <div class="card card-widget widget-user">
                                                <div <?php echo empty($resultPedido) ? 'success' : 'danger'; ?>>
                                                    <h3 class="card-header text-center">MESA
                                                        <?php echo $item; ?>
                                                    </h3>
                                                </div>
                                                <div class="widget-user-image" style="max-width: 100%;">
                                                    <img class="img-circle elevation-2" src="../images/mesa.jpg" alt="User Avatar"
                                                        style="max-width: 100%;">
                                                </div>
                                                <div class="card-footer">
                                                    <div class="text-center">
                                                        <h6 class="my-3 text-center"><span class="badge badge-info">
                                                                <?php echo $data['nombre']; ?>
                                                            </span></h6>
                                                        <div class="mt-4">
                                                            <?php if (empty($resultPedido) && ($rolUsuario == 'camarero' || $rolUsuario == 'administrador')): ?>
                                                                <a class="btn btn-outline-info btn-block btn-flat atender-btn"
                                                                    data-id-sala="<?php echo $id; ?>" data-num-mesa="<?php echo $item; ?>"
                                                                    href="pedido.php?id_sala=<?php echo $id; ?>&mesa=<?php echo $item; ?>">
                                                                    Atender
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($resultPedido) && ($rolUsuario == 'camarero' || $rolUsuario == 'administrador')): ?>
                                                                <a class="btn btn-outline-danger btn-block btn-flat finalizar-btn"
                                                                    data-id-sala="<?php echo $id; ?>" data-num-mesa="<?php echo $item; ?>"
                                                                    href="verpedido.php?id_sala=<?php echo $id; ?>&mesa=<?php echo $item; ?>">
                                                                    Ver pedido
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($resultPedido) && ($rolUsuario == 'cocinero' || $rolUsuario == 'administrador')): ?>
                                                                <a class="btn btn-outline-danger btn-block btn-flat finalizar-btn"
                                                                    data-id-sala="<?php echo $id; ?>" data-num-mesa="<?php echo $item; ?>"
                                                                    href="finalizar.php?id_sala=<?php echo $id; ?>&mesa=<?php echo $item; ?>">
                                                                    Finalizar
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    </br>
                                                </div>
                                            </div>
                                            </br>
                                        </div>
                                        </br>
                                        <?php
                                        if ($item % 10 == 0) {
                                            // Cerrar la fila actual y comenzar una nueva después de 10 mesas
                                            echo '</div><div class="row">';
                                        }
                                        $item++;  // Incrementar $item dentro del bucle
                                    }
                                    ?>
                                </div>
                                </br>
                                <?php
                            } else {
                                echo "<p>No hay mesas disponibles en esta sala.</p>";
                            }
                        }
                    } catch (PDOException $e) {
                        echo "Error en la consulta: " . $e->getMessage();
                    }
                    ?>
                </div>
                </br>
            </div>
            </br>
        </div>
        </br>
    </div>
    </br>
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
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

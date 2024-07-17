<?php
function verificarSiEsAdminPredeterminado()
{
    // Verificar si el usuario es el administrador predeterminado
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"] == "admin" && isset($_SESSION["es_admin_predeterminado"]) && $_SESSION["es_admin_predeterminado"]) {
        return true;
    }

    return false;
}

session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
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

    <title>Asistencia-Usuarios</title>

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <!-- Custom styles -->
    <style>
        .masthead-content {
            padding: 20px;
        }

        .reloj p {
            display: inline;
            font-size: 2em;
            margin: 0;
        }

        .text-black {
            color: black;
        }

        .radio-container {
            display: flex;
            justify-content: center;
            /* Centra los elementos horizontalmente */
        }

        .radio-group {
            display: flex;
            flex-direction: row;
            /* Cambiar a fila en lugar de columna */
        }

        .radio-group label {
            display: inline-block;
            margin-right: 20px;
            /* Espacio entre los botones */
            cursor: pointer;
        }

        .radio-group label span {
            margin-left: 5px;
        }

        .radio-group input[type="radio"] {
            display: none;
        }

        .radio-group input[type="radio"]+span {
            padding: 8px 12px;
            border: 2px solid #007bff;
            border-radius: 5px;
            color: #007bff;
        }

        .radio-group input[type="radio"]:checked+span {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>

<body class="sub_page ">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-4">
                <div class="masthead-content text-center">
                    <div class="mb-4">
                        <p id="fechaHora" class="fechaHora text-black"></p>
                    </div>
                    <h1 class="text-black">Sistema de registro de asistencia</h1>
                    <p class="mb-5 text-black">Entradas y salidas de las personas</p>

                    <form id="contactForm" autocomplete="off" action="asistencia/registroasistencia.php" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="codigo" name="codigo"
                                placeholder="Ingrese DNI/NIE" aria-label="Ingrese código"
                                aria-describedby="submitButton">
                            <button class="btn btn-primary" type="submit" id="submitButton">Registrar</button>
                        </div>
                        <div class="radio-container">
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="radio" value="entrada" checked />
                                    <span>Entrada</span>
                                </label>
                                <label>
                                    <input type="radio" name="radio" value="salida" />
                                    <span>Salida</span>
                                </label>
                            </div>
                        </div>
                    </form>

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
    <!-- Script para el reloj -->
    <script>
        function actualizarHoraYFecha() {
            var ahora = new Date();
            var diaSemana = ahora.toLocaleDateString('es-ES', { weekday: 'long' });
            var dia = ahora.getDate();
            var mes = ahora.toLocaleDateString('es-ES', { month: 'long' });
            var year = ahora.getFullYear();
            var horas = ahora.getHours();
            var minutos = ahora.getMinutes();
            var segundos = ahora.getSeconds();
            var ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12;
            horas = horas ? horas : 12;
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;
            var horaMinutosSegundos = horas + ':' + minutos + ':' + segundos + ' ' + ampm;
            var fechaHora = diaSemana + ' ' + dia + ' de ' + mes + ' del ' + year + ' ' + horaMinutosSegundos;
            document.getElementById("fechaHora").innerText = fechaHora;
        }
        setInterval(actualizarHoraYFecha, 1000);
        actualizarHoraYFecha();
    </script>
    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <?php include '../footer/footer.php'; ?>
</body>

</html>

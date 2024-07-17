<?php
date_default_timezone_set('Europe/Madrid');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica si la sesión no está iniciada y redirige a la página de inicio de sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
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
    <title>Joice</title>
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
</head>

<body>
    <div class="hero_area">
        <header class="header_section">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg custom_nav-container ">
                    <a class="navbar-brand" href="../administrador/inicio.php">
                        <img src="../images/logoempresa.png" alt="" />
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars-rs-food"
                        aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbars-rs-food">
                        <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
                            <ul class="navbar-nav">
                                <?php
                                if ($_SESSION['rol'] == 'administrador') {
                                    echo '<li class="nav-item">
                                    <a class="nav-link" href="../administrador/inicio.php">Inicio</a>
                                </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'camarero') {
                                    echo '<li class="nav-item">
                                    <a class="nav-link" href="../camarero/camarero.php">Inicio</a>
                                </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'cocinero') {
                                    echo '<li class="nav-item">
                                    <a class="nav-link" href="../cocinero/cocinero.php">Inicio</a>
                                </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'administrador') {
                                    echo '<li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Gestion</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="registroplatos.php">Registrar platos</a></li>
                                                <li><a class="dropdown-item" href="registrosalas.php">Registrar salas</a></li>
                                                <li><a class="dropdown-item" href="agregar.php">Registrar usuarios</a></li>
                                                <li><a class="dropdown-item" href="RegistroCategoria.php">Registrar Categorias</a></li>
                                            </ul>
                                          </li>';
                                }
                                ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                                        aria-expanded="false">Salas</a>
                                    <ul class="dropdown-menu">
                                        <?php
                                        if ($_SESSION['rol'] == 'administrador') {
                                            echo '<li><a class="dropdown-item" href="nuevaventa.php">Realizar venta</a></li>';
                                        }
                                        if ($_SESSION['rol'] == 'camarero' || $_SESSION['rol'] == 'cocinero') {
                                            echo '<li><a class="dropdown-item" href="../administrador/nuevaventa.php">Ver mesas</a></li>';
                                        }
                                        if ($_SESSION['rol'] == 'administrador' || $_SESSION['rol'] == 'cocinero' || $_SESSION['rol'] == 'camarero') {
                                            echo '<li><a class="dropdown-item" href="../administrador/historialventa.php">Historial pedidos</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                                if ($_SESSION['rol'] == 'camarero') {
                                    echo '<li class="nav-item">
                                            <a class="nav-link" href="../camarero/ListaPlatos.php">Platillos</a>
                                          </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'cocinero') {
                                    echo '<li class="nav-item">
                                            <a class="nav-link" href="../cocinero/ListaPlatos.php">Platillos</a>
                                          </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'administrador' && !isset($_SESSION['es_admin_predeterminado'])) {
                                    // Si el usuario es un administrador y no es un administrador predeterminado, mostrar el enlace "Registrar asistencia"
                                    echo '<li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Asistencia</a>
                                            <ul class="dropdown-menu">
                                          <li><a class="dropdown-item" href="asistencia.php">ver asistencias</a></li>
                                          <li><a class="dropdown-item" href="usuarios.php">Registrar asistencia</a></li>
                                            </ul>
                                          </li>';
                                } elseif ($_SESSION['rol'] == 'administrador') {
                                    // Si el usuario es un administrador predeterminado, mostrar solo el enlace "Ver asistencias"
                                    echo '<li class="nav-item">
                                                <a class="nav-link" href="asistencia.php">ver asistencias</a>
                                          </li>';
                                }
                                ?>

                                <?php
                                if ($_SESSION['rol'] == 'camarero' || $_SESSION['rol'] == 'cocinero') {
                                    echo '<li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">Asistencia</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="../administrador/usuarios.php">Regristrar asistencia</a></li>
                                            </ul>
                                          </li>';
                                }
                                ?>
                                <?php
                                if ($_SESSION['rol'] == 'camarero' || $_SESSION['rol'] == 'cocinero') {
                                    echo '<li class="nav-item">
                                            <a class="nav-link" href="../administrador/perfil.php">perfil</a>
                                          </li>';
                                }
                                ?>
                                <?php
                                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador' && !isset($_SESSION['es_admin_predeterminado'])) {
                                    // El usuario es un administrador registrado, permitir acceso al perfil
                                    echo '<li class="nav-item">
                                            <a class="nav-link" href="perfil.php">perfil</a>
                                          </li>';
                                }
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="../php/cerrarsesion.php">Cerrar sesión</a>
                                </li>
                                <li class="nav-item">
                                    <span class="nav-link">
                                        Nombre:
                                        <?php echo $_SESSION['nombre']; ?>
                                    </span>
                                </li>
                                <li class="nav-item">
                                    <span class="nav-link">
                                        Rol:
                                        <?php echo $_SESSION['rol']; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

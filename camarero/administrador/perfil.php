<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    header("location: ../index.php");
    die();
}

include('permisos/conexion.php');

// Obtener el ID del usuario que ha iniciado sesión
$idUsuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null;

// Verificar si se proporcionó un ID de usuario
if ($idUsuario === null) {
    // Manejar el caso en que no se proporcionó un ID de usuario
    die("ID de usuario no especificado.");
}

// Realizar la consulta SQL para obtener los detalles del usuario logeado
$sql = "SELECT * FROM usuarios WHERE id = :idUsuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar el formulario de edición si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $dni = $_POST["dni"];
    $contrasena = $_POST["contrasena"];

    // Convertir el DNI a mayúsculas
    $dni = strtoupper($dni);
    
    // Verificar si el DNI ya existe en la base de datos
    $sql_check_dni = "SELECT COUNT(*) AS count FROM usuarios WHERE dni = :dni AND id != :idUsuario";
    $stmt_check_dni = $pdo->prepare($sql_check_dni);
    $stmt_check_dni->bindParam(':dni', $dni);
    $stmt_check_dni->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt_check_dni->execute();
    $dni_exists = $stmt_check_dni->fetch(PDO::FETCH_ASSOC)['count'];

    if ($dni_exists) {
        // Si el DNI ya existe, configurar una alerta de error y detener la ejecución
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Ya existe un usuario con ese DNI.');
        header("location: $_SERVER[PHP_SELF]");
        exit();
    }

    // Actualizar los datos del usuario en la base de datos
    $sql_update = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, dni = :dni";

    // Si se proporcionó una nueva contraseña, actualizarla también
    if (!empty($contrasena)) {
        $sql_update .= ", contrasena = :contrasena";
    }

    $sql_update .= " WHERE id = :idUsuario";

    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':nombre', $nombre);
    $stmt_update->bindParam(':apellido', $apellido);
    $stmt_update->bindParam(':dni', $dni);
    $stmt_update->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

    // Si se proporcionó una nueva contraseña, encriptarla y vincularla
    if (!empty($contrasena)) {
        $contrasena_encriptada = md5($contrasena);
        $stmt_update->bindParam(':contrasena', $contrasena_encriptada);
    }

    if ($stmt_update->execute()) {
        // Actualización exitosa, configurar una alerta de éxito
        $_SESSION['alert'] = array('type' => 'success', 'message' => 'Perfil actualizado correctamente.');
    } else {
        // Error al actualizar, configurar una alerta de error
        $_SESSION['alert'] = array('type' => 'error', 'message' => 'Error al actualizar el perfil.');
    }

    // Redirigir de vuelta al mismo archivo para evitar reenvío de formularios
    header("location: $_SERVER[PHP_SELF]");
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

    <title>Perfil</title>

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,500,700&display=swap" rel="stylesheet">
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />

    <!-- Custom styles for this template -->
    <link href="../css2/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="../css2/responsive.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
    <link rel="stylesheet" href="permisos/estado.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">

</head>

<body class="sub_page">
    <div class="hero_area">
        <?php include 'menuadmin.php'; ?> <!-- Incluir el menú -->
    </div>
    </br>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Perfil del Usuario
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Apellido</th>
                                        <th scope="col">DNI o NIE</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $usuario['nombre']; ?></td>
                                        <td><?php echo $usuario['apellido']; ?></td>
                                        <td><?php echo $usuario['dni']; ?></td>
                                        <td><button href="#" class="btn btn-primary btn-editar">Editar</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </br>
    <div class="container">
        <div id="formulario-editar" style="display: none;">
            <div class="card">
                <div class="card-header">
                    Editar Usuario
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" class="form-control" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" id="apellido" class="form-control" name="apellido" value="<?php echo $usuario['apellido']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="dni" class="form-label">DNI/NIE:</label>
                            <input type="text" id="dni" class="form-control" name="dni" value="<?php echo $usuario['dni']; ?>" pattern="[XYZxyz]?\d{7}[TRWAGMYFPDXBNJZSQVHLCKEtrwagmyfpdxbnjzsqvhlcke]|[a-zA-Z]?\d{8}[a-zA-Z]" title="Por favor, introduce un DNI o NIE válido." required maxlength="10">
                            <small class="form-text">El DNI debe contener una letra seguida de 7 números y otra letra al
                                final. El NIE debe contener una letra (X, Y o Z) opcional, seguida de 7 números y una
                                letra
                                al final.</small>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Nueva Contraseña:</label>
                            <input type="password" id="contrasena" class="form-control" name="contrasena" placeholder="Nueva contraseña">
                        </div>
                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js2/bootstrap.js"></script>
    <script>
        $(document).ready(function() {
            $(".btn-editar").click(function() {
                $("#formulario-editar").toggle();
            });
        });
    </script>
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
    </br>
    <?php include '../footer/footer.php'; ?>
</body>

</html>
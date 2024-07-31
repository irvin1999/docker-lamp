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

  <title>Registrar-Usuarios</title>

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

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <style>
    .form-container {
      background-color: #f8f9fa;
      /* Color de fondo */
      border: 1px solid #ced4da;
      /* Borde */
      border-radius: 10px;
      /* Bordes redondos */
      padding: 20px;
      /* Espacio interno */
      margin-top: 20px;

      position: fixed;
      /* Posición fija */
      top: 0px;
      /* Espacio superior */
      right: -20px;
      /* Alineado a la derecha */
      width: 300px;
      /* Ancho del formulario */
    }

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
  <div class="container-fluid row">
    <?php
    include('permisos/conexion.php');

    // Crear la consulta SQL para obtener los usuarios y sus roles
    $sql = "SELECT usuarios.id, usuarios.nombre, usuarios.apellido, usuarios.dni, cargo.nombre AS rol_nombre
    FROM usuarios
    INNER JOIN cargo ON usuarios.rol = cargo.id_cargo";

    // Preparar la consulta con PDO
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    ?>
    <div class="col-md-12 p-5">
      <!-- Barra de búsqueda -->
      <div class="col-12 p-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por DNI o NIE">
      </div>
      <h1 class="text-center p-3">Lista de Usuarios</h1>
      <div class="col-12 p-3 form-container">
        <div class="platos-container" style="max-height: 400px; overflow-y: auto;">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">DNI o NIE</th>
                <th scope="col">Rol</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Obtener el ID del usuario que ha iniciado sesión desde la variable de sesión
              $userEnSesion = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
              $esAdminPredeterminado = verificarSiEsAdminPredeterminado(); // Debes implementar esta función

              while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $fila['nombre'] . "</td>";
                echo "<td>" . $fila['apellido'] . "</td>";
                echo "<td>" . $fila['dni'] . "</td>";
                echo "<td>" . $fila['rol_nombre'] . "</td>";

                // Botones de modificar y eliminar
                echo "<td>";

                if ($esAdminPredeterminado || $userEnSesion != $fila['id']) {
                  // Muestra el botón de eliminar solo si es el administrador predeterminado o no es el usuario en sesión
                  if ($esAdminPredeterminado || ($fila['rol_nombre'] == 'camarero' || $fila['rol_nombre'] == 'cocinero')) {
                    $btnClass = ($fila['activo'] == 1) ? 'btn-deshabilitar' : 'btn-habilitar';
                    $btnText = ($fila['activo'] == 1) ? 'Deshabilitado' : 'Habilitado';

                    echo "<a href='#' class='edit-btn' data-toggle='modal' data-target='#editModal' data-id='" . $fila['id'] . "' data-nombre='" . $fila['nombre'] . "' data-apellido='" . $fila['apellido'] . "' data-dni='" . $fila['dni'] . "' data-rol='" . $fila['rol_nombre'] . "'><img src='../images/modificar.png' alt='Modificar'></a> ";

                    if ($esAdminPredeterminado) {
                      // El administrador predeterminado puede eliminar a cualquier usuario
                      echo "<a href='../php/eliminar_usuario.php?id=" . $fila['id'] . "'><img src='../images/eliminar.png' alt='Eliminar'></a><span style='margin-right: 10px;'></span>";
                    } elseif ($_SESSION['rol'] == 'administrador') {
                      // Verificar si el usuario en sesión es un administrador registrado en la base de datos
                      // Mostrar el botón de eliminar solo para camareros y cocineros
                      if ($fila['rol_nombre'] == 'camarero' || $fila['rol_nombre'] == 'cocinero') {
                        echo "<a href='../php/eliminar_usuario.php?id=" . $fila['id'] . "'><img src='../images/eliminar.png' alt='Eliminar'></a><span style='margin-right: 10px;'></span>";
                      }
                    }

                    echo "<button class='cambiar_estado $btnClass' data-id='" . $fila['id'] . "'>$btnText</button>";
                  } else {
                    // Si es un administrador, simplemente muestra un mensaje o deja el espacio en blanco
                    echo "Administrador";
                  }
                } else {
                  // Si el usuario en sesión es el mismo que el usuario en la fila, muestra un mensaje o deja el espacio en blanco
                  echo "Tú";
                }

                echo "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <!-- Botón para abrir el modal de agregar usuario -->
        <div class="mr-3">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            Agregar <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>

    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Agregar Personal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="../php/crearUser.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" class="form-control" name="nombre" required>
              </div>
              <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" class="form-control" name="apellido" required>
              </div>
              <div class="form-group">
                <label for="dni">DNI/NIE:</label>
                <input type="text" id="dni" class="form-control" name="dni" pattern="[XYZxyz]?\d{7}[TRWAGMYFPDXBNJZSQVHLCKEtrwagmyfpdxbnjzsqvhlcke]|[a-zA-Z]?\d{8}[a-zA-Z]" title="Por favor, introduce un DNI o NIE válido." required maxlength="10">
                <small class="form-text text-muted">El DNI debe contener una letra seguida de 7 números y otra letra al final. El NIE debe contener una letra (X, Y o Z) opcional, seguida de 7 números y una letra al final.</small>
              </div>
              <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" class="form-control" name="rol">
                  <?php
                  if ($_SESSION["es_admin_predeterminado"]) {
                    echo '<option value="1">Administrador</option>';
                    echo '<option value="2">Camarero</option>';
                    echo '<option value="3">Cocinero</option>';
                  } else {
                    echo '<option value="2">Camarero</option>';
                    echo '<option value="3">Cocinero</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" class="form-control" name="contrasena" required>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Agregar Usuario</button>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="permisos/editaruser.php" method="post" enctype="multipart/form-data">
              <input type="hidden" id="editId" name="id">
              <div class="form-group">
                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" class="form-control" name="nombre" required>
              </div>
              <div class="form-group">
                <label for="editApellido">Apellido:</label>
                <input type="text" id="editApellido" class="form-control" name="apellido" required>
              </div>
              <div class="form-group">
                <label for="editDni">DNI/NIE:</label>
                <input type="text" id="editDni" class="form-control" name="dni" required maxlength="10">
              </div>
              <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" class="form-control" aria-label="Default select example" name="rol">
                  <?php
                  // Si es administrador predeterminado, mostrar solo opciones de "Camarero" y "Cocinero"
                  if ($_SESSION["es_admin_predeterminado"]) {
                    echo '<option value="1">Administrador</option>';
                    echo '<option value="2">Camarero</option>';
                    echo '<option value="3">Cocinero</option>';
                  } else {
                    // Si es administrador registrado en la base de datos, mostrar también la opción de "Administrador"
                    echo '<option value="2">Camarero</option>';
                    echo '<option value="3">Cocinero</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="editContrasena">Nueva Contraseña:</label>
                <input type="password" id="editContrasena" class="form-control" name="contrasena">
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        // Filtrar la tabla de usuarios según el DNI
        $('#searchInput').on('keyup', function() {
          var searchText = $(this).val().toLowerCase();
          $('.table tbody tr').each(function() {
            var dni = $(this).find('td:nth-child(3)').text().toLowerCase();
            var match = dni.indexOf(searchText) !== -1;
            $(this).toggle(match);
          });
        });

        // Rellenar el formulario de edición con los datos del usuario
        $('.edit-btn').on('click', function() {
          var id = $(this).data('id');
          var nombre = $(this).data('nombre');
          var apellido = $(this).data('apellido');
          var dni = $(this).data('dni');
          var rol = $(this).data('rol');

          $('#editId').val(id);
          $('#editNombre').val(nombre);
          $('#editApellido').val(apellido);
          $('#editDni').val(dni);
          $('#editRol').val(rol);
        });
      });
    </script>
    <script>
      $(document).ready(function() {
        // Función para obtener y actualizar el estado al cargar la página
        function obtenerYActualizarEstado(idUsuario) {
          $.ajax({
            type: "POST",
            url: "../administrador/permisos/estado_actual.php", // Cambia la URL según tu estructura de archivos
            data: {
              id: idUsuario
            },
            success: function(response) {
              // Actualizar el botón y su estilo
              var btn = $(".cambiar_estado[data-id='" + idUsuario + "']");
              if (response === "1") {
                btn.removeClass("btn-deshabilitar").addClass("btn-habilitar").text("Habilitado");
              } else {
                btn.removeClass("btn-habilitar").addClass("btn-deshabilitar").text("Deshabilitado");
              }
            },
            error: function() {
              console.log("Error al obtener el estado del usuario.");
            },
          });
        }

        // Función para cambiar el estado (habilitar/deshabilitar) y el color del botón
        $(".cambiar_estado").click(function() {
          var idUsuario = $(this).data("id");
          var btn = $(this);

          // Realizar una solicitud AJAX para cambiar el estado
          $.ajax({
            type: "POST",
            url: "../administrador/permisos/estado_usuarios.php",
            data: {
              id: idUsuario
            },
            success: function(response) {
              if (response === "success") {
                // Cambio de estado exitoso, actualizar el botón y su estilo
                if (btn.hasClass("btn-habilitar")) {
                  btn.removeClass("btn-habilitar").addClass("btn-deshabilitar").text("Deshabilitado");
                } else {
                  btn.removeClass("btn-deshabilitar").addClass("btn-habilitar").text("Habilitado");
                }
              } else {
                alert("Error al cambiar el estado del usuario.");
              }
            },
            error: function() {
              alert("Error en la solicitud AJAX.");
            },
          });
        });

        // Obtener y actualizar el estado al cargar la página para cada botón
        $(".cambiar_estado").each(function() {
          var idUsuario = $(this).data("id");
          obtenerYActualizarEstado(idUsuario);
        });
      });
    </script>
  </div>
  <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="../js2/bootstrap.js"></script>
  </br>
  <?php include '../footer/footer.php'; ?>
</body>

</html>
<?php
    // Iniciar sesión

    session_start();

    // Destruir sesión

    session_destroy();

    // Redirigir a la página de inicio

    header( "Location: ../index.php" );

    exit();
?>

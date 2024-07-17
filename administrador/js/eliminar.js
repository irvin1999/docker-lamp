$(document).ready(function () {
    // Función para eliminar un usuario
    $(".eliminar_usuario").click(function () {
        var idUsuario = $(this).data("id");
        var btn = $(this);

        // Realiza una solicitud AJAX para eliminar al usuario
        $.ajax({
            type: "POST",
            url: "../php/eliminar_usuario.php", // Ruta al archivo PHP para eliminar usuarios
            data: { id: idUsuario }, // Envía el ID del usuario a eliminar
            success: function (response) {
                if (response === "success") {
                    // Eliminación exitosa: elimina la fila de la tabla
                    btn.closest("tr").remove();
                } else {
                    alert("Error al eliminar el usuario.");
                }
            },
        });
    });
});

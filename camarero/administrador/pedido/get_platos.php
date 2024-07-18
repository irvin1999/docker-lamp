<?php
session_start();
include('../permisos/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_categoria = $_POST['id_categoria'];

    $query = $pdo->prepare("SELECT * FROM platos WHERE estado = 1 AND id_categoria = :id_categoria");
    $query->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
    $query->execute();
    
    while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="col-md-3 plato-item" data-categoria="' . $data['id_categoria'] . '">
                <div class="col-12">
                    <img src="../../administrador/images' . $data['imagen'] . '" class="product-image"
                         alt="Product Image" style="max-width: 100%; height: auto;">
                </div>
                <h6 class="my-3">' . $data['nombre'] . '</h6>
                <div class="bg-gray py-2 px-3 mt-4">
                    <h2 class="mb-0">$' . $data['precio'] . '</h2>
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary btn-block btn-flat addDetalle"
                            data-id="' . $data['id'] . '">
                        <i class="fas fa-cart-plus mr-2"></i> Agregar
                    </button>
                </div>
            </div>';
    }
}
?>
<script>
$(document).ready(function() {
    $(".addDetalle").on("click", function(e) {
        e.preventDefault();

        var platoId = $(this).data("id");

        // Buscar el plato en la lista
        var platoExistente = $("#detalle_pedido").find(".cantidad-input[data-id='" + platoId +
            "']");
        if (platoExistente.length > 0) {
            // Incrementar la cantidad del plato existente
            var cantidadInput = platoExistente;
            cantidadInput.val(parseInt(cantidadInput.val()) + 1);
        } else {
            // Obtener datos del plato
            var platoNombre = $(this).closest(".col-md-3").find("h6").text();
            var platoPrecio = parseFloat($(this).closest(".col-md-3").find(".bg-gray h2").text()
                .replace('$', ''));
            var platoImagen = $(this).closest(".col-md-3").find("img").attr("src");

            // Agregar el plato a la lista
            var detalleHtml = '<div class="row">';
            detalleHtml += '<div class="col-md-3">';
            detalleHtml += '<img src="' + platoImagen + '" alt="Plato Image" class="plato-imagen">';
            detalleHtml += '</div>';
            detalleHtml += '<div class="col-md-6">';
            detalleHtml += '<h6 class="my-3">' + platoNombre + '</h6>';
            detalleHtml += '<div class="bg-gray py-2 px-3 mt-4">';
            detalleHtml += '<h2 class="mb-0">$' + platoPrecio.toFixed(2) + '</h2>';
            detalleHtml += '</div>';
            detalleHtml += '</div>';
            detalleHtml += '<div class="col-md-3">';
            detalleHtml += '<input type="number" class="form-control cantidad-input" data-id="' +
                platoId + '" name="platos[' + platoId +
                ']" placeholder="Cantidad" min="1" value="1"></br>';
            detalleHtml += '<button class="btn btn-danger btn-sm eliminar-plato" data-id="' +
                platoId + '">Eliminar</button>';
            detalleHtml += '</div>';
            detalleHtml += '</div>';

            $("#detalle_pedido").append(detalleHtml);
        }

        // Actualizar el total al agregar un nuevo plato
        actualizarTotal();
    });

    // Agrega la lógica para eliminar un plato
    $("#detalle_pedido").on("click", ".eliminar-plato", function() {
        $(this).closest(".row").remove();

        // Actualizar el total al eliminar un plato
        actualizarTotal();
    });

    // Función para actualizar el total
    function actualizarTotal() {
        var total = 0;

        // Recorrer los platos en la lista
        $(".cantidad-input").each(function() {
            var platoId = $(this).data("id");
            var cantidad = parseInt($(this).val());

            // Consultar el precio del plato
            var query = $.ajax({
                url: "precio.php", // Cambia esto al archivo que obtiene el precio del plato por ID
                method: "GET",
                data: {
                    id: platoId
                },
                async: false
            });

            query.done(function(precio) {
                total += cantidad * parseFloat(precio);
            });
        });

        // Actualizar el valor del campo total en el formulario
        $("#total").val(total.toFixed(2));
    }
});
</script>
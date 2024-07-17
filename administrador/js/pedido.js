document.addEventListener("DOMContentLoaded", function () {
    if ($('#detalle_pedido').length > 0) {
        listar();
    }

    $('#tbl').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
        },
        "order": [
            [0, "desc"]
        ]
    });

    $(".confirmar").submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Está seguro de eliminar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    $('.addDetalle').click(function () {
        let id_producto = $(this).data('id');

        // Utiliza SweetAlert2 para mostrar la confirmación
        Swal.fire({
            title: '¿Desea agregar este plato al pedido?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                registrarDetalle(id_producto);
                Swal.fire('Plato agregado al pedido', '', 'success');
            }
        });
    });

    $('#realizar_pedido').click(function (e) {
        e.preventDefault();
        var action = 'procesarPedido';
        var id_sala = $('#id_sala').val();
        var mesa = $('#mesa').val();
        var observacion = $('#observacion').val();
        $.ajax({
            url: 'ajax.php',
            async: true,
            data: {
                procesarPedido: action,
                id_sala: id_sala,
                mesa: mesa,
                observacion: observacion
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (response != 'error') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pedido Solicitado',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => {
                        window.location = 'mesas.php?id_sala=' + id_sala + '&mesas=' + res.mensaje;
                    }, 1500);
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al generar',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function (error) {
                alert(error);
            }
        });
    });

    $('.finalizarPedido').click(function () {
        var action = 'finalizarPedido';
        var id_sala = $('#id_sala').val();
        var mesa = $('#mesa').val();
        $.ajax({
            url: 'ajax.php',
            async: true,
            data: {
                finalizarPedido: action,
                id_sala: id_sala,
                mesa: mesa
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (response != 'error') {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pedido Finalizado',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => {
                        window.location = 'mesas.php?id_sala=' + id_sala + '&mesas=' + res.mensaje;
                    }, 1500);
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al finalizar',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function (error) {
                alert(error);
            }
        });

    });
});

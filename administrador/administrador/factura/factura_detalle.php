<?php
// Incluir el archivo de conexión a la base de datos
include('../permisos/conexion.php');

if (isset($_GET['id_pedido'])) {
    $id_pedido = intval($_GET['id_pedido']);

    try {
        // Preparar y ejecutar la consulta para obtener los detalles del pedido
        $query = $pdo->prepare("
            SELECT d.cantidad, d.nombre, d.precio 
            FROM detalle_pedidos d 
            WHERE d.id_pedido = :id_pedido
        ");
        $query->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $query->execute();
        $pedido_detalle = $query->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($pedido_detalle as $row) {
            $total += $row['cantidad'] * $row['precio'];
        }
    
        // El total es el valor calculado a partir de los datos
        // Calcular el IVA y el subtotal de forma que: subtotal + iva = total
        $iva = $total * 0.13;
        $subtotal = $total - $iva;  
        ?>
        <div class="card mt-4">
            <div class="card-header">
                Detalles de la Factura
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Nombre del Plato</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedido_detalle as $item) { ?>
                            <tr>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo number_format($item['precio'], 2); ?></td>
                                <td><?php echo number_format($item['cantidad'] * $item['precio'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <h5>Subtotal</h5>
                    </div>
                    <div class="col-6 text-right">
                        <h5><?php echo number_format($subtotal, 2); ?></h5>
                    </div>
                    <div class="col-6">
                        <h5>IVA (13%)</h5>
                    </div>
                    <div class="col-6 text-right">
                        <h5><?php echo number_format($iva, 2); ?></h5>
                    </div>
                    <div class="col-6">
                        <h5>Total</h5>
                    </div>
                    <div class="col-6 text-right">
                        <h5><?php echo number_format($total, 2); ?></h5>
                    </div>
                </div>
                <form method="post" action="factura/procesar_factura.php">
                    <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                    <button type="submit" class="btn btn-success btn-block">Cobrar</button>
                </form>
            </div>
        </div>
        <?php
    } catch (PDOException $e) {
        echo "Error al obtener los detalles del pedido: " . $e->getMessage();
    }
}
?>

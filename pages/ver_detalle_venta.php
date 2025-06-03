<?php
include("../includes/header.php");
include("../conn/conexio.php");

$ventaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$venta = null;
$detallesVenta = [];

if ($ventaId > 0) {
    // Obtener los datos de la venta principal
    $stmt_venta = mysqli_prepare($conex, "SELECT id, DATE_FORMAT(fecha_venta, '%d/%m/%Y %H:%i') as fecha_venta, cliente_nombre, cliente_cedula, total_dolares, total_bolivares FROM ventas WHERE id = ?");
    if ($stmt_venta) {
        mysqli_stmt_bind_param($stmt_venta, "i", $ventaId);
        mysqli_stmt_execute($stmt_venta);
        $result_venta = mysqli_stmt_get_result($stmt_venta);
        $venta = mysqli_fetch_assoc($result_venta);
        mysqli_stmt_close($stmt_venta);
    }

    // Obtener los detalles (productos) de esa venta
    $stmt_detalles = mysqli_prepare($conex, "SELECT nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares FROM detalle_venta WHERE venta_id = ?");
    if ($stmt_detalles) {
        mysqli_stmt_bind_param($stmt_detalles, "i", $ventaId);
        mysqli_stmt_execute($stmt_detalles);
        $result_detalles = mysqli_stmt_get_result($stmt_detalles);
        while ($row = mysqli_fetch_assoc($result_detalles)) {
            $detallesVenta[] = $row;
        }
        mysqli_stmt_close($stmt_detalles);
    }
}

mysqli_close($conex);
?>

<main class="container mt-4">
    <?php if ($venta): ?>
        <h1 class="mb-4">Detalle de Venta #<?php echo htmlspecialchars($venta['id']); ?></h1>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Información General de la Venta
            </div>
            <div class="card-body">
                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($venta['fecha_venta']); ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente_nombre']); ?></p>
                <p><strong>Cédula/RIF:</strong> <?php echo htmlspecialchars($venta['cliente_cedula']); ?></p>
                <p><strong>Total en Dólares:</strong> $<?php echo number_format($venta['total_dolares'], 2); ?></p>
                <p><strong>Total en Bolívares:</strong> <?php echo number_format($venta['total_bolivares'], 2); ?> Bs</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Productos Vendidos
            </div>
            <div class="card-body">
                <?php if (!empty($detallesVenta)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Subtotal ($)</th>
                                    <th class="text-end">Subtotal (Bs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detallesVenta as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                        <td class="text-end"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                        <td class="text-end">$<?php echo number_format($detalle['subtotal_dolares'], 2); ?></td>
                                        <td class="text-end"><?php echo number_format($detalle['subtotal_bolivares'], 2); ?> Bs</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">No se encontraron detalles de productos para esta venta.</div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Venta no encontrada o ID de venta inválido.
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="consultar_ventas.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver a Consultar Ventas</a>
    </div>
</main>

<?php
include("../includes/footer.php");
?>
</body>
</html>
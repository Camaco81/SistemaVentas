<?php
include("../includes/header.php"); // Asume que incluye Bootstrap y otros estilos
include("../conn/conexio.php"); // Tu archivo de conexión a la base de datos

$ventas = [];
$totalDolaresConsulta = 0;
$totalBolivaresConsulta = 0;
$fechaInicio = '';
$fechaFin = '';

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    // Validación básica de fechas
    if (!empty($fechaInicio) && !empty($fechaFin)) {
        // Convertir formato de fecha para la base de datos (YYYY-MM-DD)
        // Si tus fechas en la BD son 'YYYY-MM-DD HH:MM:SS', ajusta el final para incluir el día completo
        $fechaInicioDB = $fechaInicio . ' 00:00:00';
        $fechaFinDB = $fechaFin . ' 23:59:59';

        // Consulta SQL para obtener las ventas dentro del rango de fechas
        // y para calcular la sumatoria
        $sql = "SELECT 
                    id, 
                    DATE_FORMAT(fecha_venta, '%d/%m/%Y %H:%i') as fecha_formateada, 
                    cliente_nombre, 
                    cliente_cedula, 
                    total_dolares, 
                    total_bolivares 
                FROM 
                    ventas 
                WHERE 
                    fecha_venta BETWEEN ? AND ? 
                ORDER BY 
                    fecha_venta DESC"; // Ordena las ventas de la más reciente a la más antigua

        $stmt = mysqli_prepare($conex, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $fechaInicioDB, $fechaFinDB);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $ventas[] = $row;
                $totalDolaresConsulta += $row['total_dolares'];
                $totalBolivaresConsulta += $row['total_bolivares'];
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<div class='alert alert-danger'>Error al preparar la consulta: " . mysqli_error($conex) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Por favor, seleccione un rango de fechas válido.</div>";
    }
}

// Cierra la conexión si no la necesitas abierta para otras cosas,
// aunque a menudo se cierra al final de la página si es un include de conexión.
// mysqli_close($conex); 
?>

<main class="container mt-4">
    <h1 class="mb-4">Consultar Ventas</h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Buscar Ventas por Fecha
        </div>
        <div class="card-body">
            <form action="consultar_ventas.php" method="POST">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fechaInicio); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fechaFin); ?>" required>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Buscar Ventas</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($fechaInicio) && !empty($fechaFin)): // Solo muestra si se envió el formulario con fechas válidas ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Resultados de Ventas (<?php echo htmlspecialchars($fechaInicio); ?> a <?php echo htmlspecialchars($fechaFin); ?>)
            </div>
            <div class="card-body">
                <?php if (!empty($ventas)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Cédula/RIF</th>
                                    <th class="text-end">Total ($)</th>
                                    <th class="text-end">Total (Bs)</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($venta['id']); ?></td>
                                        <td><?php echo htmlspecialchars($venta['fecha_formateada']); ?></td>
                                        <td><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($venta['cliente_cedula']); ?></td>
                                        <td class="text-end">$<?php echo number_format($venta['total_dolares'], 2); ?></td>
                                        <td class="text-end"><?php echo number_format($venta['total_bolivares'], 2); ?> Bs</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="window.open('ver_detalle_venta.php?id=<?php echo $venta['id']; ?>', '_blank')">Ver Detalles</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <div class="p-3 bg-light border rounded">
                            <h4 class="mb-2">Sumatoria de Ventas en este período:</h4>
                            <p class="fs-5 text-success">Total Dólares: <strong>$<?php echo number_format($totalDolaresConsulta, 2); ?></strong></p>
                            <p class="fs-5 text-success">Total Bolívares: <strong><?php echo number_format($totalBolivaresConsulta, 2); ?> Bs</strong></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        No se encontraron ventas para el rango de fechas seleccionado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="dashboar.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
    </div>
</main>

<?php
// Opcional: Incluye tu footer si tienes uno.
// include("../includes/footer.php");
?>
</body>
</html>
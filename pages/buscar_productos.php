<?php
// Conexión a la base de datos
include("../conn/conexio.php");

// Manejo de errores para la conexión
if (!$conex) {
    die("Error de conexión: " . mysqli_connect_error());
}

$queryTasa = "SELECT * FROM tipo_cambio";
$resultado_tasa = mysqli_query($conex, $queryTasa);
$row_tasa = mysqli_fetch_array($resultado_tasa);
$tasa_cambio = $row_tasa['tasa_de_cambio'];

// Recibir el término de búsqueda
$termino = isset($_GET['termino']) ? $_GET['termino'] : '';

// Consulta para buscar productos
// Asegúrate de que tu columna para el stock se llame 'cantidad' o el nombre correcto
$sql = "SELECT id, nombre, precio_en_dolares, cantidad FROM productos WHERE nombre LIKE ? LIMIT 10"; // Usando 'cantidad' como nombre de columna de stock
$stmt = mysqli_prepare($conex, $sql);

if (!$stmt) {
    echo "Error al preparar la consulta: " . mysqli_error($conex);
    exit;
}

$param_termino = '%' . $termino . '%';
mysqli_stmt_bind_param($stmt, "s", $param_termino);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Generar HTML con los resultados
if ($result->num_rows > 0) {
    echo "<table class='table table-striped table-hover mt-3' id='tabla_productos'>";
    echo "<thead><tr>";
    echo "<th>Nombre</th>";
    echo "<th>Precio ($)</th>";
    echo "<th>Precio (Bs)</th>";
    echo "<th>Stock Disponible</th>"; // Cambié el texto del encabezado también
    echo "<th>Seleccionar</th>";
    echo "<th>Cantidad a Vender</th>"; // Cambié el texto del encabezado también
    echo "</tr></thead><tbody>";

    while ($row_producto = mysqli_fetch_assoc($result)) {
        echo "<tr data-id='" . htmlspecialchars($row_producto['id']) . "'>";
        echo "<td class='nombre'>" . htmlspecialchars($row_producto['nombre']) . "</td>";
        echo "<td class='precio_en_dolares'>" . htmlspecialchars($row_producto['precio_en_dolares']) . "$</td>";
        echo "<td class='precio_en_bolivares'>" . htmlspecialchars(number_format($row_producto['precio_en_dolares'] * $tasa_cambio, 2)) . "bs</td>";
        echo "<td class='stock-disponible'>" . htmlspecialchars($row_producto['cantidad']) . "</td>"; // **CLASE CAMBIADA A 'stock-disponible'**
        echo "<td><input type='checkbox' class='seleccionar'></td>";
        echo "<td><input type='number' class='cantidad-a-vender form-control' value='1' min='1'></td>"; // **CLASE CAMBIADA A 'cantidad-a-vender'**
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "No se encontraron productos.";
}

mysqli_stmt_close($stmt);
mysqli_close($conex);
?>
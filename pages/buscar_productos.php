<?php
// Conexión a la base de datos
include("../conn/conexio.php");

$queryTasa="SELECT * FROM tipo_cambio";
  $resultado_tasa=mysqli_query($conex,$queryTasa);
  $row=mysqli_fetch_array($resultado_tasa);
  $tasa_cambio=$row['tasa_de_cambio'];

// Recibir el término de búsqueda
$termino = $_GET['termino'];

// Consulta para buscar productos
$sql = "SELECT * FROM productos WHERE nombre LIKE '%$termino%'";
$result = $conex->query($sql);

// Generar HTML con los resultados
if ($result->num_rows > 0) {
    // ... (código similar a productos.php para generar la tabla)
    echo "<table id='tabla_productos'>";
while ($row = $result->fetch_assoc()) {
    echo "<tr data-id='" . $row['id'] . "'>";
    echo "<td class='nombre'>" . $row['nombre'] . "</td>";
    echo "<td class='precio_en_dolares'>" . $row['precio_en_dolares'] . "$</td>";
    echo "<td class='precio_en_bolivares'>" . $row['precio_en_dolares']*$tasa_cambio . "bs</td>";
    echo "<td><input type='checkbox'  class='seleccionar' data-product-id='" . $row['id'] . "' >"."</td>";
     echo "<td><input type='number'  class='cantidad col-3' >"."</td>";

    echo "</tr>";
}
echo "</table>";
} else {
    echo "No se encontraron productos.";
}

 // <input type="checkbox" class="seleccionar-producto" data-producto-id="123">
<?php
include("../conn/conexio.php");

// Activar la visualización de errores solo para desarrollo (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Recuperar los datos del POST
$productos_json = isset($_POST['productos']) ? $_POST['productos'] : '[]';
$totalDolares = isset($_POST['totalDolares']) ? (float)$_POST['totalDolares'] : 0;
$totalBolivares = isset($_POST['totalBolivares']) ? (float)$_POST['totalBolivares'] : 0;
$clienteNombre = isset($_POST['clienteNombre']) ? $_POST['clienteNombre'] : 'Consumidor Final';
$clienteCedula = isset($_POST['clienteCedula']) ? $_POST['clienteCedula'] : 'V-00000000';

$productos = json_decode($productos_json, true);

// Iniciar una transacción para asegurar la integridad de los datos
mysqli_autocommit($conex, FALSE); // Desactivar autocommit
$success = true;

// Inicializar $stmt_detalle y $stmt_stock a null fuera del try-catch
// Esto asegura que estén definidos incluso si la preparación falla.
$stmt_detalle = null;
$stmt_stock = null;
$stmt_venta = null; // También inicializa stmt_venta para un manejo más seguro en finally

try {
    // 1. Insertar la venta principal
    $stmt_venta = mysqli_prepare($conex, "INSERT INTO ventas (fecha_venta, total_dolares, total_bolivares, cliente_nombre, cliente_cedula) VALUES (NOW(), ?, ?, ?, ?)");
    if (!$stmt_venta) {
        throw new Exception("Error al preparar la consulta de venta: " . mysqli_error($conex));
    }
    mysqli_stmt_bind_param($stmt_venta, "ddss", $totalDolares, $totalBolivares, $clienteNombre, $clienteCedula);

    $exec_success_venta = mysqli_stmt_execute($stmt_venta);
    if (!$exec_success_venta) {
        throw new Exception("Error al ejecutar la inserción de venta: " . mysqli_stmt_error($stmt_venta));
    }

    $venta_id = mysqli_insert_id($conex); // <-- Obtener el ID de la venta recién insertada
    // mysqli_stmt_close($stmt_venta); // <-- ELIMINAR ESTA LÍNEA DE AQUÍ

    if ($venta_id === 0) {
        throw new Exception("Error al obtener el ID de la última venta. Confirma que la columna 'id' en la tabla 'ventas' es AUTO_INCREMENT y que la inserción de la venta no falló silenciosamente.");
    }

    // 2. Preparar las consultas de detalle y stock ANTES del bucle
    $stmt_detalle = mysqli_prepare($conex, "INSERT INTO detalle_venta (venta_id, nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_detalle) {
        throw new Exception("Error al preparar la consulta de detalle de venta: " . mysqli_error($conex));
    }

    $stmt_stock = mysqli_prepare($conex, "UPDATE productos SET cantidad = cantidad - ? WHERE nombre = ?"); // Asumiendo que tu columna de stock es 'cantidad'
    if (!$stmt_stock) {
        throw new Exception("Error al preparar la consulta de stock: " . mysqli_error($conex));
    }

    foreach ($productos as $producto) {
        $nombre_producto = $producto['nombre'];
        $cantidad = $producto['cantidad'];
        $subtotal_dolares = $producto['subtotalDolares'];
        $subtotal_bolivares = $producto['subtotalBolivares'];

        // Insertar detalles de la venta
        mysqli_stmt_bind_param($stmt_detalle, "isdds", $venta_id, $nombre_producto, $cantidad, $subtotal_dolares, $subtotal_bolivares);
        $exec_success_detalle = mysqli_stmt_execute($stmt_detalle);
        if (!$exec_success_detalle) {
             throw new Exception("Error al ejecutar la inserción de detalle para producto " . htmlspecialchars($nombre_producto) . ": " . mysqli_stmt_error($stmt_detalle));
        }

        // Actualizar el stock
        mysqli_stmt_bind_param($stmt_stock, "is", $cantidad, $nombre_producto);
        $exec_success_stock = mysqli_stmt_execute($stmt_stock);
        if (!$exec_success_stock) {
             throw new Exception("Error al actualizar stock para producto " . htmlspecialchars($nombre_producto) . ": " . mysqli_stmt_error($stmt_stock));
        }
    }

    // ELIMINAR ESTAS LÍNEAS DE AQUÍ (ahora se cierran en finally)
    // mysqli_stmt_close($stmt_detalle);
    // mysqli_stmt_close($stmt_stock);

    // Si todo salió bien, confirmar la transacción
    mysqli_commit($conex);
    echo "Venta registrada con éxito!";

} catch (Exception $e) {
    // Si algo falló, revertir la transacción
    mysqli_rollback($conex);
    error_log("Error al registrar venta: " . $e->getMessage());
    http_response_code(500);
    echo "Error al registrar la venta: " . $e->getMessage();
    $success = false;
} finally {
    // Asegurarse de que las sentencias se cierren UNA SOLA VEZ y solo si son objetos válidos
    if ($stmt_venta && is_object($stmt_venta)) { // Añadida la verificación para stmt_venta
        mysqli_stmt_close($stmt_venta);
    }
    if ($stmt_detalle && is_object($stmt_detalle)) {
        mysqli_stmt_close($stmt_detalle);
    }
    if ($stmt_stock && is_object($stmt_stock)) {
        mysqli_stmt_close($stmt_stock);
    }
    mysqli_autocommit($conex, TRUE); // Volver a activar autocommit
    mysqli_close($conex);
}
?>
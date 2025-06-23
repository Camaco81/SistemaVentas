<?php
// registrar_venta.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../conn/conexio.php"); // Asegúrate de que este archivo establece la conexión en $conex o $mysqli

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido.'];

if (!isset($conex) || !$conex) {
    $response['message'] = "Error de conexión a la base de datos.";
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productosJson = isset($_POST['productos']) ? $_POST['productos'] : '[]';
    $totalDolares = isset($_POST['totalDolares']) ? floatval($_POST['totalDolares']) : 0;
    $totalBolivares = isset($_POST['totalBolivares']) ? floatval($_POST['totalBolivares']) : 0;
    $clienteNombre = isset($_POST['clienteNombre']) ? $_POST['clienteNombre'] : 'Consumidor Final';
    $clienteCedula = isset($_POST['clienteCedula']) ? $_POST['clienteCedula'] : 'V-00000000';

    $productos = json_decode($productosJson, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = "Datos de productos inválidos: " . json_last_error_msg();
        echo json_encode($response);
        exit();
    }

    if (empty($productos)) {
        $response['message'] = "No hay productos para registrar.";
        echo json_encode($response);
        exit();
    }

    $conex->autocommit(false);

    try {
        // *** LÍNEA 56 - CORRECCIÓN CRÍTICA EN EL SQL ***
        // Asegúrate de que el número de '?' coincida con el número de variables que pasarás.
        // Si 'fecha_venta' se llenará con NOW(), no necesita un '?'.
        $stmtVenta = $conex->prepare("INSERT INTO ventas (cliente_nombre, cliente_cedula, total_dolares, total_bolivares, fecha_venta) VALUES (?, ?, ?, ?, NOW())");
        // SI LA CONSULTA ANTERIOR SIGUE EN TU CÓDIGO CON 5 '?' Y NOW(), ES LA FUENTE DEL PROBLEMA.
        // CÁMBIALA A:
        $stmtVenta = $conex->prepare("INSERT INTO ventas (cliente_nombre, cliente_cedula, total_dolares, total_bolivares, fecha_venta) VALUES (?, ?, ?, ?, NOW())");


        if (!$stmtVenta) {
            throw new Exception("Error al preparar la consulta de venta: " . $conex->error);
        }

        // *** LÍNEA 61 - CORRECCIÓN CRÍTICA EN bind_param ***
        // Ahora, el string de tipos debe tener 4 caracteres ("ssdd") para las 4 variables.
     $stmtVenta->bind_param("ssdd", $clienteNombre, $clienteCedula, $totalDolares, $totalBolivares);
        $stmtVenta->execute();
        $ventaId = $conex->insert_id;
        $stmtVenta->close();

        if ($ventaId === 0) {
            throw new Exception("No se pudo obtener el ID de la venta principal.");
        }

        foreach ($productos as $producto) {
            $id_producto = $producto['id'];
            $cantidad = $producto['cantidad'];
            $nombre_producto = $producto['nombre'];
            $subtotalDolares = $producto['subtotalDolares'];
            $subtotalBolivares = $producto['subtotalBolivares'];

            $stmtStock = $conex->prepare("SELECT cantidad FROM productos WHERE id = ? FOR UPDATE");
            if (!$stmtStock) {
                throw new Exception("Error al preparar la consulta de stock: " . $conex->error);
            }
            $stmtStock->bind_param("i", $id_producto);
            $stmtStock->execute();
            $stmtStock->bind_result($currentStock);
            $stmtStock->fetch();
            $stmtStock->close();

            if ($currentStock === null || $currentStock < $cantidad) {
                throw new Exception("Stock insuficiente para el producto '{$nombre_producto}'. Disponible: {$currentStock}, Solicitado: {$cantidad}.");
            }

            $stmtUpdateStock = $conex->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");
            if (!$stmtUpdateStock) {
                throw new Exception("Error al preparar la actualización de stock: " . $conex->error);
            }
            $stmtUpdateStock->bind_param("ii", $cantidad, $id_producto);
            $stmtUpdateStock->execute();
            $stmtUpdateStock->close();

            $stmtDetalle = $conex->prepare("INSERT INTO detalle_venta (producto_id, nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares, venta_id) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmtDetalle) {
                throw new Exception("Error al preparar la consulta de detalle: " . $conex->error);
            }
            $stmtDetalle->bind_param("isdddi", $id_producto, $nombre_producto, $cantidad, $subtotalDolares, $subtotalBolivares, $ventaId);
            $stmtDetalle->execute();
            $stmtDetalle->close();
        }

        $conex->commit();
        $response['status'] = 'success';
        $response['message'] = "Venta registrada con éxito";

    } catch (Exception $e) {
        $conex->rollback();
        $response['status'] = 'error';
        $response['message'] = "Error en la operación: " . $e->getMessage();
        error_log("Error al registrar venta: " . $e->getMessage());
    } finally {
        $conex->autocommit(true);
    }

} else {
    $response['message'] = "Método de solicitud no permitido.";
}

echo json_encode($response);
?>
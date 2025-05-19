<?php
include("../conn/conexio.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productosJSON = $_POST["productos"];
    $totalDolares = $_POST["totalDolares"];
    $totalBolivares = $_POST["totalBolivares"];
    $productos = json_decode($productosJSON, true);

    // Iniciar una transacción para asegurar la integridad de los datos
    $conex->begin_transaction();

    try {
        // Insertar la información principal de la venta en la tabla de ventas
        $sqlVenta = "INSERT INTO ventas (fecha_venta, total_dolares, total_bolivares) VALUES (NOW(), ?, ?)";
        $stmtVenta = $conex->prepare($sqlVenta);
        $stmtVenta->bind_param("dd", $totalDolares, $totalBolivares);
        $stmtVenta->execute();
        $idVenta = $conex->insert_id;
        $stmtVenta->close();

        $ventaExitosa = true; // Variable para controlar si todos los productos se vendieron

        foreach ($productos as $producto) {
            $nombre = $producto["nombre"];
            $cantidadVendida = $producto["cantidad"];
            $subtotalDolares = $producto["subtotalDolares"];
            $subtotalBolivares = $producto["subtotalBolivares"];

            // Verificar el stock disponible en la base de datos
            $sqlStock = "SELECT cantidad FROM productos WHERE nombre = ?";
            $stmtStock = $conex->prepare($sqlStock);
            $stmtStock->bind_param("s", $nombre);
            $stmtStock->execute();
            $resultStock = $stmtStock->get_result();

            if ($resultStock->num_rows > 0) {
                $filaStock = $resultStock->fetch_assoc();
                $stockDisponible = $filaStock["cantidad"];

                if ($cantidadVendida > $stockDisponible) {
                    $conex->rollback();
                    echo "Error: No hay suficiente stock de '" . $nombre . "'. Stock disponible: " . $stockDisponible;
                    $ventaExitosa = false;
                    break; // Salir del bucle si no hay suficiente stock
                } else {
                    // Insertar el detalle de la venta
                    $sqlDetalle = "INSERT INTO detalle_venta (id, nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares) VALUES (?, ?, ?, ?, ?)";
                    $stmtDetalle = $conex->prepare($sqlDetalle);
                    $stmtDetalle->bind_param("isddd", $id, $nombre, $cantidadVendida, $subtotalDolares, $subtotalBolivares);
                    $stmtDetalle->execute();
                    $stmtDetalle->close();

                    // Actualizar el stock del producto
                    $sqlUpdateStock = "UPDATE productos SET cantidad = cantidad - ? WHERE nombre = ?";
                    $stmtUpdateStock = $conex->prepare($sqlUpdateStock);
                    $stmtUpdateStock->bind_param("is", $cantidadVendida, $nombre);
                    $stmtUpdateStock->execute();
                    $stmtUpdateStock->close();
                }
            } else {
                $conex->rollback();
                echo "Error: El producto '" . $nombre . "' no existe en la base de datos.";
                $ventaExitosa = false;
                break;
            }

            $stmtStock->close();
        }

        if ($ventaExitosa) {
            $conex->commit();
            echo "Venta registrada exitosamente y stock actualizado.";
        }

    } catch (Exception $e) {
        $conex->rollback();
        echo "Error al registrar la venta: " . $e->getMessage();
    }

    $conex->close();
} else {
    echo "Acceso no permitido.";
}
?>
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
        $idVenta = $conex->insert_id; // Obtener el ID de la venta recién insertada
        $stmtVenta->close();

        // Insertar los detalles de cada producto vendido en una tabla de detalles de venta
        foreach ($productos as $producto) {
            $nombre = $producto["nombre"];
            $cantidad = $producto["cantidad"];
            $subtotalDolares = $producto["subtotalDolares"];
            $subtotalBolivares = $producto["subtotalBolivares"];

            // Suponiendo que tienes una tabla 'detalles_venta' con las columnas:
            // id_detalle, id_venta, nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares
            $sqlDetalle = "INSERT INTO detalle_venta (id, nombre_producto, cantidad, subtotal_dolares, subtotal_bolivares) VALUES (?, ?, ?, ?, ?)";
            $stmtDetalle = $conex->prepare($sqlDetalle);
            $stmtDetalle->bind_param("isddd", $idVenta, $nombre, $cantidad, $subtotalDolares, $subtotalBolivares);
            $stmtDetalle->execute();
            $stmtDetalle->close();

            // Opcional: Actualizar el stock del producto en la tabla de productos
            // $sqlStock = "UPDATE productos SET stock = stock - ? WHERE nombre = ?";
            // $stmtStock = $conex->prepare($sqlStock);
            // $stmtStock->bind_param("is", $cantidad, $nombre);
            // $stmtStock->execute();
            // $stmtStock->close();
        }

        // Confirmar la transacción
        $conex->commit();
        echo "Venta registrada exitosamente.";

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conex->rollback();
        echo "Error al registrar la venta: " . $e->getMessage();
    }

    $conex->close();
} else {
    echo "Acceso no permitido.";
}
?>
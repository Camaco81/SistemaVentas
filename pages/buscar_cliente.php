<?php
include("../conn/conexio.php"); // Ajusta la ruta si es necesario

header('Content-Type: application/json'); // Indicar que la respuesta es JSON

$cedula = isset($_GET['cedula']) ? mysqli_real_escape_string($conex, $_GET['cedula']) : '';

$cliente = null;

if (!empty($cedula)) {
    $stmt = mysqli_prepare($conex, "SELECT id, cedula, nombre, telefono FROM clientes WHERE cedula = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $cedula);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $cliente = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conex);

echo json_encode($cliente); // Devuelve null si no lo encuentra, o los datos del cliente
?>
<?php
include("../includes/header.php"); // O tu header general
include("../conn/conexio.php");

$mensaje = ""; // Para mostrar mensajes de éxito o error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y sanitizar datos del formulario
    $cedula = mysqli_real_escape_string($conex, $_POST['cedula']);
    $nombre = mysqli_real_escape_string($conex, $_POST['nombre']);
    $telefono = mysqli_real_escape_string($conex, $_POST['telefono']);
   

    // Validaciones básicas (puedes añadir más)
    if (empty($cedula) || empty($nombre)) {
        $mensaje = "<div class='alert alert-danger'>La cédula y el nombre son campos obligatorios.</div>";
    } else {
        // Preparar la consulta SQL para insertar el cliente
       $stmt = mysqli_prepare($conex, "INSERT INTO clientes (cedula, nombre, telefono) VALUES (?, ?, ?)");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $cedula, $nombre, $telefono);
            
            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "<div class='alert alert-success'>Cliente registrado exitosamente.</div>";
            } else {
                // Capturar errores específicos, como el de cédula duplicada
                if (mysqli_errno($conex) == 1062) { // 1062 es el código de error para entrada duplicada (UNIQUE constraint)
                    $mensaje = "<div class='alert alert-danger'>Error: La cédula '{$cedula}' ya está registrada.</div>";
                } else {
                    $mensaje = "<div class='alert alert-danger'>Error al registrar cliente: " . mysqli_error($conex) . "</div>";
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al preparar la consulta: " . mysqli_error($conex) . "</div>";
        }
    }
}
mysqli_close($conex);
?>

<main class="container mt-4">
    <h1 class="mb-4">Registrar Nuevo Cliente</h1>

    <?php echo $mensaje; // Mostrar mensajes de éxito o error ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Datos del Cliente
        </div>
        <div class="card-body">
            <form action="registrar_cliente.php" method="POST">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula/RIF <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cedula" name="cedula" required>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre y Apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
               
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono">
                </div>
            
                
                <button type="submit" class="btn btn-success"><i class="bi bi-person-plus"></i> Registrar Cliente</button>
                <a href="dashboar.php" class="btn btn-secondary">Regresar</a> </form>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
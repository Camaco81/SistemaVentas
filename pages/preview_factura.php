<?php
// preview_factura.php

// Recuperar los datos enviados por POST
$productosJson = isset($_POST['productos']) ? $_POST['productos'] : '[]';
$totalDolares = isset($_POST['totalDolares']) ? floatval($_POST['totalDolares']) : 0;
$totalBolivares = isset($_POST['totalBolivares']) ? floatval($_POST['totalBolivares']) : 0;
$clienteNombre = isset($_POST['clienteNombre']) ? $_POST['clienteNombre'] : 'Consumidor Final';
$clienteCedula = isset($_POST['clienteCedula']) ? $_POST['clienteCedula'] : 'V-00000000';

// Decodificar el JSON de productos
// ¡Importante! Reintroducimos urldecode() aquí para asegurar que el JSON se decodifique correctamente
// si fue encodeURIComponent() en el lado del cliente.
$productos = json_decode(urldecode($productosJson), true); 

// Verificar si la decodificación fue exitosa
if (json_last_error() !== JSON_ERROR_NONE) {
    $productos = []; // Si hay un error, inicializar productos como un array vacío
    error_log("Error al decodificar JSON de productos en preview_factura.php: " . json_last_error_msg());
    // Opcional: Para depuración, puedes imprimir el error JSON
    // echo "DEBUG JSON ERROR: " . json_last_error_msg();
}

// Generar un número de factura aleatorio (puedes ajustarlo a tu lógica real)
// Idealmente, esto vendría de la base de datos después de registrar la venta
$numeroFactura = 'FAC-' . date('YmdHis') . rand(1000, 9999);

// Incluye tu header y el resto de tu HTML para la factura
include("../includes/header.php"); // Asegúrate de que este header exista y sea correcto
?>

<div class="container mt-4 mb-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3>Factura de Venta</h3>
                <p>Fecha: <?php echo date('d/m/Y'); ?></p>
                <p>N° Factura: <?php echo htmlspecialchars($numeroFactura); ?></p>
            </div>
            <div class="text-end">
                <h4>Tu Empresa S.A.</h4>
                <p>RIF: J-12345678-9</p>
                <p>Dirección: Calle Principal, Ciudad, Estado</p>
                <p>Teléfono: (000) 123-4567</p>
            </div>
        </div>

        <div class="mb-4">
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($clienteNombre); ?></p>
            <p><strong>C.I./RIF:</strong> <?php echo htmlspecialchars($clienteCedula); ?></p>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO U. ($)</th>
                    <th>SUBTOTAL ($)</th>
                    <th>SUBTOTAL (BS)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                            <td>$<?php echo number_format($producto['precioUnitarioDolar'], 2); ?></td>
                            <td>$<?php echo number_format($producto['subtotalDolares'], 2); ?></td>
                            <td><?php echo number_format($producto['subtotalBolivares'], 2); ?> Bs</td> </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay productos en esta factura.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-end mt-4">
            <h5>Total en Dólares: <span style="color: green;">$<?php echo number_format($totalDolares, 2); ?></span></h5>
            <h5>Total en Bolívares: <span style="color: green;"><?php echo number_format($totalBolivares, 2); ?> Bs</span></h5>
        </div>

        <p class="text-center mt-5">¡Gracias por tu compra!</p>
        <p class="text-center text-muted"><small>Software de Ventas - Creado por [Tu Nombre/Empresa]</small></p>
    </div>

    <div class="d-flex justify-content-around mt-4">
        <button class="btn btn-info" id="downloadPdfBtn">Descargar Factura PDF</button>
        
        <button class="btn btn-primary" onclick="window.print()">Imprimir Factura</button>
        <button class="btn btn-secondary" onclick="window.close()">Cerrar Ventana</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        if (downloadPdfBtn) {
            downloadPdfBtn.addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const element = document.querySelector('.card'); // Selecciona el div de la factura

                html2canvas(element, { scale: 2 }).then(canvas => { // Aumentar escala para mejor resolución
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4'); 
                    const imgWidth = 210; // Ancho A4 en mm
                    const pageHeight = 297; // Alto A4 en mm
                    const imgHeight = canvas.height * imgWidth / canvas.width;
                    let heightLeft = imgHeight;
                    let position = 0;

                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;

                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                    }
                    pdf.save('factura_<?php echo htmlspecialchars($numeroFactura); ?>.pdf');
                }).catch(error => {
                    console.error("Error al generar PDF:", error);
                    alert("Hubo un error al generar el PDF de la factura.");
                });
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
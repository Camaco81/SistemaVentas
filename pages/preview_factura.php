<?php
// Incluye tu header si lo necesitas para estilos globales o librerías
// (Asegúrate de que no tenga elementos que no quieras en la factura, como la barra de navegación)
// include("../includes/header.php");

// Recibe los datos enviados por POST desde ventas.php
$productos_json = isset($_POST['productos']) ? $_POST['productos'] : '[]';
$totalDolares = isset($_POST['totalDolares']) ? (float)$_POST['totalDolares'] : 0;
$totalBolivares = isset($_POST['totalBolivares']) ? (float)$_POST['totalBolivares'] : 0;

$productos = json_decode($productos_json, true); // Convierte el JSON a un array de PHP

// Datos de la factura (puedes traerlos de tu base de datos o generarlos aquí)
$invoiceDate = date('d/m/Y');
// Simplemente para demostración, puedes obtener el número de factura de la BD
$invoiceNumber = "FAC-" . date('YmdHis'); // Esto sería mejor generarlo al registrar la venta

// --- Aquí iría el CSS específico de tu factura ---
// Puedes ponerlo directamente aquí o vincular un archivo CSS externo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa de Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
            position: relative; /* Necesario para la posición absoluta de los botones de control */
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .invoice-header h2 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .invoice-header .company-info p {
            margin: 0;
            text-align: right;
            font-size: 11px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info p {
            margin: 2px 0;
            font-size: 12px;
        }
        .invoice-info strong {
            color: #555;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .invoice-table td.text-right {
            text-align: right;
        }
        .invoice-totals {
            width: 100%;
            text-align: right;
            font-size: 13px;
        }
        .invoice-totals p {
            margin: 5px 0;
        }
        .invoice-totals .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 10px;
        }

        /* Estilos para los botones de control fuera del área de impresión */
        .invoice-controls {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 8px;
        }
        .invoice-controls button, .invoice-controls a {
            margin: 5px;
        }

        /* Ocultar elementos de control al imprimir/generar PDF */
        @media print {
            .invoice-controls {
                display: none;
            }
            body {
                background-color: #fff; /* Fondo blanco para impresión */
                padding: 0;
            }
            .invoice-container {
                box-shadow: none; /* Sin sombra en impresión */
                border: none; /* Sin borde en impresión */
                margin: 0; /* Sin margen en impresión */
                padding: 0; /* Asegurar que el contenido llene el papel */
                width: 100%; /* Asegurar que el contenido ocupe todo el ancho */
            }
        }
    </style>
</head>
<body>

    <div class="invoice-container" id="invoice_content_to_print">
        <div class="invoice-header">
            <div>
                <h2>Factura de Venta</h2>
                <p>Fecha: <strong><?php echo $invoiceDate; ?></strong></p>
                <p>Nº Factura: <strong><?php echo $invoiceNumber; ?></strong></p>
            </div>
            <div class="company-info">
                <h4>Tu Empresa S.A.</h4>
                <p>RIF: J-12345678-9</p>
                <p>Dirección: Calle Principal, Ciudad, Estado</p>
                <p>Teléfono: (000) 123-4567</p>
            </div>
        </div>

        <div class="invoice-info">
            <p>Cliente: <strong>Consumidor Final</strong></p>
            <p>C.I./RIF: <strong>V-00000000</strong></p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio U. ($)</th>
                    <th class="text-right">Subtotal ($)</th>
                    <th class="text-right">Subtotal (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td class="text-right"><?php echo htmlspecialchars($p['cantidad']); ?></td>
                            <td class="text-right"><?php echo number_format($p['subtotalDolares'] / $p['cantidad'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($p['subtotalDolares'], 2); ?></td>
                            <td class="text-right"><?php echo number_format($p['subtotalBolivares'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No hay productos en esta factura.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="invoice-totals">
            <p>Total en Dólares: <span class="total-amount">$<?php echo number_format($totalDolares, 2); ?></span></p>
            <p>Total en Bolívares: <span class="total-amount"><?php echo number_format($totalBolivares, 2); ?> Bs</span></p>
        </div>

        <div class="invoice-footer">
            <p>¡Gracias por tu compra!</p>
            <p>Software de Ventas - Creado por [Tu Nombre/Empresa]</p>
        </div>
    </div>

    <div class="invoice-controls">
        <button class="btn btn-primary" id="downloadPdfBtn">Descargar Factura PDF</button>
        <button class="btn btn-info" onclick="window.print()">Imprimir Factura</button>
        <a href="ventas.php" class="btn btn-secondary">Volver a Ventas</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('#downloadPdfBtn').on('click', function() {
                const invoiceContent = document.getElementById('invoice_content_to_print');

                html2canvas(invoiceContent, { scale: 2 }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jspdf.jsPDF('p', 'mm', 'a4'); // 'p' portrait, 'mm' units, 'a4' size
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                    const margin = 10; // 10mm margin on all sides
                    const actualPdfWidth = pdfWidth - (margin * 2);
                    const actualPdfHeight = (imgProps.height * actualPdfWidth) / imgProps.width;

                    pdf.addImage(imgData, 'PNG', margin, margin, actualPdfWidth, actualPdfHeight);
                    pdf.save('factura_venta.pdf');
                }).catch(error => {
                    console.error("Error al generar el PDF:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de PDF',
                        text: 'No se pudo generar el PDF. Revisa la consola para más detalles.'
                    });
                });
            });
        });
    </script>
</body>
</html>
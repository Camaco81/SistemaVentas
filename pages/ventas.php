<?php include("../includes/header.php"); ?>

<?php  
include("../conn/conexio.php"); ?>
<div class="text-center">
    <style>
    /* Contenedor principal de la factura oculto */
    #invoice_printable_area {
        display: none; /* Esto lo esconde de la vista del usuario */
        position: absolute; /* Para que no afecte el layout de la página */
        left: -9999px; /* Lo mueve fuera del área visible */
        top: -9999px;
        width: 794px; /* Ancho para A4 en píxeles (a 96 DPI) */
        padding: 30px; /* Márgenes internos */
        box-sizing: border-box; /* Incluye padding en el ancho */
        background-color: #fff;
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        color: #333;
        font-size: 12px;
    }

    /* Encabezado de la factura */
    #invoice_printable_area .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    #invoice_printable_area .invoice-header h2 {
        color: #007bff;
        margin: 0;
        font-size: 24px;
    }

    #invoice_printable_area .invoice-header .company-info p {
        margin: 0;
        text-align: right;
        font-size: 11px;
    }

    /* Información del cliente */
    #invoice_printable_area .invoice-info {
        margin-bottom: 20px;
    }
    #invoice_printable_area .invoice-info p {
        margin: 2px 0;
        font-size: 12px;
    }
    #invoice_printable_area .invoice-info strong {
        color: #555;
    }

    /* Tabla de productos */
    #invoice_printable_area .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    #invoice_printable_area .invoice-table th,
    #invoice_printable_area .invoice-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    #invoice_printable_area .invoice-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
    }

    #invoice_printable_area .invoice-table td.text-right {
        text-align: right;
    }

    /* Totales de la factura */
    #invoice_printable_area .invoice-totals {
        width: 100%;
        text-align: right; /* Alinea a la derecha */
        font-size: 13px;
    }

    #invoice_printable_area .invoice-totals p {
        margin: 5px 0;
    }

    #invoice_printable_area .invoice-totals .total-amount {
        font-size: 18px;
        font-weight: bold;
        color: #28a745; /* Verde para el total final */
    }

    /* Pie de página */
    #invoice_printable_area .invoice-footer {
        text-align: center;
        margin-top: 50px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        color: #777;
        font-size: 10px;
    }
</style>
  
<h1>Ventas</h1>
</div>
<main class="p-3">
  <h3>Buscador de productos</h3>
  <p class="text-muted">(Busque el producto, agregue la cantidad y seleccionelo )</p>
  <input type="text" id="busqueda" placeholder="Buscar producto" class="rounded-2 p-1 border border-2" autofocus>
<div id="resultados">
</div>
<div id="productos_Container" class="mt-3">
  <h3>Productos selecionados</h3>
  <p class="text-muted">(Aca se listan lo productos selecionados)</p>
  <div id="productos_seleccionados"></div>
  <h3>Total de la venta</h3>
  <p class="text-muted">(Precio total de todos los productos seleccionados tanto en dolares como bolivares)</p>
  <p id="total" class="fw-semibold"></p> 
   <button class="btn btn-danger" id="registrarVentaBtn">Generar factura</button>

 
   
</div>
<div class=" d-flex justify-content-center  ">
  <a href="dashboar.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
</div>

</main>
<script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>

    $(document).ready(function() {
    $("#busqueda").on("keyup", function() {
        var termino = $(this).val();
        $.ajax({
            url: "buscar_productos.php",
            data: { termino: termino },
            success: function(data) {
                $("#resultados").html(data);
            }
        });
    });
    // ... (tu código JavaScript existente en ventas.php) ...

$("#registrarVentaBtn").on("click", function() {
    var productos = [];
    var hayProductos = false;

    $(".producto_seleccionado").each(function() {
        hayProductos = true;
        var textoProducto = $(this).text();
        var partes = textoProducto.split(' - ');
        var nombreCantidad = partes[0].split('(');
        var nombre = nombreCantidad[0].trim();
        var cantidad = parseInt(nombreCantidad[1].replace(')', '').trim());
        var subtotalDolares = parseFloat(partes[1].trim().split(' ')[0]);
        var subtotalBolivares = parseFloat(partes[2].trim().split(' ')[0]);

        productos.push({
            nombre: nombre,
            cantidad: cantidad,
            // Aquí podrías agregar precioUnitarioDolar y precioUnitarioBolivar si los necesitas en la vista previa
            // ya que ahora tienes subtotalDolares y cantidad.
            subtotalDolares: subtotalDolares,
            subtotalBolivares: subtotalBolivares
        });
    });

    if (!hayProductos) {
        Swal.fire({
            icon: "warning",
            title: "No hay productos seleccionados",
            text: "Por favor, agregue productos a la venta antes de generar la factura.",
        });
        return;
    }

    var totalDolares = parseFloat($('#total').text().split('Total en dolares:')[1].trim().split('$')[0].trim());
    var totalBolivares = parseFloat($('#total').text().split('Total en bolivares:')[1].trim().split('bs')[0].trim());

    // --- ¡CAMBIO CLAVE AQUÍ! ---
    // En lugar de generar el PDF directamente, enviamos los datos a la página de vista previa.
    // Usamos un formulario dinámico para enviar los datos por POST a la nueva página.
    var form = $('<form action="preview_factura.php" method="post" target="_blank"></form>');
    form.append('<input type="hidden" name="productos" value="' + JSON.stringify(productos) + '">');
    form.append('<input type="hidden" name="totalDolares" value="' + totalDolares + '">');
    form.append('<input type="hidden" name="totalBolivares" value="' + totalBolivares + '">');
    $('body').append(form);
    form.submit();
    form.remove(); // Elimina el formulario después de enviarlo

    // Ahora, después de abrir la vista previa, puedes registrar la venta en la BD
    // Esto asegura que la venta se registre incluso si el usuario cierra la vista previa.
    registrarVentaEnBD(productos, totalDolares, totalBolivares);

    // No necesitas llamar a generarFacturaPDF() desde aquí en ventas.php
    // porque la vista previa (preview_factura.php) se encargará de eso.
});

// La función generarFacturaPDF() ya NO va en este archivo (ventas.php)
// Se ha movido y adaptado a preview_factura.php


    $(document).on("change", ".seleccionar", function() {
        var row = $(this).closest('tr');
        var nombre = row.find('.nombre').text();
        var cantidadSeleccionada = parseInt(row.find('.cantidad').val());
        var precioText = row.find('.precio_en_dolares').text();
        var precioIntDolar = parseFloat(precioText);
        var precioTextV = row.find('.precio_en_bolivares').text();
        var precioIntBolivar = parseFloat(precioTextV);
        var stockDisponible = parseInt(row.find('.stock').text()); // Asumiendo que tienes una clase 'stock' en tu fila de resultados

        if ($(this).is(":checked")) {
            if (cantidadSeleccionada > stockDisponible) {
                Swal.fire({
                    icon: "warning",
                    title: "Stock Insuficiente",
                    text: `No hay suficiente stock de ${nombre}. Stock disponible: ${stockDisponible}`,
                });
                $(this).prop("checked", false); // Desmarca el checkbox
                row.find('.cantidad').val(1); // Restablece la cantidad a 1 o un valor válido
                return;
            }
            agregarProductoALista(nombre, cantidadSeleccionada, precioIntDolar, precioIntBolivar);
        } else {
            // Eliminar el producto de la lista (tendrías que implementar esta lógica si es necesario)
            eliminarProductoDeLista(nombre); // Ejemplo de función para eliminar
        }
        calcularTotal();
    });

    function agregarProductoALista(nombre, cantidad, precioIntDolar, precioIntBolivar) {
        var productoExistente = $(`#productos_seleccionados div:contains('${nombre} (')`);
        if (productoExistente.length > 0) {
            // Si el producto ya existe, actualiza la cantidad (opcional, según tu lógica)
            var cantidadActual = parseInt(productoExistente.text().split('(')[1].split(')')[0].trim());
            var nuevaCantidad = cantidadActual + cantidad;
            var subtotalDolaresActual = parseFloat(productoExistente.find('.subtotalDolares').text());
            var subtotalBolivaresActual = parseFloat(productoExistente.find('.subtotalBolivares').text());
            productoExistente.html(`${nombre} (${nuevaCantidad}) - <span class="subtotalDolares"> ${(nuevaCantidad * precioIntDolar).toFixed(2)} </span>$ - <span class="subtotalBolivares">${(nuevaCantidad * precioIntBolivar).toFixed(2)}</span>bs`);
        } else {
            var html = `<div class="producto_seleccionado">
                ${nombre} (${cantidad}) - <span class="subtotalDolares"> ${(cantidad * precioIntDolar).toFixed(2)} </span>$ - <span class="subtotalBolivares">${(cantidad * precioIntBolivar).toFixed(2)}</span>bs
            </div>`;
            $("#productos_seleccionados").append(html);
        }
    }

    function eliminarProductoDeLista(nombre) {
        $(`#productos_seleccionados div:contains('${nombre} (')`).remove();
    }


    function calcularTotal() {
        let totalDolares = 0;
        let totalBolivares = 0;

        $('.producto_seleccionado').each(function() {
            const subtotalDolares = parseFloat($(this).find('.subtotalDolares').text());
            const subtotalBolivares = parseFloat($(this).find('.subtotalBolivares').text());
            totalDolares += subtotalDolares;
            totalBolivares += subtotalBolivares;
        });

        $('#total').text('Total en dolares: ' + totalDolares.toFixed(2) + '$' + ' Total en bolivares:  ' + totalBolivares.toFixed(2) + 'bs');
    }

  

    function registrarVentaEnBD(productos, totalDolares, totalBolivares) {
        $.ajax({
            url: "registrar_venta.php",
            method: "POST",
            data: {
                productos: JSON.stringify(productos),
                totalDolares: totalDolares,
                totalBolivares: totalBolivares
            },
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: response,
                });
                $("#productos_seleccionados").empty();
                $("#total").text('');
            },
            error: function(xhr, status, error) {
                console.error("Error al registrar la venta:", error);
                alert("Error al registrar la venta.");
            }
        });
    }
});

</script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>
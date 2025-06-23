<?php include("../includes/header.php"); ?>

<?php
include("../conn/conexio.php"); ?>
<div class="text-center">
    <h1>Ventas</h1>
</div>
<main class="p-3">
    <h3>Buscador de productos</h3>
    <p class="text-muted">(Busque el producto, agregue la cantidad y seleccionelo )</p>
    <div class="d-flex align-items-center">
        <input type="text" id="busqueda" placeholder="Buscar producto" class="rounded-2 p-1 border border-2" autofocus>
        <div id="stockMessage" class="ms-2 text-danger fw-bold"></div> </div>
    <div id="resultados">
    </div>
    <div id="productos_Container" class="mt-3">
        <div class="mb-3">
            <label for="clienteCedula" class="form-label">Cédula/RIF del Cliente</label>
            <div class="input-group">
                <input type="text" class="form-control" id="clienteCedula" name="clienteCedula" value="V-00000000">
                <button class="btn btn-outline-secondary" type="button" id="buscarClienteBtn">Buscar</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="clienteNombre" class="form-label">Nombre del Cliente</label>
            <input type="text" class="form-control" id="clienteNombre" name="clienteNombre" value="Consumidor Final" readonly>
        </div>

        <h3>Productos seleccionados</h3> <p class="text-muted">(Aca se listan los productos seleccionados)</p>
        <div id="productos_seleccionados"></div>
        <h3>Total de la venta</h3>
        <p class="text-muted">(Precio total de todos los productos seleccionados tanto en dolares como bolivares)</p>
        <p id="total" class="fw-semibold"></p>
        <button class="btn btn-danger" id="registrarVentaBtn">Generar Factura y Registrar Venta</button>


    </div>
    <div class=" d-flex justify-content-center ">
        <a href="dashboar.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
    </div>

</main>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Al buscar un producto, limpia los resultados anteriores y el mensaje de stock
        $("#busqueda").on("keyup", function() {
            var termino = $(this).val();
            $.ajax({
                url: "buscar_productos.php",
                data: {
                    termino: termino
                },
                success: function(data) {
                    $("#resultados").html(data);
                    $('#stockMessage').text(''); // Limpia el mensaje de stock
                    // Re-aplicar la lógica a los nuevos elementos cargados si es necesario.
                    // Aunque el `$(document).on` ya maneja esto.
                }
            });
        });

        // =========================================================================
        // === LÓGICA UNIFICADA PARA EL BOTÓN "GENERAR FACTURA Y REGISTRAR VENTA" ===
        // =========================================================================
        $("#registrarVentaBtn").on("click", function() {
            // 1. Validar datos del cliente
            const clienteNombre = $('#clienteNombre').val().trim();
            const clienteCedula = $('#clienteCedula').val().trim();

            if (clienteNombre === "" || clienteCedula === "") {
                Swal.fire({
                    icon: "warning",
                    title: "Datos del Cliente Incompletos",
                    text: "Por favor, ingrese el nombre y la cédula/RIF del cliente.",
                });
                return;
            }

            // 2. Obtener productos seleccionados para la factura (con precios unitarios)
            const productosParaFactura = obtenerProductosSeleccionadosParaFactura();

            if (productosParaFactura.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "No hay productos seleccionados",
                    text: "Por favor, agregue productos a la venta antes de generar la factura.",
                });
                return;
            }

            // 3. Recalcular los totales finales (asegurando la exactitud)
            let currentTotalDolares = 0;
            let currentTotalBolivares = 0;
            $(".producto_seleccionado").each(function() {
                // Asegurarse de que se parseen los valores numéricos correctamente
                const subtotalDolares = parseFloat($(this).find('.subtotalDolares').text());
                const subtotalBolivares = parseFloat($(this).find('.subtotalBolivares').text());

                if (!isNaN(subtotalDolares)) currentTotalDolares += subtotalDolares;
                if (!isNaN(subtotalBolivares)) currentTotalBolivares += subtotalBolivares;
            });
            currentTotalDolares = parseFloat(currentTotalDolares.toFixed(2));
            currentTotalBolivares = parseFloat(currentTotalBolivares.toFixed(2));


            // 4. Abrir preview de factura en una nueva pestaña (con POST)
            var formPreview = $('<form action="preview_factura.php" method="post" target="_blank"></form>');
            // Usar encodeURIComponent para asegurar que el JSON se pase correctamente en el 'value'
            // Los valores de cliente se pasan directamente, preview_factura.php los manejará
            formPreview.append('<input type="hidden" name="productos" value="' + encodeURIComponent(JSON.stringify(productosParaFactura)) + '">');
            formPreview.append('<input type="hidden" name="totalDolares" value="' + currentTotalDolares + '">');
            formPreview.append('<input type="hidden" name="totalBolivares" value="' + currentTotalBolivares + '">');
            formPreview.append('<input type="hidden" name="clienteNombre" value="' + clienteNombre + '">'); // SIN encodeURIComponent
            formPreview.append('<input type="hidden" name="clienteCedula" value="' + clienteCedula + '">'); // SIN encodeURIComponent
            $('body').append(formPreview);
            formPreview.submit(); // Esto abre la nueva pestaña
            formPreview.remove(); // Eliminar el formulario después de enviarlo

            // 5. Llamar a la función para registrar la venta en la base de datos (después de abrir la factura)
            // Para la base de datos, necesitamos los mismos datos que para la factura
            registrarVentaEnBD(productosParaFactura, currentTotalDolares, currentTotalBolivares, clienteNombre, clienteCedula);
        });

        // =========================================================================
        // === Lógica para la interacción con la tabla de resultados de búsqueda ===
        // =========================================================================

        // Esta función se ejecuta cuando se cambia la cantidad o se marca/desmarca el checkbox
        $(document).on("input change", ".cantidad-a-vender, .seleccionar", function() {
            var row = $(this).closest('tr');
            var nombre = row.find('.nombre').text();
            var id_producto = row.data('id');
            var cantidadInput = row.find('.cantidad-a-vender');
            var cantidadSeleccionada = parseInt(cantidadInput.val());
            // IMPORTANTE: Obtener los precios unitarios directamente desde los elementos HTML de la búsqueda
            // Asegurarse de limpiar los símbolos $ o Bs antes de parseFloat
            var precioUnitarioDolar = parseFloat(row.find('.precio_en_dolares').text().replace('$', '').trim());
            var precioUnitarioBolivar = parseFloat(row.find('.precio_en_bolivares').text().replace('Bs', '').trim());
            var stockDisponible = parseInt(row.find('.stock-disponible').text());
            var checkbox = row.find('.seleccionar');

            // Limpiar mensaje de stock previo
            $('#stockMessage').text('');

            // Validar si la cantidad es un número válido y positivo
            if (isNaN(cantidadSeleccionada) || cantidadSeleccionada <= 0) {
                checkbox.prop("checked", false);
                eliminarProductoDeLista(id_producto);
                calcularTotal();
                return;
            }

            // Validar stock
            if (cantidadSeleccionada > stockDisponible) {
                $('#stockMessage').text(`Lo sentimos, no hay ${cantidadSeleccionada} unidades de ${nombre}. Solo hay ${stockDisponible} disponibles.`);
                checkbox.prop("checked", false);
                eliminarProductoDeLista(id_producto);
                cantidadInput.val(stockDisponible); // Opcional: ajustar la cantidad al máximo disponible
            } else {
                $('#stockMessage').text('');
                checkbox.prop("checked", true); // MARCAR EL CHECKBOX AUTOMÁTICAMENTE
                agregarProductoALista(nombre, cantidadSeleccionada, precioUnitarioDolar, precioUnitarioBolivar, id_producto);
            }
            calcularTotal();
        });

        // =========================================================================
        // === Funciones de ayuda ===
        // =========================================================================

        function agregarProductoALista(nombre, cantidad, precioUnitarioDolar, precioUnitarioBolivar, id_producto) {
            var productoExistente = $(`#productos_seleccionados div.producto_seleccionado[data-id='${id_producto}']`);
            
            // Calculamos los subtotales usando los precios unitarios limpios
            let subtotalDolaresCalculado = (cantidad * precioUnitarioDolar).toFixed(2);
            let subtotalBolivaresCalculado = (cantidad * precioUnitarioBolivar).toFixed(2);

            if (productoExistente.length > 0) {
                // Si el producto ya existe, actualizamos su información
                productoExistente.attr('data-cantidad', cantidad); // Actualizar data-cantidad
                productoExistente.attr('data-precio-dolar', precioUnitarioDolar); // Actualizar data-precio-dolar
                productoExistente.attr('data-precio-bolivar', precioUnitarioBolivar); // Actualizar data-precio-bolivar
                productoExistente.find('.subtotalDolares').text(subtotalDolaresCalculado);
                productoExistente.find('.subtotalBolivares').text(subtotalBolivaresCalculado);
                // Actualizar el texto general del div
                productoExistente.html(`${nombre} (${cantidad}) - $<span class="subtotalDolares">${subtotalDolaresCalculado}</span> - <span class="subtotalBolivares">${subtotalBolivaresCalculado}</span> Bs`);
            } else {
                // Si es un producto nuevo, lo agregamos
                var html = `<div class="producto_seleccionado" 
                                data-nombre="${nombre}" 
                                data-id="${id_producto}"
                                data-precio-dolar="${precioUnitarioDolar}"
                                data-precio-bolivar="${precioUnitarioBolivar}"
                                data-cantidad="${cantidad}">
                    ${nombre} (${cantidad}) - $<span class="subtotalDolares">${subtotalDolaresCalculado}</span> - <span class="subtotalBolivares">${subtotalBolivaresCalculado}</span> Bs
                </div>`;
                $("#productos_seleccionados").append(html);
            }
        }

        function eliminarProductoDeLista(id_producto) {
            $(`#productos_seleccionados div.producto_seleccionado[data-id='${id_producto}']`).remove();
            $(`#resultados table tr[data-id='${id_producto}'] .seleccionar`).prop("checked", false);
            // También limpiar la cantidad en el input en la tabla de resultados
            $(`#resultados table tr[data-id='${id_producto}'] .cantidad-a-vender`).val('');
        }

        function calcularTotal() {
            let totalDolares = 0;
            let totalBolivares = 0;

            $('.producto_seleccionado').each(function() {
                // Asegurarse de que se parseen los valores numéricos correctamente
                const subtotalDolares = parseFloat($(this).find('.subtotalDolares').text());
                const subtotalBolivares = parseFloat($(this).find('.subtotalBolivares').text());

                if (!isNaN(subtotalDolares)) totalDolares += subtotalDolares;
                if (!isNaN(subtotalBolivares)) totalBolivares += subtotalBolivares;
            });

            $('#total').text('Total en dólares: ' + totalDolares.toFixed(2) + '$' + ' Total en bolívares: ' + totalBolivares.toFixed(2) + ' Bs');
        }

        // Función específica para obtener productos con precio unitario para la FACTURA PREVIEW y BD
        function obtenerProductosSeleccionadosParaFactura() {
            const productos = [];
            $('#productos_seleccionados .producto_seleccionado').each(function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const cantidad = parseInt($(this).data('cantidad'));
                const precioUnitarioDolar = parseFloat($(this).data('precio-dolar'));
                const precioUnitarioBolivar = parseFloat($(this).data('precio-bolivar'));

                // Calcular subtotal nuevamente aquí para asegurarnos de que estén correctos
                const subtotalDolares = (precioUnitarioDolar * cantidad).toFixed(2);
                const subtotalBolivares = (precioUnitarioBolivar * cantidad).toFixed(2);

                productos.push({
                    id: id,
                    nombre: nombre,
                    cantidad: cantidad,
                    precioUnitarioDolar: parseFloat(precioUnitarioDolar), // Asegurar que sea numérico
                    precioUnitarioBolivar: parseFloat(precioUnitarioBolivar), // Asegurar que sea numérico
                    subtotalDolares: parseFloat(subtotalDolares), // Asegurar que sea numérico
                    subtotalBolivares: parseFloat(subtotalBolivares) // Asegurar que sea numérico
                });
            });
            return productos;
        }

        // Función para registrar la venta en la base de datos (se mantiene igual, ya está corregida)
        function registrarVentaEnBD(productos, totalDolares, totalBolivares, clienteNombre, clienteCedula) {
            $.ajax({
                url: "registrar_venta.php",
                method: "POST",
                dataType: "json", // Especificar que esperamos JSON
                data: {
                    productos: JSON.stringify(productos),
                    totalDolares: totalDolares,
                    totalBolivares: totalBolivares,
                    clienteNombre: clienteNombre,
                    clienteCedula: clienteCedula
                },
                success: function(res) { // El parámetro 'res' ya será un objeto JSON debido a 'dataType: "json"'
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Venta registrada con éxito",
                            text: res.message,
                        });
                        // Limpiar interfaz después de un registro exitoso
                        $("#productos_seleccionados").empty();
                        $("#total").text('');
                        $('#clienteNombre').val('Consumidor Final');
                        $('#clienteCedula').val('V-00000000');
                        $("#busqueda").val('');
                        $("#busqueda").trigger("keyup"); // Recargar productos en búsqueda para actualizar stock
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error al registrar la venta",
                            text: res.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error AJAX al registrar la venta:", error, xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error de conexión o respuesta inválida",
                        text: "No se pudo comunicar con el servidor para registrar la venta o la respuesta no fue válida. Verifique la consola para más detalles.",
                    });
                }
            });
        }

        // Lógica para el botón de buscar cliente
        document.getElementById('buscarClienteBtn').addEventListener('click', function() {
            const cedula = document.getElementById('clienteCedula').value;
            if (cedula.trim() === '') {
                Swal.fire('Atención', 'Por favor, ingrese una cédula para buscar.', 'warning');
                return;
            }

            fetch('buscar_cliente.php?cedula=' + encodeURIComponent(cedula))
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('clienteNombre').value = data.nombre;
                        Swal.fire('Éxito', 'Cliente encontrado: ' + data.nombre + ' ' + (data.apellido || ''), 'success');
                    } else {
                        document.getElementById('clienteNombre').value = 'Consumidor Final';
                        Swal.fire('Información', 'Cliente no encontrado. Se usará "Consumidor Final".', 'info');
                    }
                })
                .catch(error => {
                    console.error('Error al buscar cliente:', error);
                    Swal.fire('Error', 'Ocurrió un error al buscar el cliente.', 'error');
                });
        });

        document.getElementById('clienteCedula').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('buscarClienteBtn').click();
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
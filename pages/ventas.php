<?php include("../includes/header.php"); ?>

<?php  
include("../conn/conexio.php"); ?>
<div class="text-center">
<h1>Ventas</h1>
</div>
<main class="p-3">
  <h3>Buscador de productos</h3>
  <p class="text-muted">(Busque el producto, agregue la cantidad y seleccionelo )</p>
  <input type="text" id="busqueda" placeholder="Buscar producto" class="rounded-2 p-1 border border-2" autofocus>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        $("#registrarVentaBtn").on("click", function() {
            var productos = [];
            var hayProductos = false;

            // 1. Capturar datos del cliente
            var clienteNombre = $('#clienteNombre').val().trim();
            var clienteCedula = $('#clienteCedula').val().trim();

            if (clienteNombre === "" || clienteCedula === "") {
                Swal.fire({
                    icon: "warning",
                    title: "Datos del Cliente Incompletos",
                    text: "Por favor, ingrese el nombre y la cédula/RIF del cliente.",
                });
                return;
            }

            // 2. Recoger productos seleccionados
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

            // 3. Crear y enviar el formulario a preview_factura.php (nueva pestaña)
            var form = $('<form action="preview_factura.php" method="post" target="_blank"></form>');
            form.append('<input type="hidden" name="productos" value="' + JSON.stringify(productos) + '">');
            form.append('<input type="hidden" name="totalDolares" value="' + totalDolares + '">');
            form.append('<input type="hidden" name="totalBolivares" value="' + totalBolivares + '">');
            form.append('<input type="hidden" name="clienteNombre" value="' + clienteNombre + '">');
            form.append('<input type="hidden" name="clienteCedula" value="' + clienteCedula + '">');

            $('body').append(form);
            form.submit(); // ¡Aquí estaba el error principal!
            form.remove(); // Y aquí el otro para limpiar el DOM

            // 4. Registrar la venta en la base de datos (AJAX)
            // Asegúrate de pasar los datos del cliente a la función registrarVentaEnBD
            registrarVentaEnBD(productos, totalDolares, totalBolivares, clienteNombre, clienteCedula);
        });

        // Evento para seleccionar/deseleccionar productos y actualizar la lista
        $(document).on("change", ".seleccionar", function() {
            var row = $(this).closest('tr');
            var nombre = row.find('.nombre').text();
            var cantidadSeleccionada = parseInt(row.find('.cantidad').val());
            var precioIntDolar = parseFloat(row.find('.precio_en_dolares').text());
            var precioIntBolivar = parseFloat(row.find('.precio_en_bolivares').text());
            var stockDisponible = parseInt(row.find('.stock').text());

            if ($(this).is(":checked")) {
                if (cantidadSeleccionada > stockDisponible) {
                    Swal.fire({
                        icon: "warning",
                        title: "Stock Insuficiente",
                        text: `No hay suficiente stock de ${nombre}. Stock disponible: ${stockDisponible}`,
                    });
                    $(this).prop("checked", false);
                    row.find('.cantidad').val(1);
                    return;
                }
                agregarProductoALista(nombre, cantidadSeleccionada, precioIntDolar, precioIntBolivar);
            } else {
                eliminarProductoDeLista(nombre);
            }
            calcularTotal();
        });

        function agregarProductoALista(nombre, cantidad, precioIntDolar, precioIntBolivar) {
            var productoExistente = $(`#productos_seleccionados div:contains('${nombre} (')`);
            if (productoExistente.length > 0) {
                var cantidadActual = parseInt(productoExistente.text().split('(')[1].split(')')[0].trim());
                var nuevaCantidad = cantidadActual + cantidad;
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
            calcularTotal();
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

            $('#total').text('Total en dolares: ' + totalDolares.toFixed(2) + '$' + ' Total en bolivares: ' + totalBolivares.toFixed(2) + 'bs');
        }

        // 5. Función registrarVentaEnBD con los nuevos parámetros del cliente
        function registrarVentaEnBD(productos, totalDolares, totalBolivares, clienteNombre, clienteCedula) {
            $.ajax({
                url: "registrar_venta.php",
                method: "POST",
                data: {
                    productos: JSON.stringify(productos),
                    totalDolares: totalDolares,
                    totalBolivares: totalBolivares,
                    clienteNombre: clienteNombre, // Asegúrate de pasar estos datos
                    clienteCedula: clienteCedula  // Asegúrate de pasar estos datos
                },
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Venta registrada con éxito", // Mensaje más descriptivo
                        text: response,
                    });
                    $("#productos_seleccionados").empty();
                    $("#total").text('');
                    // Opcional: limpiar también los campos del cliente después de registrar la venta
                    $('#clienteNombre').val('Consumidor Final');
                    $('#clienteCedula').val('V-00000000');
                },
                error: function(xhr, status, error) {
                    console.error("Error al registrar la venta:", error, xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error al registrar la venta",
                        text: xhr.responseText || "Hubo un problema. Intente de nuevo.",
                    });
                }
            });
        }
    });
</script>
<script>
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
                    // Maneja si el apellido es null
                    // Si tienes más campos, llénalos aquí
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

    // Opcional: Para que la búsqueda se realice al presionar Enter en el campo de cédula
    document.getElementById('clienteCedula').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevenir el envío del formulario
            document.getElementById('buscarClienteBtn').click(); // Simular clic en el botón
        }
    });

    // Asegúrate de que los valores de clienteNombre y clienteCedula se usen en registrarVentaEnBD
    // Tu función registrarVentaEnBD ya los recibe, solo asegúrate de que los campos del form los tengan
    // O si los obtienes directamente del DOM, que apunten a los IDs correctos.
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>
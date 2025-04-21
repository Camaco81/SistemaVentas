<?php include("../includes/header.php"); ?>

<?php  
include("../conn/conexio.php"); ?>
<div class="text-center">
  <a href="dashboar.php">Regresar a Inicio</a>
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
   <button class="btn btn-primary" id="registrarVentaBtn">Registra Venta</button>
   <button class="btn btn-info" id="generarFacturaBtn">Generar factura</button>
   
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
$("#registrarVentaBtn").on("click", function() {
var productos = [];
var totalDolares = parseFloat($('#total').text().split(':')[1].trim().split('$')[0].trim());
 var totalBolivares = parseFloat($('#total').text().split('Total en bolivares:')[1].trim().split('bs')[0].trim());

 $(".producto_seleccionado").each(function() {
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

 // Ahora tenemos la información de los productos y el total,
 // podemos enviarla al servidor para registrar la venta.
 registrarVentaEnBD(productos, totalDolares, totalBolivares);
});

 $("#generarFacturaBtn").on("click", function() {
 generarFacturaPDF();
 });

  $(document).on("change", ".seleccionar", function() {
var row = $(this).closest('tr'); // Suponiendo que cada producto está dentro de un <tr>
 var nombre = row.find('.nombre').text(); // Ajusta el selector según tu HTML
 var cantidad = row.find('.cantidad').val();
 var precioText = row.find('.precio_en_dolares').text();
 var precioIntDolar= parseFloat(precioText);
 var precioTextV = row.find('.precio_en_bolivares').text();
 var precioIntBolivar= parseFloat(precioTextV);
 if ($(this).is(":checked")) {
 agregarProductoALista(nombre, cantidad, precioIntDolar,precioIntBolivar);
 } else {
 // Eliminar el producto de la lista
 }
 calcularTotal();
 });

 function agregarProductoALista(nombre, cantidad, precioIntDolar,precioIntBolivar) {
// Crear un elemento HTML para el producto y agregarlo a #productos_seleccionados
 var html = `<div class="producto_seleccionado">
 ${nombre} (${cantidad}) - <span class="subtotalDolares"> ${cantidad * precioIntDolar} </span>$ - <span class="subtotalBolivares">${cantidad*precioIntBolivar}</span>bs
 </div>`;
 $("#productos_seleccionados").append(html);
 }


function calcularTotal() {
 let totalDolares = 0;
 let totalBolivares = 0;

 // Seleccionamos todos los elementos que representan productos seleccionados
 $('.producto_seleccionado').each(function() {
 // Obtenemos el precio y la cantidad de cada producto
 const subtotalDolares = parseFloat($(this).find('.subtotalDolares').text());
const subtotalBolivares = parseFloat($(this).find('.subtotalBolivares').text());
 totalDolares += subtotalDolares;
 totalBolivares += subtotalBolivares;
 });

 // Mostramos el total en un elemento HTML (ajusta el selector según tu HTML)
 $('#total').text('Total en dolares: ' + totalDolares.toFixed(2)+'$'+' Total en bolivares:  '+ totalBolivares.toFixed(2)+'bs' );
}

 function generarFacturaPDF() {
        const elementoParaConvertir = document.getElementById('productos_Container'); // El contenedor de los productos y el total

 html2canvas(elementoParaConvertir).then(function(canvas) {
 const imgData = canvas.toDataURL('image/png');
 const pdf = new jspdf.jsPDF();
 const imgProps= pdf.getImageProperties(imgData);
 const pdfWidth = pdf.internal.pageSize.getWidth();
 const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

 pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
 pdf.save('factura_venta.pdf');
});
 }

 function registrarVentaEnBD(productos, totalDolares, totalBolivares) {
 $.ajax({
 url: "registrar_venta.php", // Archivo PHP que procesará el registro
 method: "POST",
 data: {
 productos: JSON.stringify(productos), // Convertimos el array de productos a JSON
 totalDolares: totalDolares,
 totalBolivares: totalBolivares
 },
success: function(response) {
     Swal.fire({
                    icon: "success",
                    title: response,
                });
  // Puedes mostrar un mensaje de éxito o error
 // Opcional: Limpiar la lista de productos seleccionados
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
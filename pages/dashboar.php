<?php 
include('../conn/conexio.php');
session_start();
if (isset($_SESSION['usuario'])!="usuario") {
	header("Location: ../auth/login.php");
}
?>
<?php  include("../includes/header.php")  ?>

<main class="d-flex">
	<aside class="bg-secondary  col-2" style="height: 100vh;">
		<ul class="mt-4 text-white" style="list-style: none;">
			<li  class="mb-2"><i class="bi bi-house-fill"></i> <a class="text-white text-decoration-none" href="dashboar.php"> Inicio</a> </li>
			<li  class="mb-2"><i class="bi bi-currency-dollar"></i> <a class="text-white text-decoration-none" href="tasa.php">Tasa dolar</a></li>
			<li  class="mb-2"><i class="bi bi-shop"></i> <a class="text-white text-decoration-none" href="productos.php"> Productos</a></li>
			<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="ventasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-cart"></i> Ventas
    </a>
    <ul class="dropdown-menu" aria-labelledby="ventasDropdown">
        <li>
            <a class="dropdown-item" href="ventas.php">
                <i class="bi bi-cash-coin"></i> Realizar Venta </a>
        </li>
        <li>
            <a class="dropdown-item" href="consultar_ventas.php"> <i class="bi bi-search"></i> Consultar Ventas </a>
        </li>
         <li>
            <a class="dropdown-item" href="registrar_cliente.php"> <i class="bi bi-person-plus"></i> Registar cliente </a>
        </li>
    </ul>
</li>
			<a href="../auth/cerrarSesion.php" class="col-2 text-decoration-none text-white"><i class="bi bi-box-arrow-left h4"></i> Salir  </a>
		</ul>
	</aside>
	<div class="d-flex  align-items-center col-10 flex-column p-3">
		<h1>Bienvenido a sistema ventas</h1>
		<p class="p-2">Sea bienvenido a este sistema de ventas, el cual le ayudara avender sus productos de un forma mas sencilla</p>
	</div>

</main>

<?php  include("../includes/footer.php")?>
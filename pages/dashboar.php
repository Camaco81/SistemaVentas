<?php
include('../conn/conexio.php');
session_start();
if (isset($_SESSION['usuario'])!="usuario") {
	header("Location: login.php");
}
?>
<?php include("../includes/header.php") ?>

<main class="d-flex bg-light">
	<aside class="brawn col-2" style="height: 100vh;">
		<ul class="mt-4 " style="list-style: none;">
			<li class="mb-2"><i class="bi bi-house-fill"></i> <a class="text-black text-decoration-none" href="dashboar.php"> Inicio</a> </li>
			<li class="mb-2"><i class="bi bi-currency-dollar"></i> <a class="text-black text-decoration-none" href="tasa.php">Tasa dolar</a></li>
			<li class="mb-2"><i class="bi bi-shop"></i> <a class="text-black text-decoration-none" href="productos.php"> Productos</a></li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="ventasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="bi bi-cart"></i> Ventas
				</a>
				<ul class="dropdown-menu" aria-labelledby="ventasDropdown">
					<li>
						<a class="dropdown-item" href="ventas.php">
							<i class="bi bi-cash-coin"></i> Realizar Venta
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="consultar_ventas.php"> <i class="bi bi-search"></i> Consultar Ventas </a>
					</li>
					<li>
						<a class="dropdown-item" href="registrar_cliente.php"> <i class="bi bi-person-plus"></i> Registar cliente </a>
					</li>
				</ul>
			</li>
			<a href="../auth/cerrarSesion.php" class="col-2 text-decoration-none text-black"><i class="bi bi-box-arrow-left h4"></i> Salir </a>
		</ul>
	</aside>

	<div class="col-10 d-flex flex-column p-3 align-items-center">
		<div class="col-6 d-flex justify-content-center align-items-center mb-4"> <img src="../img/logo-v1.png" class="col-4 rounded-4">
		</div>

		<section class="d-flex flex-column align-items-center w-100"> <div class="carousel-container" style="max-width: 800px;"> <div id="miCarrusel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

			<h3 class="mt-4 mb-4 text-center">Productos mas vendidos</h3>
					<div class="carousel-indicators">
						<button type="button" data-bs-target="#miCarrusel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
						<button type="button" data-bs-target="#miCarrusel" data-bs-slide-to="1" aria-label="Slide 2"></button>
						<button type="button" data-bs-target="#miCarrusel" data-bs-slide-to="2" aria-label="Slide 3"></button>
					</div>

					<div class="carousel-inner ">
						<div class="carousel-item active">
							<img src="../img/arroz-mary.jpg" class="d-block  w-100" alt="Atardecer en la playa">
						</div>
						<div class="carousel-item">
							<img src="../img/harina-deli.jpg" class="d-block  w-100" alt="Montañas Nevadas">
							
						</div>
						<div class="carousel-item">
							<img src="../img/cafe-sucafe.jpg" class="d-block  w-100" alt="Ciudad Nocturna">
					
						</div>
					</div>

					<button class="carousel-control-prev" type="button" data-bs-target="#miCarrusel" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Anterior</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#miCarrusel" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Siguiente</span>
					</button>
				</div>
			</div>
		</section>
	</div>
</main>

<?php include("../includes/footer.php")?>
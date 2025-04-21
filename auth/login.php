<?php include("../includes/header.php") ?>
<?php include("../conn/conexio.php"); 
session_start();
	if (!empty($_POST['comprobar'])) {
		$usuario=$_POST['correo'];
		$clave=$_POST['clave'];
		$sql=$conex->query("SELECT * FROM usuarios WHERE correo='$usuario' and clave='$clave'");
		if ($datos=$sql->fetch_object()) {
			$_SESSION['usuario']=$usuario;
			header("Location:../pages/dashboar.php");
		} else {
			header("Location: register.php");
		}	
	}

?>
<main style="height:100vh; " class="bg-secondary p-2">
	<div class="container col-6 border border-secondary p-4  mb-4 rounded-2 bg-light" >
	<h1 class="text-center">Sistema de Ventas</h1>
	<h3 class="text-center">Bienvenido de nuevo</h3>
	<form action="login.php"
	method="POST" class="d-flex flex-column p-4 ">
		<label for="">Usuario</label>
		<input  required type="email" name="correo" id="" class="mb-2 p-1 rounded-2 border border-1 border-secondary">
		<label for="">Contraseña</label>
		<input  required type="password" name="clave" id="pass" class="mb-2 p-1 rounded-2 border border-1 border-secondary">
		<div class="text-muted mb-2"><input type="checkbox" name="" id="show"> Mostar contraseña</div>
		<input type="submit" value="Iniciar sesión" class="btn btn-dark mb-2" name="comprobar">
		<p>No tienes una cuenta? <a href="register.php" class="text-decoration-none text-secondary"> Registrate aquí</a></p>
	</form>

	
</div>
</main>





<?php include("../includes/footer.php") ?>
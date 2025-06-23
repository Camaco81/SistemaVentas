
<?php 
include("../conn/conexio.php");
include("../includes/header.php");
	if (isset($_POST['registrar_user'])) {
		$correo=$_POST['correo'];
		$clave=$_POST['clave'];
		$query="INSERT INTO usuarios(correo, clave) VALUES('$correo','$clave')";
		$result=mysqli_query($conex, $query);

		if (!$result) {
			die("query fallo");
		}else{
			 echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Operación exitosa",
                    text: "Usuario registrado exitosamente."
                });
            </script>';
		}

		
	}

?>
<main style="height:100vh; " class="brawn p-2">

<div class="container  col-6 p-4 mt-4 rounded-2 bg-light">
	
	<form action="register.php" method="POST" class="d-flex flex-column mb-2">
		 <div class="d-flex justify-content-center align-items-center">
                  <div class="col-6 d-flex  flex-column-reverse justify-content-center align-items-center">
                    <h3 class="mt-2 text-green-700">Registro</h3>
            <img src="../img/logo-v1.png" class="col-7 rounded-4">
            </div> 
            </div>
		<label class="text-green-700 fw-semibold" >Correo</label>
		<input required type="email" name="correo" id="" class="mb-2 p-1 rounded-2 border border-1 p-2">
		<label class="text-green-700 fw-semibold"  >Contraseña</label>
		<input required type="password" name="clave"  class="mb-2 p-1 rounded-2 border border-1 p-2" id="pass">
		<div class=" mb-2"><input type="checkbox" name="" id="show"> Mostar contraseña</div>
		<input type="submit" value="Registrarse" class="btn  mb-2 btn-login text-white" name="registrar_user" >
		
	</form>
	<p>Ya tienes una cuenta? <a href="login.php" class="text-decoration-none text-green-500 "> Inicia Sesión aquí</a></p>
</div>
</main>
<?php include("../includes/footer.php")?>
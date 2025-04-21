<?php 
	include("../conn/conexio.php");
 if (isset($_GET['id'])) {
	$id= $_GET['id'];
	$query="SELECT * FROM tipo_cambio WHERE id= $id";
	$result = mysqli_query($conex, $query);
	if (mysqli_num_rows($result)==1) {
		$row = mysqli_fetch_array($result);
		$tasa_de_cambio=$row['tasa_de_cambio'];
		
	}
}

if (isset($_POST['actualizar_tasa'])) {
	$id=$_GET['id'];
	$tasa_de_cambio_optenido=$_POST['tasa'];
	$query=" UPDATE  tipo_cambio set tasa_de_cambio='$tasa_de_cambio_optenido' WHERE id =$id ";
	$result = mysqli_query($conex, $query);
	if($result){
		  echo "<script>
           alert('Datos de pago actualizado')

           </script>";
	}
	header("Location: tasa.php");
}

?>
<?php include("../includes/header.php") ?>

	
	<div class="d-flex flex-column justify-content-center align-items-center">
		<h1>Tasa de cambio $</h1>
<form action="actualizar_tasa.php?id=<?php echo $_GET['id']; ?>" method="POST" class="d-flex flex-column">
	<label for="">Actualizar Tasa </label>
    <input type="text"  name="tasa"  class="mb-2" value="<?php echo $tasa_de_cambio; ?>">
<button class="btn btn-primary mb-2" type="submit" name="actualizar_tasa">Actualizar</button>
</form>
</div>


<?php include("../includes/footer.php") ?>
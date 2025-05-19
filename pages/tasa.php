<?php 
include("../conn/conexio.php");

if (isset($_POST['registar_precio'])) {
	$tasa= $_POST['tasa'];
	$query="INSERT INTO tipo_cambio(tasa_de_cambio) VALUES('$tasa')";
	$result=mysqli_query($conex, $query);
	if ($result) {
		echo "<script> alert('Precio registrado')</script>";
	}
	
}

?>

<?php include("../includes/header.php") ?>


<div class="d-flex flex-column  justify-content-center align-items-center">
	<img src="../img/descarga.png" alt="" class="rounded-2 mt-2 mb-2">
	
	<h1>Tasa de cambio </h1>
	<div class=" d-flex justify-content-center  flex-column ">
 
<!-- <form action="tasa.php" method="POST" class="d-flex flex-column">
	<label for="">Registrar tasa </label>
    <input type="text"  name="tasa"  class="mb-2">
            
	
<button class="btn btn-primary mb-2" type="submit" name="registar_precio">Guardar</button>
</form> -->


 <?php 

            $query="SELECT * FROM tipo_cambio";
            $resultado_tasa=mysqli_query($conex,$query);
            while ($row=mysqli_fetch_array($resultado_tasa)) { ?>
            	<div class="d-flex mx-4 justify-content-center align-items-center">
            		<p class="h5 text-secondary">Precio actual del dolar <?php echo $row['tasa_de_cambio'] ?> bs</p>
                  <a href="actualizar_tasa.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none bg-secondary text-white rounded-2 p-2 mx-2"><i class="bi bi-pencil-fill"></i></a>
            	</div>
            	
               
            <?php } ?>

         <div class=" d-flex justify-content-center  ">
  <a href="dashboar.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
</div>



</div>

<?php include("../includes/footer.php") ?>

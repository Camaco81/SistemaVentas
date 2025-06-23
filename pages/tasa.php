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
<main class="mt-4" style="height:100vh;">
	<div class="d-flex flex-column  justify-content-center align-items-center " >
	<div class="col-12 d-flex flex-row-reverse">
		<div class="col-8 d-flex flex-row-reverse"><img src="../img/logo-v1.png" class="col-2 rounded-4 mx-2"></div>
		 
	</div>

	<img src="../img/El-dolar-BCV.jpg" alt="" class="rounded-2  mb-2 col-4">
	
	<h1 class="text-green-700">Tasa de cambio </h1>
	<div class=" d-flex justify-content-center  flex-column ">
 



 <?php 

            $query="SELECT * FROM tipo_cambio";
            $resultado_tasa=mysqli_query($conex,$query);
            while ($row=mysqli_fetch_array($resultado_tasa)) { ?>
            	<div class="d-flex mx-4 justify-content-center align-items-center">
            		<p class="h5 text-green-500">Precio actual del dolar <?php echo $row['tasa_de_cambio'] ?> bs</p>
                  <a href="actualizar_tasa.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn-login text-white rounded-2 p-2 mx-2"><i class="bi bi-pencil-fill"></i></a>
            	</div>
            	
               
            <?php } ?>
 <div class="mt-4 text-center">
        <a href="dashboar.php" class="btn btn-login text-white mb-4"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
    </div>



</div>
</main>




<?php include("../includes/footer.php") ?>

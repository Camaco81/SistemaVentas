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
	<img src="../img/El-dolar-BCV.jpg" alt="" class="rounded-2 mt-2 mb-2 col-7">
	
	<h1>Tasa de cambio </h1>
	<div class=" d-flex justify-content-center  flex-column ">
 



 <?php 

            $query="SELECT * FROM tipo_cambio";
            $resultado_tasa=mysqli_query($conex,$query);
            while ($row=mysqli_fetch_array($resultado_tasa)) { ?>
            	<div class="d-flex mx-4 justify-content-center align-items-center">
            		<p class="h5 text-secondary">Precio actual del dolar <?php echo $row['tasa_de_cambio'] ?> bs</p>
                  <a href="actualizar_tasa.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none bg-secondary text-white rounded-2 p-2 mx-2"><i class="bi bi-pencil-fill"></i></a>
            	</div>
            	
               
            <?php } ?>
 <div class="mt-4 text-center">
        <a href="dashboar.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
    </div>



</div>

<?php include("../includes/footer.php") ?>

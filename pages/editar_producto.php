<?php 
include("../conn/conexio.php");
 if (isset($_GET['id'])) {
	$id= $_GET['id'];
	$query="SELECT * FROM productos WHERE id= $id";
	$result = mysqli_query($conex, $query);
	if (mysqli_num_rows($result)==1) {
		$row = mysqli_fetch_array($result);
		$nombre=$row['nombre'];
		$cantidad=$row['cantidad'];
		$precio_en_dolares=$row['precio_en_dolares'];
		
	}
}

if (isset($_POST['actualizar_producto'])) {
	$id=$_GET['id'];
	$nombre_optenido=$_POST['nombre'];
	$cantidad_optenida=$_POST['cantidad'];
	$precio_optenido=$_POST['precio_en_dolares'];

	$query=" UPDATE  productos set nombre='$nombre_optenido', cantidad='$cantidad_optenida', precio_en_dolares='$precio_optenido' WHERE id =$id ";
	$result = mysqli_query($conex, $query);
	// $_SESSION['mensaje']='Cliente modificado exitosamente';
	// $_SESSION['tipo_mensaje']='warning';

	if($result){
		  echo "<script>
           alert('Datos del cliente actualizado')

           </script>";
	}
	header("Location: productos.php");

}


 ?>
 <?php include("../includes/header.php") ?>
<div class="container p-4">
	<h2>Editar Producto</h2>
	<div class="row d-flex justify-content-center">
		<div class="col md-4 mx-auto">
			<div class="card card-body ">
				<form action="editar_producto.php?id=<?php echo $_GET['id']; ?>" method="POST">
					<div class="form-group">
					<label for="" class="fw-semibold">Nombre del producto</label>
					<input type="text" name="nombre" value="<?php echo $nombre; ?>" class="form-control" placeholder="Actualize producto">
					</div>
					<div class="form-group">
					<label for="" class="fw-semibold">Cantidad</label>
					<input type="text" name="cantidad" class="form-control" placeholder="Actualizar cantidad" value="<?php echo $cantidad; ?>">
					</div>
					<div class="form-group">
					<label for="" class="fw-semibold">Precio</label>
					<input type="text" name="precio_en_dolares" class="form-control" placeholder="Actualize precio en dolares" value="<?php echo $precio_en_dolares; ?>">
					</div>
						
					
					
					<button class="btn btn-success" name="actualizar_producto">Actualizar</button>
				</form>
			</div>
			
		</div>
	</div>
</div>

<?php include("../includes/footer.php") ?>
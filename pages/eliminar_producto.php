<?php 
	include("../conn/conexio.php");
 if (isset($_GET['id'])) {
	$id= $_GET['id'];
	$query="DELETE FROM productos WHERE id= $id";
	$result = mysqli_query($conex, $query);
	if (!$result) {
		die("query fallo");
	}else{
		echo "<script>
	alert('Producto eliminado');
	</script>";
	header('Location: productos.php');

	}

	
	
}

?>
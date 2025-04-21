<?php include("../includes/header.php"); ?>
<?php  
include("../conn/conexio.php");

 $queryTasa="SELECT * FROM tipo_cambio";
  $resultado_tasa=mysqli_query($conex,$queryTasa);
  $row=mysqli_fetch_array($resultado_tasa);
  $tasa_cambio=$row['tasa_de_cambio'];


// function convertir_a_bolivares($precio_dolares, $tasa_cambio) {
//     return $precio_dolares * $tasa_cambio;
// }

if (isset($_POST['registrar_producto'])) {
    $nombre=$_POST['nombre'];
    $precio_en_dolares=$_POST['precio_en_dolares'];
    
    $query="INSERT INTO productos(nombre,precio_en_dolares) VALUES('$nombre','$precio_en_dolares')";
    $result=mysqli_query($conex, $query);

    if (!$result) {
      die("query fallo");
    }else{
        echo "<script>
           alert('Producto registrado exitosamente')

           </script>";
  }

 }

?>
<div class="text-center">
  <a href="dashboar.php">Regresar a Inicio</a>

<h1 >Registrar Productos</h1>
</div>

<main class="d-flex justify-content-center align-items-center">
  <div class="p-2 d-flex flex-column col-5  border border-1 border-secondary mx-4 mt-4 mb-4 rounded-2 " >
  <form action="productos.php" method="POST" class="d-flex flex-column p-2">
    <label class="fw-semibold" for="">Nombre del producto</label>
    <input class="mb-2" type="text" name="nombre">
    <label class="fw-semibold" for="">Precio del producto en $ </label>
    <input class="mb-2" type="text" name="precio_en_dolares">
    <button type="submit" class="btn btn-secondary" name="registrar_producto">Registar producto</button>
  </form>
</div>
</main>


<div class="mx-4">
  <table class="table col-10" id="productos">
              <thead>
                <tr>
                 
                  <th scope="col">Nombre del producto</th>
                  <th scope="col">Precio en $</th>
                  <th scope="col">Precio en bs</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
               <tbody>
                <?php 

            $query="SELECT * FROM productos";
            $resultado_productos=mysqli_query($conex,$query);
            while ($row=mysqli_fetch_array($resultado_productos)) { ?>
              <tr>

                <td ><?php echo $row['nombre'] ?></td>
                <td ><?php echo $row['precio_en_dolares'] ?></td>
                <td ><?php echo $row['precio_en_dolares']*$tasa_cambio ?></td>

                <td class="d-flex ">
                  <a href="editar_cliente.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn btn-secondary"><i class="bi bi-pencil-fill"></i></a>
                </td>


              </tr>


            <?php } ?>
        </tbody>
             
 </table>
</div>




<?php include("../includes/footer.php"); ?>

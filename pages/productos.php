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
    $cantidad=$_POST['cantidad'];
    
    $query="INSERT INTO productos(nombre,precio_en_dolares,cantidad) VALUES('$nombre','$precio_en_dolares','$cantidad')";
    $result=mysqli_query($conex, $query);

    if (!$result) {
      die("query fallo");
    }else{
        echo '<script>
          Swal.fire({
                    icon: "success",
                    title: "Operación exitosa",
                    text: "Producto registrado exitosamente."
                });

           </script> ';
  }

 }

?>
<div class="text-center">
  <!-- <a href="dashboar.php">Regresar a Inicio</a> -->

<h1 >Registrar Productos</h1>
</div>

<main class="d-flex justify-content-center align-items-center">
  <div class="p-2 d-flex flex-column col-5  border border-1 border-secondary mx-4 mt-4 mb-4 rounded-2 " >
  <form action="productos.php" method="POST" class="d-flex flex-column p-2">
    <label class="fw-semibold" for="">Nombre del producto</label>
    <input class="mb-2" type="text" name="nombre">
    <label class="fw-semibold" for="">Precio del producto en $(por unidad) </label>
    <input class="mb-2" type="text" name="precio_en_dolares">
     <label class="fw-semibold" for="">Cantidad(unidades)</label>
    <input class="mb-2" type="text" name="cantidad">
    <button type="submit" class="btn btn-secondary" name="registrar_producto">Registar producto</button>
  </form>
</div>
</main>


<div class="mx-4">
  <table class="table col-10" id="productos">
              <thead>
                <tr>
                 
                  <th scope="col">Nombre del producto</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Precio en $ <br>(por unidad)</th>
                  <th scope="col">Precio en bs <br>(por unidad)</th>
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
                <td ><?php echo $row['cantidad'] ?></td>
                <td ><?php echo $row['precio_en_dolares'] ?></td>
                <td ><?php echo $row['precio_en_dolares']*$tasa_cambio ?></td>

                <td class="d-flex ">
                  <a href="editar_producto.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn btn-secondary"><i class="bi bi-pencil-fill"></i></a>
                
            
                  <a href="eliminar_producto.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn btn-danger"> <i class="bi bi-trash-fill"></i></a>
                  </td>

              </tr>


            <?php } ?>
        </tbody>
             
 </table>
  <div class=" d-flex justify-content-center mb-3 ">
  <a href="dashboar.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
</div>

</div>




<?php include("../includes/footer.php"); ?>

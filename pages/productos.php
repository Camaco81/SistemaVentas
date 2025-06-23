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


<main class=" brawn" style="height:100; width: 100%;">
   
 <div class="col-12 d-flex flex-row-reverse">
        <div class="col-5 d-flex flex-row-reverse mt-2"><img src="../img/logo-v2.png" class="col-3 rounded-4 mx-2"></div>
        <div class="text-center col-8 d-flex flex-row-reverse mt-3 align-items-center">
    <h1 class="text-green-700">  Registrar productos</h1>
    </div>
         
    </div>

<section class="d-flex  flex-column justify-content-center align-items-center">
    <div class="p-2 d-flex flex-column col-5  bg-light mx-4  mb-4 rounded-2 p-4" >
  <form action="productos.php" method="POST" class="d-flex flex-column p-2 bg-light">
    <label class="fw-semibold" for="">Nombre del producto</label>
    <input class="mb-2 p-2 border border-2 rounded-2" type="text" name="nombre">
    <label class="fw-semibold" for="">Precio del producto en $(por unidad) </label>
    <input class="mb-2 p-2 border border-2 rounded-2" type="text" name="precio_en_dolares">
     <label class="fw-semibold" for="">Cantidad(unidades)</label>
    <input class="mb-2 p-2 border border-2 rounded-2" type="text" name="cantidad">
    <button type="submit" class="btn btn-login text-white" name="registrar_producto">Registar producto</button>
  </form>
</div>
</section>


<div class="mx-4 ">
  <table class="table col-10 " id="productos">
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
                  <a href="editar_producto.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn btn-login text-white"><i class="bi bi-pencil-fill"></i></a>
                
            
                  <a href="eliminar_producto.php?id=<?php echo $row['id']?>" class=" d-flex m-1 text-decoration-none btn btn-danger"> <i class="bi bi-trash-fill"></i></a>
                  </td>

              </tr>


            <?php } ?>
        </tbody>
             
 </table>
<div class="mt-4 text-center brawn" style="width: 100%;">
        <a href="dashboar.php" class="btn btn-login text-white mb-4"><i class="bi bi-arrow-left"></i> Regresar a Inicio</a>
    </div>

</div>
</main>



<?php include("../includes/footer.php"); ?>

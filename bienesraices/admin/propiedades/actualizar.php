<?php 

    require "../../includes/funciones.php";
    $auth = estaAutenticado();


    if(!$auth) {
        header("Location: /");
    }

    // Ver que el id sea int(valido)
    $id = $_GET["id"];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id) {
        header("Location: /admin");
    }

    require "../../includes/config/database.php";
    $db = conectarBD();

    // Consulta para obtener los datos de la propiedad
    $consulta = "SELECT * FROM propiedades WHERE id = ${id}";
    $resultado = mysqli_query($db,$consulta);
    $propiedad = mysqli_fetch_assoc($resultado);     //Aunque sean iguales las variables con la de abajo , no hay problema ya que se 
                                                     // le asigna el valor de resultado          


    //Consultar para obtener los vendedores

    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    //Arreglo con mensajes de errores
    $errores = [];

    $titulo = $propiedad["titulo"];
    $precio = $propiedad["precio"];
    $descripcion = $propiedad["descripcion"];
    $habitaciones = $propiedad["habitaciones"];
    $wc = $propiedad["wc"];
    $estacionamiento = $propiedad["estacionamiento"];
    $vendedores_id = $propiedad["vendedores_id"];
    $imagenPropiedad = $propiedad["imagen"];   // No se puede mostrar el url guardado en la BD
 
    // Ejecutar codigo despues de que el usuario envia el formulario

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        //Para evitar SQL INJECTIONS SIN PDO O POO 
        
        $precio = mysqli_real_escape_string($db, $_POST["precio"]);
        $titulo = mysqli_real_escape_string($db,  $_POST["titulo"]);
        $descripcion = mysqli_real_escape_string($db, $_POST["descripcion"]);
        $habitaciones =mysqli_real_escape_string($db,  $_POST["habitaciones"]);
        $wc = mysqli_real_escape_string($db, $_POST["wc"]);
        $estacionamiento = mysqli_real_escape_string($db, $_POST["estacionamiento"]);
        $vendedores_id = mysqli_real_escape_string($db, $_POST["vendedor"]);
        $creado = date("Y/m/d");

        // Asignar files hacia una var

        $imagen = $_FILES["imagen"];

        if(!$titulo){
            $errores[] = "Debes insertar un titulo";
        }
        if(!$precio){
            $errores[] = "Debes insertar un precio";
        }
        if(strlen($descripcion) < 50){//Debe escribir minimo 50 caracteres
            $errores[] = "Debes insertar un descripcion";
        }
        if( !$habitaciones){ 
            $errores[] = "Debes insertar un habitaciones";
        }
        if(!$wc){
            $errores[] = "Debes insertar un wc";
        }
        if(!$estacionamiento){
            $errores[] = "Debes insertar un estacionamiento";
        }
        

        //Validar por tamaño(150kb max)
        $medida = 1000*1000;
        if($imagen["size"] > $medida) {
            $errores[] =  "La imagen es muy pesada";
        }


        
  
        if(empty($errores)){
     
        //Crear carpeta
        $carpetaImagenes = "../../imagenes/";
        if(!is_dir($carpetaImagenes)){
        mkdir($carpetaImagenes);   // Si no existe, se crea 
    }
        /** Subida de archivos **/

        $nombreImagen = "";

        if($imagen["name"]){

        // Eliminar la imagen previa
        unlink($carpetaImagenes . $propiedad["imagen"]);

        //Generar un nombre unico
        $nombreImagen = md5(uniqid(rand(),true)) . ".jpg";

        // Subir la imagen a la carpeta
        move_uploaded_file($imagen["tmp_name"], $carpetaImagenes  . $nombreImagen);
        } else {
            $nombreImagen =  $propiedad["imagen"]; // Para que no se elimine la imagen automaticamente
        }


       
      

        //Actualizar en la base de datos
        $query = "UPDATE propiedades SET titulo = '${titulo}', precio = ${precio}, imagen= '${nombreImagen}', descripcion = '${descripcion}', 
        habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedores_id = ${vendedores_id} WHERE id =${id}";
       

        // echo $query;

        $resultado = mysqli_query($db, $query);

        if($resultado) {
            //Redireccionar al usuario
            header("Location: /admin?resultado=2");
        }
        }
        
    }
    
    incluirTemplate("header");
?>

    <main class="contenedor seccion">
        <h1>Actualizar</h1>
        <a href="/admin" class="boton boton-verde">Volver </a>

    <?php foreach($errores as $error): ?>
        <div class="alerta error">
              <?php echo $error; ?>
        </div>
             <?php endforeach; ?>
        
        <form class="formulario" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion Generlal</legend>
                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad"   value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png" name="imagen" value="<?php echo $imagen; ?>">

                <img src="/imagenes/<?php echo $imagenPropiedad;?>" class="imagen-small">

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"   ><?php echo $descripcion; ?></textarea>

            </fieldset>

            <fieldset>
                <legend>Informacion de la Propiedad</legend>

                 <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ejemplo: 3" min="1" max="15"   value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ejemplo: 3" min="1" max="15"   value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ejemplo: 3" min="1" max="15"  value="<?php echo $estacionamiento; ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>
                <select name="vendedor">
                    <option value="" >>--- Seleccione --<</option>
                    <?php  while($vendedor = mysqli_fetch_assoc($resultado)) :  ?>
                        <option <?php echo $vendedores_id === $vendedor["id"] ? "selected" : ""; ?>    value="<?php echo $vendedor ["id"]; ?>"> <?php echo $vendedor["nombre"] . " " . $vendedor["apellido"]; ?>   </option> <!-- Se llama a la variable y como es un array asociativo -->
                        <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>

 <?php 
    incluirTemplate("footer");
?>
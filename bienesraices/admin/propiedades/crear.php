<?php 

    require "../../includes/config/database.php";
    $db = conectarBD();


    //Consultar para obtener los vendedores

    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    //Arreglo con mensajes de errores
    $errores = [];

    $titulo = "";
    $precio = "";
    $descripcion = "";
    $habitaciones = "";
    $wc = "";
    $estacionamiento = "";
    $vendedores_id = "";
    $imagen = "";

    // Ejecutar codigo despues de que el usuario envia el formulario

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // $numero = "1HOLA";
        // $numero2 = 1;

        // // Sanitizar
        // $resultado = filter_var($numero, FILTER_SANITIZE_NUMBER_INT); //Elimina lo q no son numeros

        // // Validar

        // $resultado = filter_var($numero2, FILTER_VALIDATE_INT); // Devuelve booleano
        // exit;

        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";


        // echo "<pre>";
        // var_dump($_FILES); Para mostrar la informacion de imagenes
        // echo "</pre>";
        
        
        
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
        if(!$imagen["name"]){
            $errores[] = "Debes subir una imagen";
        }

        //Validar por tamaño(150kb max)
        $medida = 1000*1000;
        if($imagen["size"] > $medida) {
            $errores[] =  "La imagen es muy pesada";
        }


        
        // echo "<pre>";
        // var_dump($errores);
        // echo "</pre>";

      
        if(empty($errores)){
        /** Subida de archivos **/
        //Crear carpeta
        $carpetaImagenes = "../../imagenes/";
        if(!is_dir($carpetaImagenes)){
        mkdir($carpetaImagenes);   // Si no existe, se crea 
    }
        //Generar un nombre unico
        $nombreImagen = md5(uniqid(rand(),true)) . ".jpg";


        // Subir la imagen a la carpeta
        move_uploaded_file($imagen["tmp_name"], $carpetaImagenes  . $nombreImagen);

        //Insertar en la base de datos
        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedores_id) 
        VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedores_id')";

        // echo $query;

        $resultado = mysqli_query($db, $query);

        if($resultado) {
            //Redireccionar al usuario
            header("Location: /admin?resultado=1");
        }
        }
        
    }
    
    require "../../includes/funciones.php";
    incluirTemplate("header");
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin" class="boton boton-verde">Volver </a>

    <?php foreach($errores as $error): ?>
        <div class="alerta error">
              <?php echo $error; ?>
        </div>
             <?php endforeach; ?>
        
        <form class="formulario" method="post" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion Generlal</legend>
                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad"   value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png" name="imagen" value="<?php echo $imagen; ?>">

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

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

 <?php 
    incluirTemplate("footer");
?>
<?php 

    require "../../includes/config/database.php";
    $db = conectarBD();


    //Consultar para obtener los vendedores

    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    //Arreglo con mensajes de errores
    // $errores = [];

    $titulo = "";
    $precio = "";
    $descripcion = "";
    $habitaciones = "";
    $wc = "";
    $estacionamiento = "";
    $vendedores_id = "";

    // Ejecutar codigo despues de que el usuario envia el formulario

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";

        $titulo = $_POST["titulo"];
        $precio = $_POST["precio"];
        $descripcion = $_POST["descripcion"];
        $habitaciones = $_POST["habitaciones"];
        $wc = $_POST["wc"];
        $estacionamiento = $_POST["estacionamiento"];
        $vendedores_id = $_POST["vendedor"];

        // if(!$titulo){
        //     $errores[] = "Debes insertar un titulo";
        // }
        // if(!$precio){
        //     $errores[] = "Debes insertar un precio";
        // }
        // if(!$descripcion){
        //     $errores[] = "Debes insertar un descripcion";
        // }
        // if( strlen($habitaciones) < 50){ //Debe escribir minimo 50 caracteres
        //     $errores[] = "Debes insertar un habitaciones";
        // }
        // if(!$wc){
        //     $errores[] = "Debes insertar un wc";
        // }
        // if(!$estacionamiento){
        //     $errores[] = "Debes insertar un estacionamiento";
        // }
        
        // echo "<pre>";
        // var_dump($errores);
        // echo "</pre>";

      

        //Insertar en la base de datos

        
        $query = "INSERT INTO propiedades (titulo, precio, descripcion, habitaciones, wc, estacionamiento, vendedores_id) 
        VALUES ('$titulo', '$precio', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$vendedores_id')";

        // echo $query;

        $resultado = mysqli_query($db, $query);

        if($resultado) {
            echo "Insertado correctamente";
        }
    }
    
    require "../../includes/funciones.php";
    incluirTemplate("header");
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin" class="boton boton-verde">Volver </a>

    <?php //foreach($errores as $error): ?>
              <?php //echo $error; ?>
             <?php   //endforeach; ?>
        
        <form class="formulario" method="post" action="/admin/propiedades/crear.php">
            <fieldset>
                <legend>Informacion Generlal</legend>
                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" required value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad"  required  value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png"  >

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion" required  ><?php echo $descripcion; ?></textarea>

            </fieldset>

            <fieldset>
                <legend>Informacion de la Propiedad</legend>

                 <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ejemplo: 3" min="1" max="15" required  value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ejemplo: 3" min="1" max="15" required  value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ejemplo: 3" min="1" max="15" required value="<?php echo $estacionamiento; ?>">
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
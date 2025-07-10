<?php 

    // Importar la conexion
    require "includes/config/database.php";
    $db = conectarBD();

    $errores = [];

    // Autenticar el usuario
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $email = mysqli_real_escape_string($db,  filter_var( $_POST["email"], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST["password"]);

        if(!$email){
            $errores[] = "El email es obligatorio o no es valido";
        }
        if(!$password){
            $errores[] = "El password es obligatorio";
        }     
        
        if(empty($errores)){
            // Revisar si el usuario existe
            $query = "SELECT * from usuarios WHERE email = '${email}'";
            $resultado = mysqli_query($db,$query);

            if( $resultado->num_rows ){ //Comprueba si hay datos en una consulta de la bd

                // Revisar si el password es correcto
                $usuario = mysqli_fetch_assoc($resultado);
                // Verificar si el passowrd es correcto o no
                $auth = password_verify($password, $usuario["password"]);  // Returna booleano
                if($auth){
                    // Usuario autenticado
                    session_start();
                    // LLenar el arreglo de la sesion
                    $_SESSION["usuario"] = $usuario["email"];
                    $_SESSION["login"] = true;
                    

                    header("Location: /admin");

                }else{
                    $errores[] = "El password es incorrecto";
                }


            }else {
                $errores[] = "El usuario no existe";
            }
        }
    }


    // Incluye el header
    require "includes/funciones.php";
    incluirTemplate("header");
?>

    <main class="contenedor seccion contenido-centrado">
        <h1>Inicio de Sesion</h1>
        <?php foreach($errores as $error): ?>
                <div class="alerta error">
                    <?php echo $error ?>
                </div>
        <?php endforeach; ?>

        <form class="formulario" method="post">
            <fieldset>
                <legend>Información Personal</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email">

                <label for="password">Contraseña</label>
                <input type="password" name="password" placeholder="Tu Contraseña" id="password">

            </fieldset>
            <input type="submit" value="Iniciar Sesion" class="boton boton-verde">
        </form>
    </main>

 <?php 
    incluirTemplate("footer");
?>
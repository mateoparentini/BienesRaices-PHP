<?php 

function conectarBD() : mysqli {
    $db = mysqli_connect("localhost", "root", "admin", "bienesraices_crud");

    if(!$db){
        echo "Error";
        exit;
    }
    return $db;
}
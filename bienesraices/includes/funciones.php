<?php

require "app.php";

function incluirTemplate(string $nombre, bool $inicio = false){
    include TEMPLATES_URL .  "/${nombre}.php";
}
function estaAutenticado() : bool {
    session_start();

    $auth = $_SESSION["login"]; //Creada en login.php
    if($auth) {
        return true;
    }else{
    return false;
}
    }
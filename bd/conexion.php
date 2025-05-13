<?php
$local = "localhost";
$username = "root";
$password = "";
$base_datos = "noticias_NI";

$conexion = mysqli_connect($local, $username, $password, $base_datos);

if(!$conexion){
    echo "OCURRIO UN ERROR AL CONECTAR LA BASE DE DATOS";
} else {
    echo "CONEXION ESTABLECIDA";
}

?>
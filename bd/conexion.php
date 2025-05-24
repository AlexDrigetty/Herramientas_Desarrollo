<?php
$local = "localhost:3306";
$username = "root";
$password = "";
$base_datos = "noticias_ni";

$conexion = mysqli_connect($local, $username, $password, $base_datos);

if(!$conexion){
    echo "OCURRIO UN ERROR AL CONECTAR LA BASE DE DATOS";
} else {
    echo "CONEXION ESTABLECIDA";
}

?>
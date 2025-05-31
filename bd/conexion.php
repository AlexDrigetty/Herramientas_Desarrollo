<?php
$local = "localhost:3308";
$username = "root";
$password = "";
$base_datos = "noticias_ni";

$conn = mysqli_connect($local, $username, $password, $base_datos);

if(!$conn){
    echo "OCURRIO UN ERROR AL CONECTAR LA BASE DE DATOS";
} else {
    echo "CONEXION ESTABLECIDA";
}
$conn->set_charset("utf8mb4");
?>
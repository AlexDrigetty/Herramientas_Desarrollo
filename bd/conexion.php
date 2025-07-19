<?php
$local = "localhost";
$username = "root";
$password = "master.";
$base_datos = "noticia_ni";

$conn = mysqli_connect($local, $username, $password, $base_datos);


$conn->set_charset("utf8mb4");
?>
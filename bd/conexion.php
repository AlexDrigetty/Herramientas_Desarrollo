<?php
$local = "localhost";
$username = "root";
$password = "master.";
$base_datos = "noticias_in";

$conn = mysqli_connect($local, $username, $password, $base_datos);


try {
    $pdo = new PDO("mysql:host=$local;dbname=$base_datos;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$conn->set_charset("utf8mb4");
?>
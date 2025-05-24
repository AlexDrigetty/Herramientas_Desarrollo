<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$titulo = $_POST['titulo'];
$categoria = $_POST['categoria'];
$contenido = $_POST['contenido'];
$autor_id = $_SESSION['usuario_id'];

$sql = "INSERT INTO noticias (titulo, categoria, contenido, autor_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $titulo, $categoria, $contenido, $autor_id);
$stmt->execute();

echo "Noticia publicada correctamente. <a href='panel.php'>Volver</a>";
?>

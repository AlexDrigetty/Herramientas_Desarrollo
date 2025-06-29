<?php
session_start();
require_once '../bd/conexion.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../Publico/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de noticia no válido";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../Admin/Todas_Noticias.php'));
    exit;
}

$noticia_id = (int)$_GET['id'];

// Obtener información de la noticia para eliminar la imagen asociada
$sql = "SELECT imagen_portada FROM noticias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticia_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "La noticia no existe";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../Admin/Todas_Noticias.php'));
    exit;
}

$noticia = $result->fetch_assoc();

// Eliminar la noticia de la base de datos
$sql_delete = "DELETE FROM noticias WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $noticia_id);

if ($stmt_delete->execute()) {
    // Eliminar la imagen asociada si no es la imagen por defecto
    if ($noticia['imagen_portada'] !== 'default.jpg') {
        $imagen_path = "../imagenes/" . $noticia['imagen_portada'];
        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }
    }
    
    $_SESSION['success'] = "Noticia eliminada correctamente";
} else {
    $_SESSION['error'] = "Error al eliminar la noticia: " . $conn->error;
}

// Redirigir a la página anterior o a la lista de noticias
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../Admin/Todas_Noticias.php'));
exit;
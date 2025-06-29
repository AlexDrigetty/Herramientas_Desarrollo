<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../Publico/login.php");
    exit;
}

require_once '../bd/conexion.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID de noticia no proporcionado";
    header("Location: ../Admin/Programar_Noticias.php");
    exit;
}

$noticia_id = (int)$_GET['id'];

// Obtener la noticia programada
$sql = "SELECT * FROM noticias WHERE id = ? AND estado_id = 2";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticia_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Noticia no encontrada o ya no está programada";
    header("Location: ../Admin/Programar_Noticias.php");
    exit;
}

// Actualizar el estado a Publicado (3) y establecer fecha de publicación
$sql_update = "UPDATE noticias 
               SET estado_id = 3, fecha_publicacion = NOW() 
               WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $noticia_id);

if ($stmt_update->execute()) {
    // Marcar la tarea programada como completada si existe
    $sql_tarea = "UPDATE tareas_programadas 
                  SET completada = 1, fecha_completada = NOW() 
                  WHERE noticia_id = ? AND accion = 'publicar' AND completada = 0";
    $stmt_tarea = $conn->prepare($sql_tarea);
    $stmt_tarea->bind_param("i", $noticia_id);
    $stmt_tarea->execute();
    
    $_SESSION['success'] = "Noticia publicada correctamente";
} else {
    $_SESSION['error'] = "Error al publicar la noticia: ".$conn->error;
}

header("Location: ../Admin/Programar_Noticias.php");
exit;
?>
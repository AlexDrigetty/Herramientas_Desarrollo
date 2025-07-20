<?php
session_start();
require_once '../bd/conexion.php';

// Verificar permisos de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'ADMIN') {
    header('Location: ../Publico/login.php');
    exit;
}

// Validar parámetros
if (!isset($_GET['id']) || !isset($_GET['estado'])) {
    $_SESSION['error'] = "Parámetros inválidos";
    header('Location: ../Admin/Revision_Noticias.php');
    exit;
}

$noticia_id = (int)$_GET['id'];
$estado_id = (int)$_GET['estado'];

// Validar estado (4: Rechazado, 2: Programado, 3: Publicado)
if (!in_array($estado_id, [4, 2, 3])) {
    $_SESSION['error'] = "Estado no válido";
    header('Location: ../Admin/Revision_Noticias.php');
    exit;
}

// Construir consulta según el estado
if ($estado_id == 3) { // Publicar ahora
    $sql = "UPDATE noticias SET 
            estado_id = 3, 
            fecha_publicacion = NOW(),
            fecha_programada = NULL
            WHERE id = ?";
} elseif ($estado_id == 2) { // Programar
    // Obtener fecha programada si existe
    $fecha_programada = isset($_GET['fecha_programada']) ? $_GET['fecha_programada'] : null;
    
    $sql = "UPDATE noticias SET 
            estado_id = 2,
            fecha_programada = ?,
            fecha_publicacion = NULL
            WHERE id = ?";
} else { // Rechazar (estado_id = 4)
    $sql = "UPDATE noticias SET 
            estado_id = 4,
            fecha_publicacion = NULL,
            fecha_programada = NULL
            WHERE id = ?";
}

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['error'] = "Error en la consulta: " . $conn->error;
    header('Location: ../Admin/Revision_Noticias.php');
    exit;
}

if ($estado_id == 2) {
    $stmt->bind_param("si", $fecha_programada, $noticia_id);
} else {
    $stmt->bind_param("i", $noticia_id);
}

if ($stmt->execute()) {
    // Mensajes de éxito personalizados
    if ($estado_id == 3) {
        $_SESSION['success'] = "Noticia publicada correctamente";
    } elseif ($estado_id == 2) {
        $_SESSION['success'] = "Noticia programada para publicación";
    } else {
        $_SESSION['success'] = "Noticia rechazada";
    }
} else {
    $_SESSION['error'] = "Error al actualizar el estado: " . $conn->error;
}

header('Location: ../Admin/Revision_Noticias.php');
exit;
?>
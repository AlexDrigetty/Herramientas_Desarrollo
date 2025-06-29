<?php
require_once '../bd/conexion.php';

$now = date('Y-m-d H:i:s');
$sql = "SELECT id FROM noticias WHERE estado_id = 2 AND fecha_programada <= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $now);
$stmt->execute();
$result = $stmt->get_result();

while ($noticia = $result->fetch_assoc()) {
    // Actualizar estado a publicado y establecer fecha de publicación
    $update = "UPDATE noticias SET estado_id = 3, fecha_publicacion = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update);
    $stmt_update->bind_param("si", $now, $noticia['id']);
    $stmt_update->execute();
    
    // Opcional: Registrar en un log
    file_put_contents('log_publicaciones.txt', date('Y-m-d H:i:s')." - Noticia ID {$noticia['id']} publicada\n", FILE_APPEND);
}

echo "Proceso de publicación automática completado";
?>
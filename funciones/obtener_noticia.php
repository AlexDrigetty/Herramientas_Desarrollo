<?php
require_once '../bd/conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de noticia no proporcionado']);
    exit;
}

$id = intval($_GET['id']);

// Obtener datos de la noticia
$sql = "SELECT n.*, c.nombre as categoria_nombre, 
        DATE_FORMAT(n.fecha_programada, '%Y-%m-%d %H:%i') as fecha_programada_format
        FROM noticias n
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Noticia no encontrada']);
    exit;
}

$noticia = $result->fetch_assoc();

// Obtener todas las categorías para el select
$categorias = [];
$sql_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre";
$result_cat = $conn->query($sql_categorias);
while ($row = $result_cat->fetch_assoc()) {
    $categorias[] = $row;
}

echo json_encode([
    'noticia' => $noticia,
    'categorias' => $categorias
]);
?>
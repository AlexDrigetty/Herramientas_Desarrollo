<?php
require_once '../bd/conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de usuario no proporcionado']);
    exit;
}

$userId = intval($_GET['id']);

$sql = "SELECT u.id, u.nombre, u.apellido, u.correo, u.rol_id, r.nombre as rol 
        FROM usuarios u 
        JOIN roles r ON u.rol_id = r.id
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

$usuario = $result->fetch_assoc();
echo json_encode($usuario);
?>
<?php
session_start();
require_once '../bd/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$comentario_id = $_POST['comentario_id'];
$tipo = $_POST['tipo'];

// Verificar si el usuario ya votó este comentario
$query = "SELECT tipo FROM votos_comentarios 
          WHERE usuario_id = ? AND comentario_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $usuario_id, $comentario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $voto_actual = $result->fetch_assoc()['tipo'];
    
    // Si el usuario está intentando votar lo mismo, eliminar el voto
    if ($voto_actual === $tipo) {
        $query = "DELETE FROM votos_comentarios 
                  WHERE usuario_id = ? AND comentario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $usuario_id, $comentario_id);
        $stmt->execute();
    } else {
        // Si está cambiando su voto (like -> dislike o viceversa)
        $query = "UPDATE votos_comentarios SET tipo = ? 
                  WHERE usuario_id = ? AND comentario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $tipo, $usuario_id, $comentario_id);
        $stmt->execute();
    }
} else {
    // Nuevo voto
    $query = "INSERT INTO votos_comentarios (usuario_id, comentario_id, tipo) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $usuario_id, $comentario_id, $tipo);
    $stmt->execute();
}

// Obtener conteo actualizado de votos
$query_likes = "SELECT COUNT(*) FROM votos_comentarios 
                WHERE comentario_id = ? AND tipo = 'like'";
$stmt_likes = $conn->prepare($query_likes);
$stmt_likes->bind_param("i", $comentario_id);
$stmt_likes->execute();
$likes = $stmt_likes->get_result()->fetch_row()[0];

$query_dislikes = "SELECT COUNT(*) FROM votos_comentarios 
                   WHERE comentario_id = ? AND tipo = 'dislike'";
$stmt_dislikes = $conn->prepare($query_dislikes);
$stmt_dislikes->bind_param("i", $comentario_id);
$stmt_dislikes->execute();
$dislikes = $stmt_dislikes->get_result()->fetch_row()[0];

// Verificar el voto actual del usuario
$query_user_vote = "SELECT tipo FROM votos_comentarios 
                    WHERE usuario_id = ? AND comentario_id = ?";
$stmt_user_vote = $conn->prepare($query_user_vote);
$stmt_user_vote->bind_param("ii", $usuario_id, $comentario_id);
$stmt_user_vote->execute();
$user_vote_result = $stmt_user_vote->get_result();
$user_vote = $user_vote_result->num_rows > 0 ? $user_vote_result->fetch_assoc()['tipo'] : null;

echo json_encode([
    'success' => true,
    'likes' => $likes,
    'dislikes' => $dislikes,
    'user_vote' => $user_vote
]);
?>
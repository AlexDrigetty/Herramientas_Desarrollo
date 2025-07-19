<?php
session_start();
require_once '../bd/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario_id'])) {
    $comentario_id = (int)$_POST['comentario_id'];
    
    try {
        // Verificar permisos
        $stmt = $pdo->prepare("
            SELECT usuario_id FROM comentarios 
            WHERE id = :comentario_id
        ");
        $stmt->execute(['comentario_id' => $comentario_id]);
        $comentario = $stmt->fetch();
        
        if ($comentario && ($_SESSION['usuario_id'] == $comentario['usuario_id'] || $_SESSION['rol_id'] == 0)) {
            // Eliminar comentario y sus respuestas
            $pdo->prepare("DELETE FROM comentarios WHERE id = ? OR respuesta_id = ?")
                ->execute([$comentario_id, $comentario_id]);
            
            header("Location: " . $_SERVER['HTTP_REFERER'] . "#comentarios");
            exit();
        }
    } catch (PDOException $e) {
        die("Error al eliminar el comentario: " . $e->getMessage());
    }
}

header("Location: ../inicio.php");
exit();
?>
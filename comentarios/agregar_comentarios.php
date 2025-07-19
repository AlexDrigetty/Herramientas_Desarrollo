<?php
session_start();
require_once '../bd/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticia_id = filter_input(INPUT_POST, 'noticia_id', FILTER_VALIDATE_INT);
    $contenido = trim($_POST['contenido']);
    $respuesta_id = isset($_POST['respuesta_id']) ? filter_input(INPUT_POST, 'respuesta_id', FILTER_VALIDATE_INT) : null;

    if ($noticia_id && !empty($contenido)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comentarios (noticia_id, usuario_id, contenido, respuesta_id)
                VALUES (:noticia_id, :usuario_id, :contenido, :respuesta_id)
            ");
            
            $stmt->execute([
                'noticia_id' => $noticia_id,
                'usuario_id' => $_SESSION['usuario_id'],
                'contenido' => $contenido,
                'respuesta_id' => $respuesta_id
            ]);

            header("Location: " . $_SERVER['HTTP_REFERER'] . "#comentarios");
            exit();
        } catch (PDOException $e) {
            die("Error al guardar el comentario: " . $e->getMessage());
        }
    }
}

header("Location: ../inicio.php");
exit();
?>
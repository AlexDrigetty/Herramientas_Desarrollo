<?php
session_start();
require_once '../bd/conexion.php';

if (!isset($_SESSION['usuario_id']) || empty($_POST['noticia_id']) || empty($_POST['contenido'])) {
    header('Location: ../index.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$noticia_id = $_POST['noticia_id'];
$contenido = trim($_POST['contenido']);
$padre_id = isset($_POST['comentario_padre_id']) ? (int)$_POST['comentario_padre_id'] : null;

// Validar que la noticia existe
$query = "SELECT id FROM noticias WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $noticia_id);
$stmt->execute();
if (!$stmt->get_result()->num_rows) {
    die("Noticia no encontrada");
}

// Insertar comentario
$query = "INSERT INTO comentarios (contenido, usuario_id, noticia_id, comentario_padre_id) 
          VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("siii", $contenido, $usuario_id, $noticia_id, $padre_id);
$stmt->execute();

header("Location: ../noticia.php?id=$noticia_id#comentarios");
exit;
?>
<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
</script>
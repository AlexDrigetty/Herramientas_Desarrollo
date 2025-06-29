<?php
include '../bd/conexion.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: Programar_Noticias.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: Programar_Noticias.php");
    exit();
}

$noticia = $result->fetch_assoc();

// Mostrar previsualización
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsualización: <?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="../Css/noticia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>Vista Previa de Noticia Programada</h1>
            <p>Esta es una vista previa de cómo se verá la noticia cuando sea publicada.</p>
            <p><strong>Programada para:</strong> <?= date('d/m/Y H:i', strtotime($noticia['fecha_programada'])) ?></p>
        </div>
        
        <article class="noticia-detalle">
            <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
            
            <div class="noticia-meta">
                <span class="categoria" style="background-color: #<?= htmlspecialchars($noticia['categoria_color']) ?>">
                    <?= htmlspecialchars(ucfirst($noticia['categoria_nombre'])) ?>
                </span>
                <span class="fecha"><?= date('d/m/Y H:i', strtotime($noticia['fecha_programada'])) ?></span>
                <span class="tipo"><?= htmlspecialchars(ucfirst($noticia['tipo_noticia'])) ?></span>
            </div>
            
            <?php if (!empty($noticia['imagen_portada'])): ?>
            <div class="noticia-imagen">
                <img src="../uploads/<?= htmlspecialchars($noticia['imagen_portada']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
            </div>
            <?php endif; ?>
            
            <div class="noticia-resumen">
                <p><?= nl2br(htmlspecialchars($noticia['resumen'])) ?></p>
            </div>
            
            <div class="noticia-contenido">
                <?= $noticia['contenido'] ?>
            </div>
        </article>
        
        <div class="preview-footer">
            <p>Esta noticia está programada para publicación automática.</p>
            <button onclick="window.close()" class="btn btn-primary">Cerrar Vista Previa</button>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
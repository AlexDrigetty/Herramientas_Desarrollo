<?php
require_once '../bd/conexion.php';

$stmt = $pdo->prepare("
    SELECT n.titulo, n.slug, n.resumen, n.fecha_publicacion, n.imagen_portada, 
           c.nombre AS categoria, c.color, u.nombre AS autor
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN usuarios u ON n.autor_id = u.id
    WHERE n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
    ORDER BY n.fecha_publicacion DESC
");
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Noticias</title>
    <link rel="stylesheet" href="../Css/noti.css">
</head>
<body>
    <h1>Últimas Noticias</h1>

    <div class="contenedor">
        <?php foreach ($noticias as $noticia): ?>
            <div class="noticia">
                <img src="imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>" alt="Portada">
                <div class="info">
                    <span class="categoria" style="background-color: <?= $noticia['color'] ?>;">
                        <?= htmlspecialchars($noticia['categoria']) ?>
                    </span>
                    <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
                    <p><?= htmlspecialchars($noticia['resumen']) ?></p>
                    <small>Por <?= htmlspecialchars($noticia['autor']) ?> | 
                        <?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?>
                    </small>
                    <a href="noticp.php?slug=<?= urlencode($noticia['slug']) ?>" class="ver-mas">Ver más</a>
                </div>
            </div>
            
        <?php endforeach; ?>
    </div>
</body>
</html>

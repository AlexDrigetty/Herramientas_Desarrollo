<?php
session_start();
require_once '../bd/conexion.php';

if (!isset($_GET['slug'])) {
    die("Noticia no especificada.");
}

$slug = $_GET['slug'];

$stmt = $pdo->prepare("
    SELECT n.titulo, n.contenido, n.fecha_publicacion, n.imagen_portada,
           c.nombre AS categoria, c.color, u.nombre AS autor, u.apellido
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN usuarios u ON n.autor_id = u.id
    WHERE n.slug = :slug AND n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
");
$stmt->execute(['slug' => $slug]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    die("Noticia no encontrada o no publicada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="../Css/noticp.css">
    <link rel="stylesheet" href="../Css/comentarios.css">

</head>
<body>
    <article class="detalle-noticia">
        <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <p>
            <span class="categoria" style="background-color: <?= $noticia['color'] ?>;">
                <?= htmlspecialchars($noticia['categoria']) ?>
            </span>
            | Publicado el <?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?>
            | Por <?= htmlspecialchars($noticia['autor']) ?> <?= htmlspecialchars($noticia['apellido']) ?>
        </p>
        <img src="imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>" alt="Imagen de portada" class="imagen-detalle">
        <div class="contenido">
            <?= nl2br($noticia['contenido']) ?>
        </div>
        <a href="index.php" class="volver">← Volver</a>
    </article>

    <section class="comentarios mt-5">
    <h3 class="mb-4">Comentarios</h3>
    
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <div class="card mb-4">
        <div class="card-body">
            <form action="../comentarios/agregar_comentarios.php" method="POST">
                <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
                <div class="form-group">
                    <textarea class="form-control" name="contenido" rows="3" 
                              placeholder="Escribe tu comentario..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Publicar comentario</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <a href="login.php">Inicia sesión</a> para dejar un comentario.
    </div>
    <?php endif; ?>

    <div id="lista-comentarios">
        <?php include '../comentarios/listar_comentarios.php'; ?>
    </div>
</section>
</body>
</html>

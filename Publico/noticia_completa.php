<?php
require_once '../bd/conexion.php';
include "../Publico/navar.php";

// Verificar conexión y parámetro ID
if (!$pdo) die("Error de conexión");
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("ID inválido");

$id_noticia = (int)$_GET['id'];

// Obtener noticia principal
$stmt = $pdo->prepare("
    SELECT n.id, n.titulo, n.contenido, n.fecha_publicacion, n.imagen_portada,
           c.nombre AS categoria, c.color, u.nombre AS autor, u.apellido
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN usuarios u ON n.autor_id = u.id
    WHERE n.id = :id
");
$stmt->execute(['id' => $id_noticia]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) die("Noticia no encontrada");

// Obtener noticias destacadas (ejemplo: las 5 más recientes)
$destacados = $pdo->query("
    SELECT n.id, n.titulo, n.resumen, n.imagen_portada, n.fecha_publicacion,
           c.nombre AS categoria, c.color
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    WHERE n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
    ORDER BY n.fecha_publicacion DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/noticia_completa.css">
    <link rel="stylesheet" href="../Css/comentarios.css">
    <link rel="stylesheet" href="../Css/contenido.css">
    <link rel="stylesheet" href="../Css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="contenedor-principal">
        <div class="row">
            <div class="col-12 col-lg-9 mt-0">
                <a href="inicio.php" class="volver mb-4">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <article class="detalle-noticia mb-5">
                    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
                    <div class="meta-info">
                        <span class="categoria" style="background-color: <?= $noticia['color'] ?>">
                            <?= htmlspecialchars($noticia['categoria']) ?>
                        </span>
                        <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?></span>
                    </div>

                    <?php if (!empty($noticia['imagen_portada']) && file_exists("../imagenes/" . $noticia['imagen_portada'])): ?>
                        <img src="../imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>"
                            alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                            class="imagen-detalle">
                    <?php else: ?>
                        <div class="imagen-default imagen-detalle">
                            <i class="fas fa-newspaper"></i>
                            <span>Noticia sin imagen</span>
                        </div>
                    <?php endif; ?>

                    <div class="contenido">
                        <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>
                    </div>

                </article>

                <!-- Sección de comentarios -->
                <section class="comentarios mb-5">
                    <h3 class="mb-4">Caja de Comentarios</h3>

                    <?php if (isset($_SESSION['usuario_id'])): ?>
                            <div class="card-body">
                                <form action="../comentarios/agregar_comentarios.php" method="POST">
                                    <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
                                    <textarea class="form-control" name="contenido"  placeholder="Escribe tu comentario..." required style="height: 200px;"></textarea>
                                    <button type="submit" class="btn btn-primary mt-3">
                                        <i class="fas fa-paper-plane"></i> Publicar
                                    </button>
                                </form>
                            </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <a href="../login.php"><i class="fas fa-sign-in-alt"></i> Inicia sesión</a> para comentar
                        </div>
                    <?php endif; ?>

                    <div id="lista-comentarios">
                        <?php include '../comentarios/listar_comentarios.php'; ?>
                    </div>
                </section>
            </div>

            <!-- Columna lateral con destacados -->
            <div class="col-12 col-lg-3 mt-4 mt-lg-0">
                <div class="destacados-container">
                    <div class="row">
                        <div class="col-12 noticias mb-3">
                            <h4>Noticias Destacadas</h4>
                        </div>
                    </div>


                    <div class="row" id="featured-news">
                        <?php foreach ($destacados as $destacado): ?>
                            <div class="col-12 mb-3">
                                <div class="noticia-destacada mb-4">
                                    <a href="noticia_completa.php?id=<?= $destacado['id'] ?>">
                                        <?php if (!empty($destacado['imagen_portada'])): ?>
                                            <img src="../imagenes/<?= htmlspecialchars($destacado['imagen_portada']) ?>"
                                                class="imagen-destacada">
                                        <?php else: ?>
                                            <div class="imagen-default destacada">
                                                <i class="fas fa-newspaper"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="mb-3"><?= htmlspecialchars($destacado['titulo']) ?></h5>
                                    </a>
                                    <div class="meta-destacada" style="display: flex; justify-content: space-between;">
                                        <span class="categoria" style="background-color: <?= $destacado['color'] ?>">
                                            <?= htmlspecialchars($destacado['categoria']) ?>
                                        </span>
                                        <span class="fecha"><i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($destacado['fecha_publicacion'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/comentarios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'footer.php'; ?>
</body>

</html>
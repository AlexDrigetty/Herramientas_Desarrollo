<?php
include 'navar.php';
include '../bd/conexion.php';

include("../Admin/checkout_admin.php");

if($admin_true) {
    include '../Admin/admin_navbar.php'; // Ruta relativa correcta
}

// Obtener noticias nacionales publicadas
$sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido, 
               c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.tipo_noticia = 'nacional' 
        AND n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
        ORDER BY n.fecha_publicacion DESC";

$noticias = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nacionales | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/noticias.css">
    <link rel="stylesheet" href="../Css/inicio.css">
</head>

<body>
    <section class="header-section texto_nacional">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fas fa-newspaper me-2"></i> Noticias Nacionales</h1>
                    <div class="search w-100 py-2 d-flex justify-content-center gap-1">
                        <input type="text" id="search-input" class="w-50 border-1 rounded-1 p-2" placeholder="Comenzar Busqueda">
                        <button id="search-button" class="rounded-1 border-1 px-5 py-2">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row news-container">
            <?php while ($noticia = $noticias->fetch_assoc()): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="news">
                        <div class="imagen">
                            <img src="../imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>"
                                alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                        </div>
                        <div class="contenido-noticia">
                            <div class="contenido-etiqueta mb-2">
                                <span class="categoria" style="background: <?= $noticia['categoria_color'] ?>">
                                    <?= htmlspecialchars($noticia['categoria_nombre']) ?>
                                </span>
                                <span class="tipo">Nacional</span>
                            </div>
                            <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                            <p><?= htmlspecialchars($noticia['resumen']) ?></p>
                            <div class="metas mb-1">
                                <span><i class="far fa-clock"></i>
                                    <?= fecha_relativa($noticia['fecha_publicacion']) ?>
                                </span>
                                <a href="noticia_completa.php?id=<?= $noticia['id'] ?>" class="vermas">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Paginación (puedes implementarla según necesidad) -->
        <div class="row">
            <div class="pagination-container mb-5">
                <ul class="pagination" id="pagination">
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item active"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/nacionales.js"></script>

    <?php
    include 'footer.php';

    // Función para mostrar fecha relativa
    function fecha_relativa($fecha)
    {
        $ahora = new DateTime();
        $fecha_noticia = new DateTime($fecha);
        $diferencia = $ahora->diff($fecha_noticia);

        if ($diferencia->y > 0) return "Hace " . $diferencia->y . " año(s)";
        if ($diferencia->m > 0) return "Hace " . $diferencia->m . " mes(es)";
        if ($diferencia->d > 0) return "Hace " . $diferencia->d . " día(s)";
        if ($diferencia->h > 0) return "Hace " . $diferencia->h . " hora(s)";
        if ($diferencia->i > 0) return "Hace " . $diferencia->i . " minuto(s)";
        return "Hace unos segundos";
    }
    ?>
</body>

</html>

<?php
// Función para mostrar el tiempo transcurrido
function tiempo_transcurrido($fecha)
{
    $fecha = new DateTime($fecha);
    $ahora = new DateTime();
    $diferencia = $ahora->diff($fecha);

    if ($diferencia->y > 0) {
        return "Hace " . $diferencia->y . " año" . ($diferencia->y > 1 ? "s" : "");
    } elseif ($diferencia->m > 0) {
        return "Hace " . $diferencia->m . " mes" . ($diferencia->m > 1 ? "es" : "");
    } elseif ($diferencia->d > 0) {
        return "Hace " . $diferencia->d . " día" . ($diferencia->d > 1 ? "s" : "");
    } elseif ($diferencia->h > 0) {
        return "Hace " . $diferencia->h . " hora" . ($diferencia->h > 1 ? "s" : "");
    } elseif ($diferencia->i > 0) {
        return "Hace " . $diferencia->i . " minuto" . ($diferencia->i > 1 ? "s" : "");
    } else {
        return "Hace unos segundos";
    }
}
?>
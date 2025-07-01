<?php
include 'navar.php';
include '../bd/conexion.php';
include("../Admin/checkout_admin.php");

if ($admin_true) {
    include '../Admin/admin_navbar.php';
}

// Obtener todas las noticias nacionales publicadas (sin paginación SQL)
$sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido, 
               c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.tipo_noticia = 'nacional' 
        AND n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
        ORDER BY n.fecha_publicacion DESC";

$noticias = $conn->query($sql);

// Preparar noticias para JSON
$noticias_array = [];
while ($noticia = $noticias->fetch_assoc()) {
    $noticias_array[] = [
        'id' => $noticia['id'],
        'titulo' => $noticia['titulo'],
        'imagen' => '../imagenes/' . $noticia['imagen_portada'],
        'resumen' => $noticia['resumen'],
        'tipo' => 'nacional',
        'fecha' => $noticia['fecha_publicacion'],
        'categoria_nombre' => $noticia['categoria_nombre'],
        'categoria_color' => $noticia['categoria_color'],
        'enlace' => 'noticia_completa.php?id=' . $noticia['id']
    ];
}
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
        <div class="row news-container" id="news-container">
        </div>

        <div class="row">
            <div class="pagination-container mb-5">
                <ul class="pagination" id="pagination">
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const noticiasData = <?php echo json_encode($noticias_array); ?>;
    </script>
    <script src="../js/nacionales.js"></script>

    <?php
    include 'footer.php';

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
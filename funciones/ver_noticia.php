<?php
include '../Publico/navar.php';
require_once '../bd/conexion.php';

// Verificar que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar que se proporcionó un ID de noticia
if (!isset($_GET['id'])) {
    header('Location: perfil_usuario.php');
    exit;
}

$noticia_id = (int)$_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos de la noticia, verificando que pertenece al usuario
$sql = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre, c.color as categoria_color, 
               en.nombre as estado_nombre
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        JOIN estados_noticia en ON n.estado_id = en.id
        WHERE n.id = ? AND n.autor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $noticia_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();

if (!$noticia) {
    $_SESSION['error'] = "Noticia no encontrada o no tienes permiso para verla";
    header('Location: perfil_usuario.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Noticia | <?= htmlspecialchars($noticia['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/usuario.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <style>
        .estado-badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
        }
        .estado-pendiente { background-color: #ffc107; color: #000; }
        .estado-publicado { background-color: #28a745; color: #fff; }
        .estado-programado { background-color: #17a2b8; color: #fff; }
        .estado-rechazado { background-color: #dc3545; color: #fff; }
        .contenido-noticia {
            line-height: 1.6;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-newspaper me-2"></i>Mi Noticia</h2>
            <a href="../Usuario/perfil_usuario.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al perfil
            </a>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class ="mb-4" style="font-size: 35px;"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                        <div class="d-flex align-items-center me-3 gap-2 mt-2 flex-wrap mb-4 ">
                            <span class="badge estado-badge estado-<?= strtolower($noticia['estado_nombre']) ?>">
                                <?= htmlspecialchars($noticia['estado_nombre']) ?>
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i><?= date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])) ?>
                            </span>
                            <?php if ($noticia['estado_nombre'] == 'Programado' && $noticia['fecha_programada']): ?>
                                <span class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Programada para: <?= date('d/m/Y H:i', strtotime($noticia['fecha_programada'])) ?>
                                </span>
                            <?php elseif ($noticia['estado_nombre'] == 'Publicado' && $noticia['fecha_publicacion']): ?>
                                <span class="text-muted">
                                    <i class="fas fa-check-circle me-1"></i>Publicada el: <?= date('d/m/Y H:i', strtotime($noticia['fecha_publicacion'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($noticia['imagen_portada'] && $noticia['imagen_portada'] != 'default.jpg'): ?>
                    <div class="mb-4 text-center">
                        <img src="../Imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>" 
                             alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                             class="img-fluid rounded w-100" style="object-fit: contain; height: 450px">
                    </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h5>Resumen:</h5>
                    <p class="lead"><?= nl2br(htmlspecialchars($noticia['resumen'])) ?></p>
                </div>
                
                <div class="contenido-noticia">
                    <h5>Contenido completo:</h5>
                    <div class="border p-3 rounded bg-light">
                        <?= $noticia['contenido'] ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($noticia['estado_nombre'] == 'Rechazado'): ?>
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Noticia rechazada</h5>
                <p class="mb-0">Tu noticia ha sido revisada y no cumple con los requisitos para su publicación. Puedes editar la noticia y volver a enviarla para revisión.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../Publico/footer.php'; ?>
</body>
</html>
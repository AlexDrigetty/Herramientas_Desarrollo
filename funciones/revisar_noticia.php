<?php
include '../Admin/admin_navbar.php';
include '../Admin/admin_auth.php';

require_once '../bd/conexion.php';

if (!isset($_GET['id'])) {
    header('Location: programar_noticia.php');
    exit;
}

$noticia_id = (int)$_GET['id'];

// Obtener datos de la noticia
$sql = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticia_id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();

if (!$noticia) {
    $_SESSION['error'] = "Noticia no encontrada";
    header('Location: programar_noticia.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Noticia | Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/admin.css">
</head>
<body>
    <main>
        <?php include '../Admin/slider.php'; ?>
        <div id="main-content">
            <div class="container py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-search me-2"></i>Revisar Noticia</h2>
                
                    <div>
                        <a href="../funciones/cambiar_estado_noticia.php?id=<?= $noticia['id'] ?>&estado=3" class="btn btn-success">
                            <i class="fas fa-check"></i>Aprobar
                        </a>
                        <p></p>
                        <form action="cambiar_estado_noticia.php" method="GET">
                            <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
                            <input type="hidden" name="estado" value="2">
                            <input type="datetime-local" name="fecha_programada" required>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-clock"></i> Programar
                            </button>
                        </form>
                        <a href="../funciones/cambiar_estado_noticia.php?id=<?= $noticia['id'] ?>&estado=4" class="btn btn-danger">
                            <i class="fas fa-times"></i>Rechazar
                        </a>
                        <a href="../Admin/Revision_Noticias.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="badge" style="background-color: <?= $noticia['categoria_color'] ?>">
                                        <?= htmlspecialchars($noticia['categoria_nombre']) ?>
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($noticia['autor']) ?>
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i><?= date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($noticia['imagen_portada'] && $noticia['imagen_portada'] != 'default.jpg'): ?>
                            <div class="mb-4">
                                <img src="../imagenes/<?= htmlspecialchars($noticia['imagen_portada']) ?>" 
                                     alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                                     class="img-fluid rounded">
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5>Resumen:</h5>
                            <p><?= nl2br(htmlspecialchars($noticia['resumen'])) ?></p>
                        </div>
                        
                        <div>
                            <h5>Contenido:</h5>
                            <div class="border p-3 rounded bg-light">
                                <?= $noticia['contenido'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
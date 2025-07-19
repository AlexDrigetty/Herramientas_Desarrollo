<?php include 'admin_navbar.php'; ?>
<?php include 'admin_auth.php'; ?>
<?php
require_once '../bd/conexion.php';

// Consultas para las tarjetas de resumen
$total_noticias = $conn->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'];
$pendientes = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 1")->fetch_assoc()['total'];
$programadas = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 2")->fetch_assoc()['total'];
$publicadas_hoy = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 3 AND DATE(fecha_publicacion) = CURDATE()")->fetch_assoc()['total'];

// Consultas para estadísticas mensuales
$noticias_mes_actual = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
    AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

$noticias_mes_pasado = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE MONTH(fecha_creacion) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha_creacion) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
")->fetch_assoc()['total'];

// Calcular variación porcentual para noticias totales
$variacion_total = 0;
if ($noticias_mes_pasado > 0) {
    $variacion_total = (($noticias_mes_actual - $noticias_mes_pasado) / $noticias_mes_pasado) * 100;
}

// Consultas para pendientes mensuales
$pendientes_mes_actual = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 1
    AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

$pendientes_mes_pasado = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 1
    AND MONTH(fecha_creacion) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha_creacion) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
")->fetch_assoc()['total'];

$variacion_pendientes = 0;
if ($pendientes_mes_pasado > 0) {
    $variacion_pendientes = (($pendientes_mes_actual - $pendientes_mes_pasado) / $pendientes_mes_pasado) * 100;
}

// Consultas para programadas mensuales
$programadas_mes_actual = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 2
    AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

$programadas_mes_pasado = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 2
    AND MONTH(fecha_creacion) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha_creacion) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
")->fetch_assoc()['total'];

$variacion_programadas = 0;
if ($programadas_mes_pasado > 0) {
    $variacion_programadas = (($programadas_mes_actual - $programadas_mes_pasado) / $programadas_mes_pasado) * 100;
}

// Consultas para publicadas mensuales
$publicadas_mes_actual = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 3
    AND MONTH(fecha_publicacion) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_publicacion) = YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

$publicadas_mes_pasado = $conn->query("
    SELECT COUNT(*) as total 
    FROM noticias 
    WHERE estado_id = 3
    AND MONTH(fecha_publicacion) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    AND YEAR(fecha_publicacion) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
")->fetch_assoc()['total'];

$variacion_publicadas = 0;
if ($publicadas_mes_pasado > 0) {
    $variacion_publicadas = (($publicadas_mes_actual - $publicadas_mes_pasado) / $publicadas_mes_pasado) * 100;
}

// Consulta para las noticias recientes
$recientes = $conn->query("
    SELECT n.*, c.nombre as categoria_nombre, en.nombre as estado_nombre,
           DATE_FORMAT(n.fecha_creacion, '%Y-%m-%d') as fecha_formateada
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN estados_noticia en ON n.estado_id = en.id
    ORDER BY n.fecha_creacion DESC
    LIMIT 7
");

// Consulta para los usuarios recientes
$usuarios_recientes = $conn->query("
    SELECT u.id, u.nombre, u.apellido, u.correo, r.nombre as rol_nombre, 
           u.activo, DATE_FORMAT(u.fecha_registro, '%Y-%m-%d %H:%i') as fecha_registro_formateada
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    ORDER BY u.fecha_registro DESC
    LIMIT 7
");

if (!$usuarios_recientes) {
    die("Error en la consulta de usuarios: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin | Noticias Globales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="../Css/admin.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content container">
            <div class="panel_control">
                <div class="title mb-3">
                    <a href="Crear_Noticias.php" class="crear"><i class="fa fa-plus me-2"></i> Crear Noticia</a>
                </div>

                <div class="dashboard-cards">
                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>NOTICIAS TOTALES</h5>
                            <i class="fa fa-newspaper"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $total_noticias ?></span>
                            <div class="stats-trend <?= ($variacion_total >= 0) ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= ($variacion_total >= 0) ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
                                <?= number_format(abs($variacion_total), 1) ?>%
                            </div>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PENDIENTES</h5>
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $pendientes ?></span>
                            <div class="stats-trend <?= ($variacion_pendientes >= 0) ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= ($variacion_pendientes >= 0) ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
                                <?= number_format(abs($variacion_pendientes), 1) ?>%
                            </div>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PROGRAMADAS</h5>
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $programadas ?></span>
                            <div class="stats-trend <?= ($variacion_programadas >= 0) ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= ($variacion_programadas >= 0) ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
                                <?= number_format(abs($variacion_programadas), 1) ?>%
                            </div>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PUBLICADAS HOY</h5>
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $publicadas_hoy ?></span>
                            <div class="stats-trend <?= ($variacion_publicadas >= 0) ? 'text-success' : 'text-danger' ?>">
                                <i class="fas <?= ($variacion_publicadas >= 0) ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
                                <?= number_format(abs($variacion_publicadas), 1) ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row recent py-4">
                <div class="table-responsive col-sm-12 col-md-6  mb-3">
                    <div class="recent-top">
                        <a href="Todas_Noticias.php" class="ver-todas mb-3">Ver Todo</a>
                    </div>
                    <table class="tabla table table-hover mb-0" style="color: black;">
                        <thead>
                            <tr>
                                <th>TÍTULO</th>
                                <th>TIPO</th>
                                <th>CATEGORIA</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recientes->num_rows > 0): ?>
                                <?php while ($noticia = $recientes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(substr($noticia['titulo'], 0, 50)) ?><?= strlen($noticia['titulo']) > 50 ? '...' : '' ?></td>
                                        <td><?= ucfirst($noticia['tipo_noticia']) ?></td>
                                        <td><?= htmlspecialchars($noticia['categoria_nombre']) ?></td>
                                        <td>
                                            <?php if ($noticia['estado_id'] == 1): ?>
                                                <span class="pendiente">Pendiente</span>
                                            <?php elseif ($noticia['estado_id'] == 2): ?>
                                                <span class="programada">Programada</span>
                                            <?php else: ?>
                                                <span class="publicada">Publicada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../funciones/editar_noticia.php?id=<?= $noticia['id'] ?>" class="editar"><i class="fa fa-edit"></i></a>
                                            <button class="eliminar" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $noticia['id'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay noticias recientes</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="recent-top">
                        <a href="Todas_Usuarios.php" class="ver-todas mb-3">Ver Todo</a>
                    </div>

                    <table  class="table table-hover">
                        <thead>
                            <tr>
                                <th>NOMBRE</th>
                                <th>CORREO</th>
                                <th>REGISTRO</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($usuarios_recientes->num_rows > 0): ?>
                                <?php while ($usuario = $usuarios_recientes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                        <td><?= htmlspecialchars(substr($usuario['correo'], 0, 20)) ?><?= strlen($usuario['correo']) > 20 ? '...' : '' ?></td>
                                        <td><?= $usuario['fecha_registro_formateada'] ?></td>
                                        <td>
                                            <?php if ($usuario['activo'] == 1): ?>
                                                <span class="publicada">Activo</span>
                                            <?php else: ?>
                                                <span class="pendiente">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay usuarios registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #003366;">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar esta noticia permanentemente?</p>
                    <p class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-primary" style="background-color: #003366; border-color: #003366;">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        if (confirmDeleteModal) {
            confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const noticiaId = button.getAttribute('data-id');
                const deleteBtn = document.getElementById('confirmDeleteBtn');
                deleteBtn.href = `../funciones/eliminar_noticia.php?id=${noticiaId}`;
            });
        }
    </script>
</body>

</html>
<?php include 'admin_navbar.php'; ?>
<?php include 'admin_auth.php'; ?>
<?php
require_once '../bd/conexion.php';

// Consultas para las tarjetas de resumen
$total_noticias = $conn->query("SELECT COUNT(*) as total FROM noticias")->fetch_assoc()['total'];
$pendientes = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 1")->fetch_assoc()['total'];
$programadas = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 2")->fetch_assoc()['total'];
$publicadas_hoy = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE estado_id = 3 AND DATE(fecha_publicacion) = CURDATE()")->fetch_assoc()['total'];
$internacionales = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE tipo_noticia = 'internacional'")->fetch_assoc()['total'];

// Consulta para noticias recientes (últimas 5)
$recientes = $conn->query("
    SELECT n.*, c.nombre as categoria_nombre, en.nombre as estado_nombre,
           DATE_FORMAT(n.fecha_creacion, '%Y-%m-%d') as fecha_formateada
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN estados_noticia en ON n.estado_id = en.id
    ORDER BY n.fecha_creacion DESC
    LIMIT 5
");
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
        <div id="main-content">
            <div class="panel_control ">
                <div class="title mb-3">
                    <h3>Panel de Control</h3>
                    <a href="Crear_Noticias.php" class="crear"><i class="fa fa-plus"></i> Crear Noticia</a>
                </div>

                <div class="dashboard-cards">
                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>NOTICIAS TOTALES</h5>
                            <i class="fa fa-newspaper"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $total_noticias ?></span>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PENDIENTES</h5>
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $pendientes ?></span>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PROGRAMADAS</h5>
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $programadas ?></span>
                        </div>
                    </div>

                    <div class="cads-news">
                        <div class="cards-header">
                            <h5>PUBLICADAS HOY</h5>
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="cards-body">
                            <span><?= $publicadas_hoy ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recent py-2">
                <div class="recent-top">
                    <h3>Noticias Recientes</h3>
                    <a href="Todas_Noticias.php" class="ver-todas">Ver Todo</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>TÍTULO</th>
                            <th>TIPO</th>
                            <th>CATEGORIA</th>
                            <th>FECHA</th>
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
                                    <td><?= $noticia['fecha_formateada'] ?></td>
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
                                <td colspan="6" class="text-center">No hay noticias recientes</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación para eliminar -->
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
        // Manejar el modal de confirmación de eliminación
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
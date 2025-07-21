<?php
include 'admin_navbar.php';
include 'admin_auth.php';

require_once '../bd/conexion.php';

// Manejo de mensajes
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Limpiar mensajes después de mostrarlos
unset($_SESSION['success']);
unset($_SESSION['error']);

// Obtener noticias pendientes (estado_id = 1)
$sql_pendientes = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre
                   FROM noticias n
                   JOIN usuarios u ON n.autor_id = u.id
                   JOIN categorias c ON n.categoria_id = c.id
                   WHERE n.estado_id = 1
                   ORDER BY n.fecha_creacion DESC";

$noticias_pendientes = $conn->query($sql_pendientes);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión de Noticias | Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Css/admin.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="todo">
                <h2 class="mb-4"></i>Noticias de usuarios</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Autor</th>
                                <th>Fecha de Envío</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($noticias_pendientes->num_rows > 0): ?>
                                <?php while ($noticia = $noticias_pendientes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                        <td><?= htmlspecialchars($noticia['categoria_nombre']) ?></td>
                                        <td><?= htmlspecialchars($noticia['autor']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])) ?></td>
                                        <td>
                                            <a href="../funciones/revisar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Revisar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i><br>
                                        No hay noticias pendientes de revisión
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
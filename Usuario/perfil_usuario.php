<?php include ("../Publico/navar.php"); ?>
<?php
require_once '../bd/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario
$query = "SELECT u.*, r.nombre as rol FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado");
}

// Obtener historial de noticias del usuario
$query_noticias = "SELECT n.id, n.titulo, n.fecha_creacion, en.nombre as estado 
                   FROM noticias n 
                   JOIN estados_noticia en ON n.estado_id = en.id 
                   WHERE n.autor_id = ? 
                   ORDER BY n.fecha_creacion DESC";
$stmt_noticias = $conn->prepare($query_noticias);
$stmt_noticias->bind_param("i", $usuario_id);
$stmt_noticias->execute();
$result_noticias = $stmt_noticias->get_result();
$noticias = $result_noticias->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($usuario['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/perfil.css">
</head>
<body class="perfil-pagina">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile-header text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($usuario['nombre'] . '+' . $usuario['apellido']) ?>&size=150" 
                         alt="Foto de perfil" class="profile-pic mb-3">
                    <h3><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h3>
                    <span class="badge bg-<?= $usuario['rol_id'] == 0 ? 'danger' : 'primary' ?>">
                        <?= htmlspecialchars($usuario['rol']) ?>
                    </span>
                    <p class="text-muted mt-2">Miembro desde: <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></p>
                    
                    <a href="editar_perfil.php" class="btn btn-primary mt-3">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                    
                    <?php if ($usuario['rol_id'] == 1): ?>
                        <a href="enviar_noticia.php" class="btn btn-enviar-noticia mt-3">
                            <i class="fas fa-paper-plane"></i> Enviar Noticia
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-user me-2"></i>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
                                <p><strong><i class="fas fa-user me-2"></i>Apellido:</strong> <?= htmlspecialchars($usuario['apellido']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-envelope me-2"></i>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
                                <p><strong><i class="fas fa-calendar-alt me-2"></i>Registrado:</strong> <?= date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Historial de Noticias -->
                <div class="card shadow-sm historial-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Noticias</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($noticias) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Fecha de Envío</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($noticias as $noticia): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])) ?></td>
                                                <td>
                                                    <?php 
                                                        $badge_class = '';
                                                        switch($noticia['estado']) {
                                                            case 'Pendiente': $badge_class = 'bg-warning'; break;
                                                            case 'Publicado': $badge_class = 'bg-success'; break;
                                                            case 'Programado': $badge_class = 'bg-info'; break;
                                                            case 'Rechazado': $badge_class = 'bg-danger'; break;
                                                            default: $badge_class = 'bg-secondary';
                                                        }
                                                    ?>
                                                    <span class="badge <?= $badge_class ?> badge-estado"><?= htmlspecialchars($noticia['estado']) ?></span>
                                                </td>
                                                <td>
                                                    <a href="../funciones/ver_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                No has enviado ninguna noticia todavía.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../Publico/footer.php'; ?>
</body>
</html>
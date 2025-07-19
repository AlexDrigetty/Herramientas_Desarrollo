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
    <style>
        .profile-header {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    
    
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
                
                <!-- Estadísticas del usuario -->
                <div class="row mt-4">
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card text-white bg-info">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-newspaper"></i> Noticias</h5>
                                <?php
                                $query = "SELECT COUNT(*) FROM noticias WHERE autor_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $usuario_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $total_noticias = $result->fetch_row()[0];
                                ?>
                                <h2 class="mb-0"><?= $total_noticias ?></h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card text-white bg-success">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-eye"></i> Vistas</h5>
                                <?php
                                $query = "SELECT SUM(vistas) FROM noticias WHERE autor_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $usuario_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $total_vistas = $result->fetch_row()[0] ?: 0;
                                ?>
                                <h2 class="mb-0"><?= number_format($total_vistas) ?></h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card text-white bg-warning">
                            <div class="card-body text-center">
                                <h5><i class="fas fa-calendar-check"></i> Programadas</h5>
                                <?php
                                $query = "SELECT COUNT(*) FROM noticias WHERE autor_id = ? AND estado_id = 2";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $usuario_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $programadas = $result->fetch_row()[0];
                                ?>
                                <h2 class="mb-0"><?= $programadas ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
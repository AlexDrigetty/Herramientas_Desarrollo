<?php include '../Publico/navar.php'; ?>
<?php
require_once '../bd/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario
$query = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    
    try {
        if (!empty($contrasena)) {
            // Actualizar con contraseña
            $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
            $query = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, contrasena = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $hashed_password, $usuario_id);
        } else {
            // Actualizar sin cambiar contraseña
            $query = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $nombre, $apellido, $correo, $usuario_id);
        }
        
        $stmt->execute();
        
        $_SESSION['mensaje'] = "Perfil actualizado correctamente";
        header('Location: perfil.php');
        exit;
    } catch (Exception $e) {
        $error = "Error al actualizar el perfil: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
</head>
<body>
    

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" 
                                           value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Nueva contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena">
                                <div class="form-text">Mínimo 8 caracteres</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="perfil.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
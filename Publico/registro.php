<?php
include '../bd/conexion.php';
include "navar.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $confirm_contrasena = $_POST['confirm_contrasena'];

    // Validaciones
    if (empty($nombre)) $errors['nombre'] = "El nombre es requerido";
    if (empty($apellido)) $errors['apellido'] = "El apellido es requerido";
    if (empty($correo)) {
        $errors['correo'] = "El correo es requerido";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors['correo'] = "El correo no es válido";
    }
    if (empty($contrasena)) $errors['contrasena'] = "La contraseña es requerida";
    if (strlen($contrasena) < 8) $errors['contrasena'] = "La contraseña debe tener al menos 8 caracteres";
    if ($contrasena !== $confirm_contrasena) $errors['confirm_contrasena'] = "Las contraseñas no coinciden";
    
    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['correo'] = "Este correo ya está registrado";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, contrasena) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $correo, $hashed_password);

        if ($stmt->execute()) {
            $success = true;
            // Redirigir después de 2 segundos
            header("Refresh: 2; URL=login.php?registro=exitoso");
        } else {
            $errors['general'] = "Error al registrar usuario: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/inicio.css">
    <link rel="stylesheet" href="../Css/login.css">
    <link rel="stylesheet" href="../Css/destacados.css">
    <link rel="stylesheet" href="../Css/registro.css">
    <style>
        /* Estilo para el mensaje de éxito en el registro */
        .alert-register-success {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            animation: fadeInOut 3s ease-in-out forwards;
            opacity: 0;
            max-width: 300px;
        }
        
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateX(20px); }
            10% { opacity: 1; transform: translateX(0); }
            90% { opacity: 1; transform: translateX(0); }
            100% { opacity: 0; transform: translateX(20px); }
        }
        
        /* Estilo para mensajes de error debajo de los inputs */
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: block;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .strength-bar-container {
            height: 5px;
            background-color: #e9ecef;
            margin-top: 5px;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <?php if ($success): ?>
        <div class="alert alert-success alert-register-success">
            <i class="fas fa-check-circle me-2"></i> ¡Registro exitoso!
        </div>
    <?php endif; ?>
    
    <main>
        <div class="container d-flex justify-content-center align-items-center h-100">
            <div class="register-container col-md-8 col-lg-6 mt-4 mb-4">
                <div class="register-header">
                    <h2><i class="fas fa-user-plus me-2"></i> Crear Cuenta</h2>
                </div>
                <div class="register-body">
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= $errors['general'] ?></div>
                    <?php endif; ?>
                    
                    <form id="registerForm" action="" method="POST" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">Nombre</label>
                                <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" 
                                       id="firstName" name="nombre" placeholder="Nombres" 
                                       value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                                <?php if (isset($errors['nombre'])): ?>
                                    <span class="error-message"><?= $errors['nombre'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Apellido</label>
                                <input type="text" class="form-control <?= isset($errors['apellido']) ? 'is-invalid' : '' ?>" 
                                       id="lastName" name="apellido" placeholder="Apellidos" 
                                       value="<?= htmlspecialchars($apellido ?? '') ?>" required>
                                <?php if (isset($errors['apellido'])): ?>
                                    <span class="error-message"><?= $errors['apellido'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>" 
                                       id="email" name="correo" placeholder="Correo" 
                                       value="<?= htmlspecialchars($correo ?? '') ?>" required>
                            </div>
                            <?php if (isset($errors['correo'])): ?>
                                <span class="error-message"><?= $errors['correo'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control <?= isset($errors['contrasena']) ? 'is-invalid' : '' ?>" 
                                       id="password" name="contrasena" placeholder="Contraseña" required>
                            </div>
                            <?php if (isset($errors['contrasena'])): ?>
                                <span class="error-message"><?= $errors['contrasena'] ?></span>
                            <?php endif; ?>
                            <div class="strength-bar-container">
                                <div class="strength-bar" id="strength-bar"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control <?= isset($errors['confirm_contrasena']) ? 'is-invalid' : '' ?>" 
                                       id="confirmPassword" name="confirm_contrasena" placeholder="Confirmar Contraseña" required>
                            </div>
                            <?php if (isset($errors['confirm_contrasena'])): ?>
                                <span class="error-message"><?= $errors['confirm_contrasena'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>" 
                                   type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">Acepto los <a href="#" class="link-orange">Términos y condiciones</a></label>
                            <?php if (isset($errors['terms'])): ?>
                                <span class="error-message">Debes aceptar los términos y condiciones</span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-register w-100 py-2 mb-3">REGISTRARSE</button>
                        <div class="text-center">
                            <p class="text-muted">¿Ya tienes cuenta? <a href="../Publico/login.php" class="iniciar">Inicia sesión</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario en el cliente
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            
            // Mostrar fortaleza de la contraseña
            document.getElementById('password').addEventListener('input', function(e) {
                const strengthBar = document.getElementById('strength-bar');
                const password = e.target.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 25;
                if (password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 25;
                if (password.match(/[^A-Za-z0-9]/)) strength += 25;
                
                strengthBar.style.width = strength + '%';
                strengthBar.style.backgroundColor = 
                    strength < 50 ? '#dc3545' : 
                    strength < 75 ? '#ffc107' : '#28a745';
            });
            
            // Validar confirmación de contraseña en tiempo real
            document.getElementById('confirmPassword').addEventListener('input', function() {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;
                const errorElement = this.nextElementSibling?.querySelector('.error-message') || 
                                    this.parentElement.nextElementSibling;
                
                if (password !== confirmPassword) {
                    this.classList.add('is-invalid');
                    if (errorElement) {
                        errorElement.textContent = "Las contraseñas no coinciden";
                        errorElement.style.display = 'block';
                    }
                } else {
                    this.classList.remove('is-invalid');
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                }
            });
            
            // Validación al enviar el formulario
            form.addEventListener('submit', function(event) {
                let isValid = true;
                
                // Validar coincidencia de contraseñas
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const confirmError = document.getElementById('confirmPassword').nextElementSibling?.querySelector('.error-message') || 
                                   document.getElementById('confirmPassword').parentElement.nextElementSibling;
                
                if (password !== confirmPassword) {
                    document.getElementById('confirmPassword').classList.add('is-invalid');
                    if (confirmError) {
                        confirmError.textContent = "Las contraseñas no coinciden";
                        confirmError.style.display = 'block';
                    }
                    isValid = false;
                }
                
                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
        
        // Menú responsive
        document.querySelector(".fas").addEventListener("click", () => {
            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
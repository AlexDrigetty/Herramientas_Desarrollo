<?php
include '../bd/conexion.php';
include "navar.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = false;

// Mostrar mensaje de registro exitoso
if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso') {
    $success = true;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    // Validaciones
    if (empty($correo)) {
        $errors['correo'] = "El correo es requerido";
    } elseif(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors['correo'] = "El correo no es válido";
    }
    if (empty($contrasena)) {
        $errors['contrasena'] = "La contraseña es requerida";
    }

    if (empty($errors)) {
        $sql = "SELECT u.id, u.nombre, u.contrasena, r.nombre as rol 
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.id
        WHERE u.correo = ? LIMIT 1";
        $stmt = $conn -> prepare($sql);
        $stmt -> bind_param("s", $correo);
        $stmt -> execute();
        $resultado = $stmt -> get_result();

        if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol']; 
            
            error_log("Rol del usuario: " . $_SESSION['rol']);
            
            if ($_SESSION['rol'] === 'ADMIN') {
                header("Location: ../Admin/dashboard_admin.php");
            } else {
                header("Location: ../Publico/inicio.php");
            }
            exit;
        }
    }
        $stmt -> close();
    }
}
?>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | Noticias Globales</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../Css/nav.css">
        <link rel="stylesheet" href="../Css/inicio.css">
        <link rel="stylesheet" href="../Css/login.css">
    </head>

    <body>
        <?php if ($success): ?>
        <div class="alert alert-success alert-register-success">
            <i class="fas fa-check-circle me-2"></i> ¡Registro exitoso! Ahora puedes iniciar sesión.
        </div>
        <script>
            setTimeout(function () {
                document.querySelector('.alert-register-success').style.display = 'none';
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 3000);
        </script>
        <?php endif; ?>

        <div class="container d-flex justify-content-center align-items-center h-100">
            <div class="login-container col-md-6 col-lg-4 mt-4 mb-4">
                <div class="login-header">
                    <h2><i class="fas fa-sign-in-alt me-2"></i> INICIAR SESIÓN</h2>
                </div>
                <div class="login-body">
                    <form method="POST" action="login.php" id="loginForm" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email"
                                    class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>"
                                    name="correo" id="email" placeholder="correo"
                                    value="<?= htmlspecialchars($correo ?? '') ?>" required>
                            </div>
                            <?php if (isset($errors['correo'])): ?>
                            <span class="error-message">
                                <?= $errors['correo'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group" style="position: relative;">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password"
                                    class="form-control <?= isset($errors['contrasena']) ? 'is-invalid' : '' ?>"
                                    name="contrasena" id="password" placeholder="Contraseña" required>
                                <span class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['contrasena'])): ?>
                            <span class="error-message">
                                <?= $errors['contrasena'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <a href="#" class="olvidaste">¿Olvidaste tu contraseña?</a>
                        </div>
                        <button type="submit" class="btn btn-login w-100 py-2 mb-3">INICIAR SESIÓN</button>

                        <div class="text-center pt-3">
                            <p>¿No tienes cuenta? <a href="../Publico/registro.php" class="registro">Regístrate</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Validación del formulario en el cliente
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('loginForm');

                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);

                // Mostrar/ocultar contraseña
                const togglePassword = document.getElementById('togglePassword');
                const password = document.getElementById('password');

                togglePassword.addEventListener('click', function () {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            });

            // Menú responsive
            document.querySelector(".fas").addEventListener("click", () => {
                document.querySelector(".nav-links").classList.toggle("active");
            });
        </script>
        <?php include 'footer.php'?>
    </body>

    </html>
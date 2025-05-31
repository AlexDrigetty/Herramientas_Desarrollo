<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];

            header("Location: ../Publico/inicio.php");
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
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
    <link rel="stylesheet" href="../Css/destacados.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center h-100 mt-4 mb-4">
        <div class="login-container col-md-6 col-lg-4">
            <div class="login-header">
                <h2 class="text-center">LOGIN</h2>
            </div>
            <div class="login-body">
                <form method="POST" action="login.php">
                    <div class="mb-4">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="correo" id="email" placeholder="correo" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="contrasena" id="password" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        <a href="#" class="text-decoration-none olvidaste">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="btn btn-login w-100 py-2">INICIAR SESIÓN</button>
                    
                    <div class="text-center pt-3">

                        <p>¿No tienes cuenta? <a href="../bd/registro.php" class="text-decoration-none registro">Regístrate</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelector(".fas").addEventListener("click", () => {

            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'?>
</body>
</html>
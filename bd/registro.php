<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, apellido, correo, contrasena) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $contrasena);


    if ($stmt->execute()) {
        echo "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
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

</head>
<body>
    <main>
        <div class="container d-flex justify-content-center align-items-center h-100">
            <div class="register-container col-md-8 col-lg-6 mt-4 mb-4">
                <div class="register-header">
                    <h2></i> Crear Cuenta</h2>
                </div>
                <div class="register-body">
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="firstName" name="nombre" placeholder="Nombres" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="lastName" name="apellido" placeholder="Apellidos" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="correo" placeholder="Correo" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="contrasena" placeholder="Contraseña" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirmar Contraseña" required>
                            </div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">Acepto los <a href="#" class="link-orange">Términos y condiciones</a></label>
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
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatar-preview').src = event.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        });

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
    </script>
    <script>
        document.querySelector(".fas").addEventListener("click", () => {

            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>

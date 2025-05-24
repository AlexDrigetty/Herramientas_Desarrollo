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
    $stmt->execute();

    echo "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
    $stmt->close();
}
?>

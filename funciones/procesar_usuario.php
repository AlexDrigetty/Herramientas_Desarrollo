<?php
require_once '../bd/conexion.php';
header('Content-Type: application/json');

// Verificar si el usuario tiene permisos de admin
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener rol del usuario desde la sesión o base de datos si es necesario
// Asumiendo que $_SESSION['usuario']['rol_id'] contiene el rol del usuario
if ($_SESSION['usuario']['rol_id'] != 0) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos de administrador']);
    exit;
}

// Manejar creación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $apellido = trim($conn->real_escape_string($_POST['apellido']));
    $correo = trim($conn->real_escape_string($_POST['correo']));
    $contrasena = $_POST['contrasena'];
    $rol_id = intval($_POST['rol']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        exit;
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
        exit;
    }
    
    if (strlen($contrasena) < 8) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
        exit;
    }
    
    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado']);
        exit;
    }
    
    // Hash de la contraseña
    $hashedPassword = password_hash($contrasena, PASSWORD_BCRYPT);
    
    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $hashedPassword, $rol_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear usuario: ' . $conn->error]);
    }
    exit;
}

// Manejar eliminación de usuario (vía AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar') {
    $userId = intval($_POST['id']);
    
    // No permitir eliminarse a sí mismo
    if (isset($_SESSION['usuario']['id']) && $userId == $_SESSION['usuario']['id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo']);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario: ' . $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida']);
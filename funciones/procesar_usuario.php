<?php
require_once '../bd/conexion.php';
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Determinar la acción basada en la solicitud
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'crear':
        crearUsuario($conn);
        break;
    case 'editar':
        editarUsuario($conn);
        break;
    case 'eliminar':
        eliminarUsuario($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        exit;
}

function crearUsuario($conn) {
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $apellido = trim($conn->real_escape_string($_POST['apellido']));
    $correo = trim($conn->real_escape_string($_POST['correo']));
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    $rol_id = intval($_POST['rol']);
    
    // Validaciones
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena) || empty($confirmar_contrasena)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        exit;
    }
    
    if ($contrasena !== $confirmar_contrasena) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
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

function editarUsuario($conn) {
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
        exit;
    }
    
    $id = intval($_POST['id']);
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $apellido = trim($conn->real_escape_string($_POST['apellido']));
    $correo = trim($conn->real_escape_string($_POST['correo']));
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
    $rol_id = intval($_POST['rol']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($correo)) {
        echo json_encode(['success' => false, 'message' => 'Los campos nombre, apellido y correo son requeridos']);
        exit;
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
        exit;
    }
    
    // Verificar si el correo ya existe (excluyendo al usuario actual)
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
    $stmt->bind_param("si", $correo, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado por otro usuario']);
        exit;
    }
    
    // Actualizar contraseña solo si se proporcionó una nueva
    $passwordUpdate = '';
    $params = [$nombre, $apellido, $correo, $rol_id, $id];
    $types = "sssii";
    
    if (!empty($contrasena)) {
        if ($contrasena !== $confirmar_contrasena) {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
            exit;
        }
        
        if (strlen($contrasena) < 8) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
            exit;
        }
        
        $hashedPassword = password_hash($contrasena, PASSWORD_BCRYPT);
        $passwordUpdate = ", contrasena = ?";
        $types = "ssssi";
        array_splice($params, 3, 0, $hashedPassword);
    }
    
    // Actualizar usuario
    $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, rol_id = ?$passwordUpdate WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario: ' . $conn->error]);
    }
    exit;
}

function eliminarUsuario($conn) {
    if (!isset($_POST['id']) && !isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
        exit;
    }
    
    $userId = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);
    
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
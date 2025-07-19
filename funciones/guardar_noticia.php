<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../Publico/login.php");
    exit;
}

require_once '../bd/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Método no permitido";
    header("Location: ../Admin/Crear_Noticias.php");
    exit;
}

// Procesar datos del formulario
$titulo = $conn->real_escape_string($_POST['titulo']);
$resumen = $conn->real_escape_string($_POST['resumen']);
$contenido = $conn->real_escape_string($_POST['contenido']);
$tipo_noticia = $conn->real_escape_string($_POST['tipo_noticia']);
$nombre_categoria = $conn->real_escape_string($_POST['categoria']);
$autor_id = $_SESSION['usuario_id'];
$accion = $_POST['accion']; // 'publicar' o 'programar'

// Validar campos requeridos
if (empty($titulo) || empty($resumen) || empty($contenido) || empty($tipo_noticia) || empty($nombre_categoria)) {
    $_SESSION['error'] = "Todos los campos son requeridos";
    header("Location: ../Admin/Crear_Noticias.php");
    exit();
}

// Obtener ID de categoría
$categoria_query = $conn->query("SELECT id FROM categorias WHERE nombre = '$nombre_categoria' LIMIT 1");
if (!$categoria_query || $categoria_query->num_rows === 0) {
    $_SESSION['error'] = "Categoría no válida";
    header("Location: ../Admin/Crear_Noticias.php");
    exit();
}
$categoria_id = $categoria_query->fetch_assoc()['id'];

// Procesar imagen (requerida siempre)
$imagen_nombre = 'default.jpg';
if ($_FILES['portada']['error'] === UPLOAD_ERR_OK) {
    $extension = strtolower(pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($extension, $allowed)) {
        $_SESSION['error'] = "Solo se permiten imágenes JPG, PNG o GIF";
        header("Location: ../Admin/Crear_Noticias.php");
        exit();
    }
    
    if ($_FILES['portada']['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = "La imagen no debe exceder los 5MB";
        header("Location: ../Admin/Crear_Noticias.php");
        exit();
    }
    
    $imagen_nombre = uniqid().'.'.$extension;
    $upload_path = '../imagenes/'.$imagen_nombre;
    
    if (!move_uploaded_file($_FILES['portada']['tmp_name'], $upload_path)) {
        $_SESSION['error'] = "Error al subir la imagen";
        header("Location: ../Admin/Crear_Noticias.php");
        exit();
    }
} else {
    $_SESSION['error'] = "La imagen de portada es requerida";
    header("Location: ../Admin/Crear_Noticias.php");
    exit();
}

// Determinar estado y fechas
if ($accion == 'programar') {
    $estado_id = 2; // Programado
    $fecha_prog = $conn->real_escape_string($_POST['fecha_programada']);
    $fecha_pub = null;
    
    // Validar fecha programada
    $now = new DateTime();
    $programada = new DateTime($fecha_prog);
    
    if ($programada <= $now) {
        $_SESSION['error'] = "La fecha programada debe ser en el futuro";
        header("Location: ../Admin/Crear_Noticias.php");
        exit();
    }
} else {
    $estado_id = 3; // Publicado
    $fecha_pub = date('Y-m-d H:i:s');
    $fecha_prog = null;
}

// Antes de la inserción, genera un slug único basado en el título
function generarSlug($titulo) {
    $slug = strtolower(trim($titulo));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}

$slug = generarSlug($titulo);

// Asegurar que el slug sea único
$slugOriginal = $slug;
$contador = 1;
while (true) {
    $check = $conn->query("SELECT COUNT(*) as total FROM noticias WHERE slug = '$slug'");
    $existe = $check->fetch_assoc()['total'];
    
    if ($existe == 0) break;
    
    $slug = $slugOriginal . '-' . $contador;
    $contador++;
}

$sql = "INSERT INTO noticias (
        titulo, slug, resumen, contenido, autor_id, categoria_id, 
        tipo_noticia, estado_id, imagen_portada, fecha_publicacion, fecha_programada
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Añadido slug

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssiisssss", // Añadida una 's' más para el slug
    $titulo, $slug, $resumen, $contenido, $autor_id, $categoria_id,
    $tipo_noticia, $estado_id, $imagen_nombre, $fecha_pub, $fecha_prog
);

if ($stmt->execute()) {
    if ($accion == 'programar') {
        $_SESSION['success'] = "Noticia programada correctamente para el ".date('d/m/Y H:i', strtotime($fecha_prog));
        header("Location: ../Admin/Programar_Noticias.php");
    } else {
        $_SESSION['success'] = "Noticia publicada correctamente";
        header("Location: ../Admin/Todas_Noticias.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Error al guardar la noticia: ".$conn->error;
    header("Location: ../Admin/Crear_Noticias.php");
    exit();
}
?>
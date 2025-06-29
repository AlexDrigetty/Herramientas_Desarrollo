<?php
require_once '../bd/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/Todas_Noticias.php');
    exit;
}

// Validar datos
$id = intval($_POST['id']);
$titulo = trim($_POST['titulo']);
$resumen = trim($_POST['resumen']);
$contenido = trim($_POST['contenido']);
$tipo_noticia = $_POST['tipo_noticia'] === 'nacional' ? 'nacional' : 'internacional';
$categoria_id = intval($_POST['categoria']);
$programar_noticia = isset($_POST['programar_noticia']) ? 1 : 0;
$fecha_programada = $programar_noticia ? $_POST['fecha_programada'] : null;

// Validaciones básicas
if (empty($titulo) || empty($resumen) || empty($contenido)) {
    $_SESSION['error'] = 'Todos los campos son requeridos';
    header("Location: ../admin/Todas_Noticias.php");
    exit;
}

// Determinar el estado_id basado en si está programada o no
if ($programar_noticia && $fecha_programada) {
    $estado_id = 2; // Programado
} else {
    $estado_id = 3; // Publicado
}

// Procesar imagen si se subió una nueva
$imagen_portada = null;
if (!empty($_FILES['portada']['name'])) {
    $nombre_archivo = uniqid() . '_' . basename($_FILES['portada']['name']);
    $ruta_destino = '../imagenes/' . $nombre_archivo;
    
    // Validar tipo de archivo
    $extension = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        $_SESSION['error'] = 'Solo se permiten imágenes JPG, PNG o GIF';
        header("Location: ../admin/Todas_Noticias.php");
        exit;
    }
    
    // Mover archivo
    if (move_uploaded_file($_FILES['portada']['tmp_name'], $ruta_destino)) {
        $imagen_portada = $nombre_archivo;
    }
}

// Actualizar noticia en la base de datos
$sql = "UPDATE noticias SET 
        titulo = ?, 
        resumen = ?, 
        contenido = ?, 
        tipo_noticia = ?, 
        categoria_id = ?,
        estado_id = ?";

// Si hay nueva imagen, agregarla a la consulta
if ($imagen_portada) {
    $sql .= ", imagen_portada = ?";
}

// Si está programada, actualizar fecha programada
if ($programar_noticia && $fecha_programada) {
    $sql .= ", fecha_programada = ?";
} else {
    $sql .= ", fecha_programada = NULL, fecha_publicacion = NOW()";
}

$sql .= " WHERE id = ?";

$stmt = $conn->prepare($sql);

// Bind parameters según si hay imagen o no
if ($imagen_portada && $programar_noticia && $fecha_programada) {
    $stmt->bind_param("ssssisssi", 
        $titulo, $resumen, $contenido, $tipo_noticia, $categoria_id,
        $estado_id, $imagen_portada, $fecha_programada, $id);
} elseif ($imagen_portada) {
    $stmt->bind_param("ssssissi", 
        $titulo, $resumen, $contenido, $tipo_noticia, $categoria_id,
        $estado_id, $imagen_portada, $id);
} elseif ($programar_noticia && $fecha_programada) {
    $stmt->bind_param("ssssissi", 
        $titulo, $resumen, $contenido, $tipo_noticia, $categoria_id,
        $estado_id, $fecha_programada, $id);
} else {
    $stmt->bind_param("ssssiii", 
        $titulo, $resumen, $contenido, $tipo_noticia, $categoria_id,
        $estado_id, $id);
}

if ($stmt->execute()) {
    $_SESSION['success'] = 'Noticia actualizada correctamente';
} else {
    $_SESSION['error'] = 'Error al actualizar la noticia: ' . $stmt->error;
}

header("Location: ../admin/Todas_Noticias.php");
?>
<?php
include '../bd/conexion.php';
session_start();

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $titulo = $_POST['titulo'] ?? '';
    $resumen = $_POST['resumen'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $tipo_noticia = $_POST['tipo_noticia'] ?? 'nacional';
    $categoria = $_POST['categoria'] ?? 'politica';
    
    // Procesar imagen (solo para previsualización)
    $imagen_preview = '';
    if (isset($_FILES['portada'])) {
        $imagen_tmp = $_FILES['portada']['tmp_name'];
        if (is_uploaded_file($imagen_tmp)) {
            $imagen_data = file_get_contents($imagen_tmp);
            $imagen_preview = 'data:'.mime_content_type($imagen_tmp).';base64,'.base64_encode($imagen_data);
        }
    }
    
    // Obtener nombre de categoría
    $stmt = $conn->prepare("SELECT nombre FROM categorias WHERE id = ?");
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $result = $stmt->get_result();
    $categoria_nombre = $result->fetch_assoc()['nombre'] ?? 'General';
    
    // Mostrar previsualización
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Previsualización: <?= htmlspecialchars($titulo) ?></title>
        <link rel="stylesheet" href="../Css/noticia.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <div class="preview-container">
            <div class="preview-header">
                <h1>Vista Previa de la Noticia</h1>
                <p>Esta es una vista previa de cómo se verá tu noticia cuando sea publicada.</p>
            </div>
            
            <article class="noticia-detalle">
                <h1><?= htmlspecialchars($titulo) ?></h1>
                
                <div class="noticia-meta">
                    <span class="categoria" style="background-color: #<?= htmlspecialchars($categoria_color) ?>">
                        <?= htmlspecialchars(ucfirst($categoria_nombre)) ?>
                    </span>
                    <span class="fecha"><?= date('d/m/Y H:i') ?></span>
                    <span class="tipo"><?= htmlspecialchars(ucfirst($tipo_noticia)) ?></span>
                </div>
                
                <?php if ($imagen_preview): ?>
                <div class="noticia-imagen">
                    <img src="<?= $imagen_preview ?>" alt="<?= htmlspecialchars($titulo) ?>">
                </div>
                <?php endif; ?>
                
                <div class="noticia-resumen">
                    <p><?= nl2br(htmlspecialchars($resumen)) ?></p>
                </div>
                
                <div class="noticia-contenido">
                    <?= $contenido ?>
                </div>
            </article>
            
            <div class="preview-footer">
                <p>Esta es solo una vista previa. La noticia no ha sido publicada aún.</p>
                <button onclick="window.close()" class="btn btn-primary">Cerrar Vista Previa</button>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Si no es POST, redirigir
header("Location: ../admin/Crear_Noticias.php");
exit();
?>
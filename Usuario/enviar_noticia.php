<?php 
include ("../Publico/navar.php");
require_once '../bd/conexion.php';

// Verificar autenticación y rol de usuario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener categorías
$categorias = $conn->query("SELECT * FROM categorias")->fetch_all(MYSQLI_ASSOC);

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $resumen = $conn->real_escape_string($_POST['resumen']);
    $contenido = $conn->real_escape_string($_POST['contenido']);
    $categoria_id = (int)$_POST['categoria'];
    $tipo_noticia = $conn->real_escape_string($_POST['tipo_noticia']);
    // La fecha de publicación será NULL inicialmente (se establecerá cuando el admin apruebe)
    $fecha_publicacion = null;
    
    // Validar campos requeridos
    if (empty($titulo) || empty($resumen) || empty($contenido) || empty($categoria_id) || empty($tipo_noticia)) {
        $error = "Todos los campos son requeridos";
    } else {
        // Procesar imagen
        $imagen_nombre = 'default.jpg';
        if ($_FILES['portada']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($extension, $allowed)) {
                $error = "Solo se permiten imágenes JPG, PNG o GIF";
            } elseif ($_FILES['portada']['size'] > 5 * 1024 * 1024) {
                $error = "La imagen no debe exceder los 5MB";
            } else {
                $imagen_nombre = uniqid().'.'.$extension;
                $upload_path = '../imagenes/'.$imagen_nombre;
                
                if (!move_uploaded_file($_FILES['portada']['tmp_name'], $upload_path)) {
                    $error = "Error al subir la imagen";
                }
            }
        }
        
        if (!isset($error)) {
            // Generar slug
            $slug = strtolower(trim($titulo));
            $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            
            // Verificar si el slug ya existe
            $slug_original = $slug;
            $contador = 1;
            do {
                $check = $conn->prepare("SELECT COUNT(*) FROM noticias WHERE slug = ?");
                $check->bind_param("s", $slug);
                $check->execute();
                $check->bind_result($existe);
                $check->fetch();
                $check->close();
                
                if ($existe > 0) {
                    $slug = $slug_original . '-' . $contador;
                    $contador++;
                }
            } while ($existe > 0);
            
            // Insertar noticia en estado "Pendiente"
            $sql = "INSERT INTO noticias (
                    titulo, slug, resumen, contenido, 
                    autor_id, categoria_id, tipo_noticia, 
                    estado_id, imagen_portada, fecha_publicacion
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en preparación: " . $conn->error);
            }
            
            // bind_param corregido: 9 parámetros (8 strings + 1 integer + NULL para fecha)
            $stmt->bind_param(
                "ssssiisss", // 9 especificadores para 9 parámetros
                $titulo, 
                $slug, 
                $resumen, 
                $contenido, 
                $usuario_id, 
                $categoria_id, 
                $tipo_noticia, 
                $imagen_nombre,
                $fecha_publicacion
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Noticia enviada para revisión. Será publicada una vez aprobada por el administrador.";
                header("Location: perfil_usuario.php");
                exit();
            } else {
                $error = "Error al enviar la noticia: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Noticia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/editor.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .image-preview {
            width: 100%;
            height: 200px;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .image-preview span {
            color: #6c757d;
            text-align: center;
            padding: 20px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="form-container">
            <h2 class="text-center mb-4"><i class="fas fa-paper-plane me-2"></i>Enviar Noticia</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="titulo" class="form-label required-field">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required
                           placeholder="Ingrese el título de la noticia" maxlength="255">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tipo_noticia" class="form-label required-field">Tipo de Noticia</label>
                        <select class="form-select" id="tipo_noticia" name="tipo_noticia" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="nacional">Nacional</option>
                            <option value="internacional">Internacional</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="categoria" class="form-label required-field">Categoría</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="resumen" class="form-label required-field">Resumen</label>
                    <textarea class="form-control" id="resumen" name="resumen" rows="3" required
                              placeholder="Escriba un breve resumen de la noticia (máximo 255 caracteres)" maxlength="255"></textarea>
                    <small class="text-muted">Máximo 255 caracteres</small>
                </div>
                
                <div class="mb-4">
                    <label for="contenido" class="form-label required-field">Contenido</label>
                    <textarea class="form-control" id="contenido" name="contenido" rows="10" required
                              placeholder="Escriba el contenido completo de la noticia"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Imagen de Portada</label>
                    <div class="image-preview" id="imagePreview" onclick="document.getElementById('portada').click()">
                        <span><i class="fas fa-image me-2"></i>Haga clic para seleccionar una imagen</span>
                        <img id="previewImage" style="display: none;">
                    </div>
                    <input type="file" class="form-control" id="portada" name="portada" accept="image/*" style="display: none;">
                    <small class="text-muted">Formatos aceptados: JPG, PNG, GIF (Máx. 5MB)</small>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="perfil_usuario.php" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Enviar para Revisión
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vista previa de la imagen
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const fileInput = document.getElementById('portada');
        
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    imagePreview.querySelector('span').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Validación de caracteres en el resumen
        document.getElementById('resumen').addEventListener('input', function() {
            if (this.value.length > 255) {
                this.value = this.value.substring(0, 255);
            }
        });
        
        // Mostrar contador de caracteres
        document.getElementById('resumen').addEventListener('focus', function() {
            const counter = document.createElement('div');
            counter.id = 'charCounter';
            counter.className = 'text-muted small text-end';
            counter.textContent = `${this.value.length}/255 caracteres`;
            this.parentNode.appendChild(counter);
            
            this.addEventListener('input', function() {
                counter.textContent = `${this.value.length}/255 caracteres`;
            });
        });
        
        document.getElementById('resumen').addEventListener('blur', function() {
            const counter = document.getElementById('charCounter');
            if (counter) counter.remove();
        });
    </script>
    <?php include '../Publico/footer.php'; ?>
</body>
</html>
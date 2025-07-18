<?php
require_once '../bd/conexion.php';

$noticia_id = isset($_GET['noticia_id']) ? (int)$_GET['noticia_id'] : 0;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10;
$offset = ($pagina - 1) * $limite;

// Obtener comentarios principales (no respuestas)
$query = "SELECT c.*, u.nombre, u.apellido 
          FROM comentarios c 
          JOIN usuarios u ON c.usuario_id = u.id 
          WHERE c.noticia_id = ? AND c.comentario_padre_id IS NULL AND c.estado = 'activo'
          ORDER BY c.fecha_creacion DESC 
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $noticia_id, $limite, $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($comentario = $result->fetch_assoc()):
    $query_respuestas = "SELECT COUNT(*) FROM comentarios 
                         WHERE comentario_padre_id = ? AND estado = 'activo'";
    $stmt_resp = $conn->prepare($query_respuestas);
    $stmt_resp->bind_param("i", $comentario['id']);
    $stmt_resp->execute();
    $num_respuestas = $stmt_resp->get_result()->fetch_row()[0];
?>
    <div class="card mb-3 comentario" data-id="<?= $comentario['id'] ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h5 class="card-title">
                    <?= htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']) ?>
                </h5>
                <small class="text-muted">
                    <?= date('d/m/Y H:i', strtotime($comentario['fecha_creacion'])) ?>
                </small>
            </div>
            <p class="card-text"><?= nl2br(htmlspecialchars($comentario['contenido'])) ?></p>
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="acciones-comentario">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <button class="btn btn-sm btn-outline-primary responder-btn">
                            <i class="fas fa-reply"></i> Responder
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($num_respuestas > 0): ?>
                        <button class="btn btn-sm btn-outline-secondary ver-respuestas-btn"
                                data-comentario-id="<?= $comentario['id'] ?>">
                            <i class="fas fa-comments"></i> Ver respuestas (<?= $num_respuestas ?>)
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="votos-comentario">
                    <span class="me-2">
                        <i class="fas fa-thumbs-up like-btn" data-comentario-id="<?= $comentario['id'] ?>"></i>
                        <span class="count" id="like-count-<?= $comentario['id'] ?>">0</span>
                    </span>
                    <span>
                        <i class="fas fa-thumbs-down dislike-btn" data-comentario-id="<?= $comentario['id'] ?>"></i>
                        <span class="count" id="dislike-count-<?= $comentario['id'] ?>">0</span>
                    </span>
                </div>
            </div>
            
            <!-- Formulario de respuesta (oculto inicialmente) -->
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="mt-3 respuesta-form" style="display: none;">
                    <form class="form-respuesta">
                        <input type="hidden" name="comentario_padre_id" value="<?= $comentario['id'] ?>">
                        <input type="hidden" name="noticia_id" value="<?= $noticia_id ?>">
                        <div class="form-group">
                            <textarea class="form-control" name="contenido" rows="2" 
                                      placeholder="Escribe tu respuesta..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Responder</button>
                        <button type="button" class="btn btn-secondary btn-sm mt-2 cancelar-respuesta">Cancelar</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Contenedor para respuestas -->
            <div class="respuestas-container mt-3" id="respuestas-<?= $comentario['id'] ?>" 
                 style="display: none;"></div>
        </div>
    </div>
<?php endwhile; ?>

<!-- Botón para cargar más comentarios -->
<?php if ($result->num_rows === $limite): ?>
    <div class="text-center mt-3">
        <button id="cargar-mas-btn" class="btn btn-outline-primary" 
                data-pagina="<?= $pagina + 1 ?>" data-noticia-id="<?= $noticia_id ?>">
            Cargar más comentarios
        </button>
    </div>
<?php endif; ?>
<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
</script>
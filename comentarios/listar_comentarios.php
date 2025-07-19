<?php
require_once '../bd/conexion.php';

// Obtener el ID de la noticia de la variable $noticia que ya existe en el ámbito
if (!isset($noticia) || !isset($noticia['id'])) {
    die("Noticia no especificada.");
}

$noticia_id = (int)$noticia['id'];

function mostrarComentarios($pdo, $noticia_id, $respuesta_id = null, $nivel = 0) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.nombre, u.apellido 
        FROM comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.noticia_id = :noticia_id AND c.respuesta_id " . 
        ($respuesta_id === null ? "IS NULL" : "= :respuesta_id") . "
        ORDER BY c.fecha_creacion DESC
    ");
    
    $params = ['noticia_id' => $noticia_id];
    if ($respuesta_id !== null) {
        $params['respuesta_id'] = $respuesta_id;
    }
    
    $stmt->execute($params);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($comentarios) {
        foreach ($comentarios as $comentario) {
            $margin = $nivel * 30;
            ?>
            <div class="comentario" style="margin-left: <?= $margin ?>px;" id="comentario-<?= $comentario['id'] ?>">
                <div class="comentario-header">
                    <span class="usuario"><?= htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']) ?></span>
                    <span class="fecha"><?= date('d/m/Y H:i', strtotime($comentario['fecha_creacion'])) ?></span>
                </div>
                <div class="comentario-contenido">
                    <?= nl2br(htmlspecialchars($comentario['contenido'])) ?>
                </div>
                <div class="comentario-acciones">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <button class="btn-respuesta" data-comentario="<?= $comentario['id'] ?>">
                            <i class="fas fa-reply"></i> Responder
                        </button>
                        
                        <?php if ($_SESSION['usuario_id'] == $comentario['usuario_id'] || ($_SESSION['rol_id'] ?? 0) == 0): ?>
                            <form method="POST" action="../comentarios/eliminar_comentario.php" class="d-inline">
                                <input type="hidden" name="comentario_id" value="<?= $comentario['id'] ?>">
                                <input type="hidden" name="noticia_id" value="<?= $noticia_id ?>">
                                <button type="submit" class="btn-eliminar">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Formulario de respuesta -->
                <div class="form-respuesta" id="form-respuesta-<?= $comentario['id'] ?>" style="display: none;">
                    <form method="POST" action="../comentarios/agregar_comentarios.php">
                        <input type="hidden" name="noticia_id" value="<?= $noticia_id ?>">
                        <input type="hidden" name="respuesta_id" value="<?= $comentario['id'] ?>">
                        <textarea name="contenido" placeholder="Escribe tu respuesta..." required></textarea>
                        <button type="submit" class="btn-enviar">Enviar respuesta</button>
                        <button type="button" class="btn-cancelar" data-comentario="<?= $comentario['id'] ?>">Cancelar</button>
                    </form>
                </div>
                
                <!-- Respuestas anidadas -->
                <?php mostrarComentarios($pdo, $noticia_id, $comentario['id'], $nivel + 1); ?>
            </div>
            <?php
        }
    }
}

// Mostrar los comentarios
mostrarComentarios($pdo, $noticia_id);
?>
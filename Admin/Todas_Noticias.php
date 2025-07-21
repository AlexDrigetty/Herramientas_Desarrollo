<?php include 'admin_navbar.php'; ?>
<?php include 'admin_auth.php'; ?>
<?php
require_once '../bd/conexion.php';

// Función para remover parámetros de la URL
function remove_query_param($param)
{
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    unset($query_params[$param]);
    $new_query = http_build_query($query_params);
    return $url['path'] . ($new_query ? '?' . $new_query : '');
}

// Función para generar enlaces de paginación con filtros
function get_pagination_link($pagina)
{
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    $query_params['pagina'] = $pagina;
    return $url['path'] . '?' . http_build_query($query_params);
}

// Obtener categorías para el filtro
$sql_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre";
$resultado_categorias = $conn->query($sql_categorias);
$categorias = $resultado_categorias->fetch_all(MYSQLI_ASSOC);

// Obtener estados para el filtro
$sql_estados = "SELECT id, nombre FROM estados_noticia WHERE id IN (1, 2, 3) ORDER BY id";
$resultado_estados = $conn->query($sql_estados);
$estados = $resultado_estados->fetch_all(MYSQLI_ASSOC);

// Obtener parámetros de filtrado
$categoria_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
$estado_filtro = isset($_GET['estado']) ? (int)$_GET['estado'] : null;

// Configuración de paginación
$noticias_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $noticias_por_pagina;

// Construir consulta base con filtros
$sql_base = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre,
        DATE_FORMAT(n.fecha_publicacion, '%d/%m/%Y %H:%i') as fecha_publicada,
        DATE_FORMAT(n.fecha_programada, '%d/%m/%Y %H:%i') as fecha_programada,
        en.nombre as estado_nombre
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        JOIN estados_noticia en ON n.estado_id = en.id
        WHERE n.estado_id IN (1, 2, 3)";

// Aplicar filtros si existen
$where_conditions = [];
$params = [];
$types = '';

if ($categoria_filtro) {
    $where_conditions[] = "n.categoria_id = ?";
    $params[] = $categoria_filtro;
    $types .= 'i';
}

if ($estado_filtro) {
    $where_conditions[] = "n.estado_id = ?";
    $params[] = $estado_filtro;
    $types .= 'i';
}

if (!empty($where_conditions)) {
    $sql_base .= " AND " . implode(" AND ", $where_conditions);
}

// Consulta para contar total de noticias (para paginación)
$sql_total = "SELECT COUNT(*) as total FROM noticias n WHERE n.estado_id IN (1, 2, 3)";
if (!empty($where_conditions)) {
    $sql_total .= " AND " . implode(" AND ", $where_conditions);
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$resultado_total = $stmt_total->get_result();
$total_noticias = $resultado_total->fetch_assoc()['total'];
$total_paginas = ceil($total_noticias / $noticias_por_pagina);

// Consulta para obtener noticias con ordenación
$sql = $sql_base . " ORDER BY 
        CASE 
            WHEN n.estado_id = 1 THEN 0 -- Pendientes primero
            WHEN n.estado_id = 2 THEN 1 -- Programadas después
            ELSE 2 -- Publicadas al final
        END,
        n.fecha_programada ASC,
        n.fecha_publicacion DESC
        LIMIT ?, ?";

// Preparar y ejecutar consulta con parámetros
$stmt = $conn->prepare($sql);
$types .= 'ii';
$params[] = $offset;
$params[] = $noticias_por_pagina;

$stmt->bind_param($types, ...$params);
$stmt->execute();
$noticias = $stmt->get_result();

if (!$noticias) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Noticias | Noticias Globales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../Css/admin.css">
    <style>
        .filtros-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filtros-container .form-select {
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .filtros-container .btn {
            margin-bottom: 10px;
        }

        .active-filter {
            background-color: #003366;
            color: white;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filtros-wrapper {
            display: flex;
            align-items: center;
        }

        .buttons-wrapper {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="todo">
                <div class="header-actions">
                    <div class="filtros-wrapper">
                        <form method="get" action="todas_noticias.php" id="filtros-form" class="row g-3">
                            <div class="col-auto">
                                <select name="categoria" class="form-select" id="filtro-categoria">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" <?= ($categoria_filtro == $categoria['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="estado" class="form-select" id="filtro-estado">
                                    <option value="">Todos los estados</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id'] ?>" <?= ($estado_filtro == $estado['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <a href="todas_noticias.php" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </form>
                    </div>

                    <div class="buttons-wrapper">
                        <a href="Crear_Noticias.php" class="crear" style="background: var(--tarjetas);
                padding: 12px 25px;
                border: none;
                border-radius: 5px;
                font-size: 12px;
                font-weight: 600;
                color: white;
                text-decoration: none;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;"><i class="fa fa-plus me-2"></i> Crear Noticia</a>
                        <a href="Programar_Noticias.php" class="crear" style="background: var(--tarjetas);
                padding: 12px 25px;
                border: none;
                border-radius: 5px;
                font-size: 12px;
                font-weight: 600;
                color: white;
                text-decoration: none;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;"><i class="fa fa-clock me-2"></i> Ver Programadas</a>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']);
                                                        unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']);
                                                    unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table-hover mb-0 ">
                        <thead class="table-dark">
                            <tr>
                                <th>TÍTULO</th>
                                <th>CATEGORÍA</th>
                                <th>ESTADO</th>
                                <th>FECHA</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($noticias->num_rows > 0): ?>
                                <?php while ($noticia = $noticias->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                        <td><?= htmlspecialchars($noticia['categoria_nombre']) ?></td>
                                        <td>
                                            <?php if ($noticia['estado_id'] == 3): ?>
                                                <span class="publicada">Publicada</span>
                                            <?php elseif ($noticia['estado_id'] == 2): ?>
                                                <span class="programada">Programada</span>
                                            <?php else: ?>
                                                <span class="pendiente">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($noticia['estado_id'] == 3) {
                                                echo $noticia['fecha_publicada'];
                                            } elseif ($noticia['estado_id'] == 2) {
                                                echo $noticia['fecha_programada'];
                                            } else {
                                                echo date('d/m/Y H:i', strtotime($noticia['fecha_creacion']));
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($noticia['estado_id'] == 2): ?>
                                                <a href="../funciones/publicar_ahora.php?id=<?= $noticia['id'] ?>" class="btn btn-sm publicar" title="Publicar ahora">
                                                    <i class="fa fa-paper-plane"></i>
                                                </a>
                                            <?php endif; ?>
                                            <button class="editar" title="Editar" data-id="<?= $noticia['id'] ?>" data-bs-toggle="modal" data-bs-target="#editarNoticiaModal">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="eliminar" title="Eliminar" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $noticia['id'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay noticias disponibles con los filtros seleccionados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación mejorada -->
                <div class="pagination-container">
                    <ul class="pagination">
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?= get_pagination_link($pagina_actual - 1) ?>">&laquo; Anterior</a></li>
                        <?php endif; ?>

                        <?php
                        // Mostrar hasta 5 páginas alrededor de la actual
                        $inicio = max(1, $pagina_actual - 2);
                        $fin = min($total_paginas, $pagina_actual + 2);

                        for ($i = $inicio; $i <= $fin; $i++): ?>
                            <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                <a class="page-link" href="<?= get_pagination_link($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item"><a class="page-link" href="<?= get_pagination_link($pagina_actual + 1) ?>">Siguiente &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal de edición de noticia -->
    <div class="modal fade" id="editarNoticiaModal" tabindex="-1" aria-labelledby="editarNoticiaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #003366;">
                    <h5 class="modal-title" id="editarNoticiaModalLabel">Editar Noticia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-editar-noticia" action="../funciones/actualizar_noticia.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="noticia-id">
                    <input type="hidden" name="accion" id="accion-hidden" value="publicar">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="box-content mb-3">
                                    <label for="edit-titulo" class="form-label">TITULO</label>
                                    <input type="text" name="titulo" id="edit-titulo" class="form-control" placeholder="Ingrese Titulo" required>
                                </div>
                                <div class="box-content mb-3">
                                    <label for="edit-resumen" class="form-label">RESUMEN</label>
                                    <textarea name="resumen" id="edit-resumen" class="form-control" placeholder="Ingrese breve resumen de la noticia" rows="3" required></textarea>
                                </div>

                                <div class="box-content">
                                    <label for="edit-contenido" class="form-label">CONTENIDO</label>
                                    <div id="edit-editor" style="height: 320px; background-color: white;"></div>
                                    <textarea id="edit-contenido" name="contenido" style="display:none;" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="box-content mb-4">
                                    <label class="form-label">FOTO DE PORTADA</label>
                                    <input type="file" id="edit-portada" name="portada" accept="image/*" style="display: none;">
                                    <div class="image-upload-container" id="edit-image-upload-container">
                                        <div class="image-preview" id="edit-image-preview">
                                            <img id="edit-preview" src="#" alt="Vista previa de la imagen" style="display: none;">
                                            <div class="upload-placeholder">
                                                <i class="fas fa-camera"></i>
                                                <span>Haz clic para seleccionar una imagen</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row content mb-3">
                                    <div class="col-md-6">
                                        <div class="tipo-noticia">
                                            <label for="edit-tipo_noticia" class="form-label">TIPO DE NOTICIA</label>
                                            <select name="tipo_noticia" id="edit-tipo_noticia" class="form-select" required>
                                                <option value="nacional">Nacional</option>
                                                <option value="internacional">Internacional</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="categoria">
                                            <label for="edit-categoria" class="form-label">CATEGORIA</label>
                                            <select name="categoria" id="edit-categoria" class="form-select" required>
                                                <option value="">Seleccione una categoria</option>
                                                <!-- Las opciones se llenarán con JavaScript -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campo para fecha de programación -->
                                <div class="box-content mb-3">
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" id="edit-programar-noticia" name="programar_noticia">
                                        <label class="form-check-label" for="edit-programar-noticia">PROGRAMAR FECHA</label>
                                    </div>

                                    <div id="edit-programacion-container" class="programacion-container" style="display: none;">
                                        <input type="text" name="fecha_programada" id="edit-fecha_programada" class="form-control" placeholder="Seleccione fecha y hora">
                                        <small class="text-muted">Seleccione cuándo desea que se publique automáticamente (mínimo 5 minutos en el futuro)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" style="background-color: #003366;" id="btn-guardar-cambios">
                            <i class="fa fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #003366;">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar esta noticia permanentemente?</p>
                    <p class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger" style="background-color: #003366; border-color: #003366;">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        // Inicializar el editor Quill para edición
        const editQuill = new Quill('#edit-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Escriba el contenido de la noticia aquí...'
        });

        editQuill.on('text-change', function() {
            document.getElementById('edit-contenido').value = editQuill.root.innerHTML;
        });

        // Inicializar datetimepicker para edición
        window.flatpickrInstances = window.flatpickrInstances || {};
        window.flatpickrInstances['edit-fecha_programada'] = flatpickr("#edit-fecha_programada", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            minTime: new Date().getHours() + ":" + (new Date().getMinutes() + 5),
            time_24hr: true,
            locale: "es",
            minuteIncrement: 5,
            defaultDate: new Date(Date.now() + 3600000) // 1 hora en el futuro
        });

        // Manejar el modal de edición
        const editarNoticiaModal = document.getElementById('editarNoticiaModal');
        if (editarNoticiaModal) {
            editarNoticiaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // Botón que disparó el modal
                const noticiaId = button.getAttribute('data-id');

                // Mostrar cargando
                document.getElementById('edit-titulo').value = 'Cargando...';
                document.getElementById('edit-resumen').value = 'Cargando...';
                editQuill.setContents([{
                    insert: 'Cargando...\n'
                }]);

                // Obtener datos de la noticia
                fetch(`../funciones/obtener_noticia.php?id=${noticiaId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        const noticia = data.noticia;
                        const categorias = data.categorias;

                        // Llenar los campos del formulario
                        document.getElementById('noticia-id').value = noticia.id;
                        document.getElementById('edit-titulo').value = noticia.titulo;
                        document.getElementById('edit-resumen').value = noticia.resumen;
                        editQuill.root.innerHTML = noticia.contenido;
                        document.getElementById('edit-contenido').value = noticia.contenido;
                        document.getElementById('edit-tipo_noticia').value = noticia.tipo_noticia;

                        // Llenar categorías
                        const selectCategoria = document.getElementById('edit-categoria');
                        selectCategoria.innerHTML = '<option value="">Seleccione una categoria</option>';
                        categorias.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.id;
                            option.textContent = categoria.nombre;
                            option.selected = (categoria.id == noticia.categoria_id);
                            selectCategoria.appendChild(option);
                        });

                        // Configurar programación si existe
                        if (noticia.fecha_programada_format) {
                            document.getElementById('edit-programar-noticia').checked = true;
                            document.getElementById('edit-programacion-container').style.display = 'block';
                            document.getElementById('edit-fecha_programada').value = noticia.fecha_programada_format;
                        }

                        // Configurar vista previa de imagen actual
                        const preview = document.getElementById('edit-preview');
                        const placeholder = document.querySelector('#edit-image-preview .upload-placeholder');
                        if (noticia.imagen_portada) {
                            preview.src = `../imagenes/${noticia.imagen_portada}`;
                            preview.style.display = 'block';
                            placeholder.style.display = 'none';
                        } else {
                            preview.style.display = 'none';
                            placeholder.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos de la noticia');
                    });
            });
        }

        // Control de la interfaz de programación en edición
        document.getElementById('edit-programar-noticia').addEventListener('change', function() {
            const programacionContainer = document.getElementById('edit-programacion-container');

            if (this.checked) {
                programacionContainer.style.display = 'block';
                document.getElementById('accion-hidden').value = 'programar';

                // Mostrar el datetimepicker
                if (window.flatpickrInstances && window.flatpickrInstances['edit-fecha_programada']) {
                    window.flatpickrInstances['edit-fecha_programada'].open();
                }
            } else {
                programacionContainer.style.display = 'none';
                document.getElementById('accion-hidden').value = 'publicar';
                document.getElementById('edit-fecha_programada').value = '';
            }
        });

        // Vista previa de la imagen en edición
        document.getElementById('edit-portada').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('edit-preview');
            const placeholder = document.querySelector('#edit-image-preview .upload-placeholder');

            if (file) {
                // Validar tipo de archivo
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Por favor seleccione una imagen válida (JPEG, PNG o GIF)');
                    this.value = '';
                    return;
                }

                // Validar tamaño (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen no debe exceder los 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                placeholder.style.display = 'block';
            }
        });

        // Manejar el clic en el contenedor de imagen en edición
        document.getElementById('edit-image-upload-container').addEventListener('click', function() {
            document.getElementById('edit-portada').click();
        });

        // Validación al enviar el formulario de edición
        document.getElementById('form-editar-noticia').addEventListener('submit', function(e) {
            // Asegurar que el contenido del editor se guarde
            document.getElementById('edit-contenido').value = editQuill.root.innerHTML;

            // Validar campos requeridos
            if (!this.titulo.value || !this.resumen.value || !editQuill.getText().trim()) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return;
            }

            // Si está en modo programación, validar fecha
            if (document.getElementById('edit-programar-noticia').checked) {
                const fechaProgramada = document.getElementById('edit-fecha_programada').value;
                if (!fechaProgramada) {
                    e.preventDefault();
                    alert('Por favor seleccione una fecha y hora para programar la publicación');
                    return;
                }

                const ahora = new Date();
                const fechaSeleccionada = new Date(fechaProgramada);
                const diferenciaMinutos = (fechaSeleccionada - ahora) / (1000 * 60);

                if (diferenciaMinutos < 5) {
                    e.preventDefault();
                    alert('La publicación debe programarse con al menos 5 minutos de anticipación');
                    return;
                }
            }
        });

        // Manejar el modal de confirmación de eliminación
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        if (confirmDeleteModal) {
            confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const noticiaId = button.getAttribute('data-id');

                // Actualizar el enlace de eliminación con el ID correcto
                const deleteBtn = document.getElementById('confirmDeleteBtn');
                deleteBtn.href = `../funciones/eliminar_noticia.php?id=${noticiaId}`;

                // Opcional: Manejar el clic para redirigir después de eliminar
                deleteBtn.addEventListener('click', function() {
                    window.location.href = this.href;
                });
            });
        }

        document.getElementById('filtro-categoria').addEventListener('change', function() {
            document.getElementById('filtros-form').submit();
        });

        document.getElementById('filtro-estado').addEventListener('change', function() {
            document.getElementById('filtros-form').submit();
        });

        // Función para actualizar la URL con los parámetros de filtro
        function updateFilterURL() {
            const categoria = document.getElementById('filtro-categoria').value;
            const estado = document.getElementById('filtro-estado').value;
            const params = new URLSearchParams();

            if (categoria) params.set('categoria', categoria);
            if (estado) params.set('estado', estado);

            // Actualizar la URL sin recargar la página
            window.history.replaceState({}, '', `${location.pathname}?${params.toString()}`);
        }
    </script>
</body>

</html>
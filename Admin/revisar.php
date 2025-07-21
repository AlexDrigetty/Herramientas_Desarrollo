<?php
include 'admin_navbar.php';
include 'admin_auth.php';

require_once '../bd/conexion.php';

// Manejo de mensajes
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Limpiar mensajes después de mostrarlos
unset($_SESSION['success']);
unset($_SESSION['error']);

// Obtener noticias programadas (estado_id = 2)
$sql = "SELECT n.*, u.nombre as autor, c.nombre as categoria_nombre,
        DATE_FORMAT(n.fecha_programada, '%d/%m/%Y %H:%i') as fecha_programada_formatted
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.estado_id = 2
        ORDER BY n.fecha_programada ASC";

$noticias = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias Programadas | Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../Css/admin.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="todo">
                <div class="boto mb-4">
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>TITULO</th>
                                <th>CATEGORIA</th>
                                <th>AUTOR</th>
                                <th>FECHA DE ENVIO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($noticias->num_rows > 0): ?>
                                <?php while ($noticia = $noticias->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                        <td><?= htmlspecialchars($noticia['categoria_nombre']) ?></td>
                                        <td><?= $noticia['fecha_programada_formatted'] ?></td>
                                        <td><?= htmlspecialchars($noticia['autor']) ?></td>
                                        <td>
                                            <a href="../funciones/publicar_ahora.php?id=<?= $noticia['id'] ?>" class="btn btn-sm publicar" title="Publicar ahora">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                            <button class="editar" title="Editar" data-id="<?= $noticia['id'] ?>" data-bs-toggle="modal" data-bs-target="#editarNoticiaModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="eliminar" title="Eliminar" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $noticia['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                                        No hay noticias pendientes de revisión
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de edición de noticia -->
    <div class="modal fade" id="editarNoticiaModal" tabindex="-1" aria-labelledby="editarNoticiaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #003366;">
                    <h5 class="modal-title" id="editarNoticiaModalLabel">Editar Noticia Programada</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-editar-noticia" action="../funciones/actualizar_noticia.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="noticia-id">
                    <input type="hidden" name="accion" id="accion-hidden" value="programar">

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

                                <!-- Campo para fecha de programación (siempre visible para noticias programadas) -->
                                <div class="box-content mb-3">
                                    <label for="edit-fecha_programada" class="form-label">FECHA PROGRAMADA</label>
                                    <input type="text" name="fecha_programada" id="edit-fecha_programada" class="form-control" placeholder="Seleccione fecha y hora" required>
                                    <small class="text-muted">Seleccione cuándo desea que se publique automáticamente (mínimo 5 minutos en el futuro)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-guardar-cambios">
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
                    <p>¿Estás seguro que deseas eliminar esta noticia programada permanentemente?</p>
                    <p class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-primary" style="background-color: #003366; border-color: #003366;">
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
            minuteIncrement: 5
        });

        // Manejar el modal de edición
        const editarNoticiaModal = document.getElementById('editarNoticiaModal');
        if (editarNoticiaModal) {
            editarNoticiaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
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

                        // Configurar fecha programada (siempre visible para noticias programadas)
                        document.getElementById('edit-fecha_programada').value = noticia.fecha_programada;

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
            // Asegurar que el contenido del editor se copie al textarea
            document.getElementById('edit-contenido').value = editQuill.root.innerHTML;

            // Validar campos requeridos
            const titulo = document.getElementById('edit-titulo').value.trim();
            const resumen = document.getElementById('edit-resumen').value.trim();
            const contenido = document.getElementById('edit-contenido').value.trim();
            const categoria = document.getElementById('edit-categoria').value;
            const fechaProgramada = document.getElementById('edit-fecha_programada').value;

            if (!titulo || !resumen || !contenido || !categoria || !fechaProgramada) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return;
            }

            // Validar que la fecha programada sea en el futuro
            const now = new Date();
            const selectedDate = new Date(fechaProgramada);

            if (selectedDate <= now) {
                e.preventDefault();
                alert('La fecha programada debe ser en el futuro');
                return;
            }

            // Validar que el contenido no sea solo HTML vacío
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = contenido;
            if (tempDiv.textContent.trim() === '') {
                e.preventDefault();
                alert('El contenido de la noticia no puede estar vacío');
                return;
            }
        });

        // Manejar el modal de eliminación
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        if (confirmDeleteModal) {
            confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const noticiaId = button.getAttribute('data-id');
                const deleteBtn = document.getElementById('confirmDeleteBtn');
                deleteBtn.href = `../funciones/eliminar_noticia.php?id=${noticiaId}&origen=programadas`;
            });
        }

        // Manejar el envío del formulario para prevenir doble envío
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitButton = this.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
                }
            });
        });

        // Mostrar alertas con timeout
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade');
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        });
    </script>
</body>

</html>
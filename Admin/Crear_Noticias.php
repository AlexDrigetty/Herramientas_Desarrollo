<?php include 'admin_navbar.php'; ?>
<?php include 'admin_auth.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Noticia | Noticias Globales</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="crear">
                <form id="form-noticia" action="../funciones/guardar_noticia.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="accion" id="accion-hidden" value="publicar">

                    <div class="row box-1">
                        <div class="col-md-7">
                            <div class="box-content mb-3">
                                <label for="titulo" class="form-label mb-2">TITULO</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingrese Titulo" required>
                            </div>
                            <div class="box-content mb-3">
                                <label for="resumen" class="form-label mb-2">RESUMEN</label>
                                <textarea name="resumen" id="resumen" class="form-control" placeholder="Ingrese breve resumen de la noticia" rows="3" required></textarea>
                            </div>

                            <div class="box-content">
                                <label for="contenido" class="form-label mb-2">CONTENIDO</label>
                                <div id="editor" style="height: 320px; background-color: white;"></div>
                                <textarea id="contenido" name="contenido" style="display:none;" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="box-content mb-4">
                                <label class="form-label mb-2">FOTO DE PORTADA</label>
                                <input type="file" id="portada" name="portada" accept="image/*" style="display: none;" required>
                                <div class="image-upload-container" id="image-upload-container">
                                    <div class="image-preview" id="image-preview">
                                        <img id="preview" src="#" alt="Vista previa de la imagen" style="display: none;">
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
                                        <label for="tipo_noticia" class="form-label mb-2">TIPO DE NOTICIA</label>
                                        <select name="tipo_noticia" id="tipo_noticia" class="form-select" required>
                                            <option value="">Seleccione tipo</option>
                                            <option value="nacional">Nacional</option>
                                            <option value="internacional">Internacional</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="categoria">
                                        <label for="categoria" class="form-label mb-2">CATEGORIA</label>
                                        <select name="categoria" id="categoria" class="form-select" required>
                                            <option value="">Seleccione una categoria</option>
                                            <option value="politica">Política</option>
                                            <option value="economia">Economía</option>
                                            <option value="deportes">Deportes</option>
                                            <option value="tecnologia">Tecnología</option>
                                            <option value="cultura">Cultura</option>
                                            <option value="salud">Salud</option>
                                            <option value="medio-ambiente">Medio Ambiente</option>
                                            <option value="educacion">Educación</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo para fecha de programación -->
                            <div class="box-content mb-3">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" id="programar-noticia" name="programar_noticia">
                                    <label class="form-check-label" for="programar-noticia">PROGRAMAR FECHA</label>
                                </div>

                                <div id="programacion-container" class="programacion-container" style="display: none;">
                                    <input type="text" name="fecha_programada" id="fecha_programada" class="form-control" placeholder="Seleccione fecha y hora" required>
                                    <small class="text-muted">Seleccione cuándo desea que se publique automáticamente (mínimo 5 minutos en el futuro)</small>
                                </div>
                            </div>

                            <div class="botones mt-4">
                                <button type="submit" class="btn btn-primary" id="btn-publicar">
                                    <i class="fa fa-paper-plane me-2"></i> Publicar ahora
                                </button>
                                <button type="button" id="btn-programar" class="btn btn-secondary" style="display: none;">
                                    <i class="fa fa-calendar-check me-2"></i> Programar publicación
                                </button>
                                <button type="button" id="previsualizar" class="btn btn-outline-secondary">
                                    <i class="fa fa-eye me-2"></i> Visualizar
                                </button>
                                <button type="reset" class="btn btn-outline-danger">
                                    <i class="fa fa-times me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        // Inicializar el editor Quill
        const quill = new Quill('#editor', {
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

        quill.on('text-change', function() {
            document.getElementById('contenido').value = quill.root.innerHTML;
        });

        // Vista previa de la imagen
        document.getElementById('portada').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            const placeholder = document.querySelector('.upload-placeholder');

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

        // Manejar el clic en el contenedor de imagen
        document.getElementById('image-upload-container').addEventListener('click', function() {
            document.getElementById('portada').click();
        });

        // Previsualización de la noticia
        document.getElementById('previsualizar').addEventListener('click', function() {
            const form = document.getElementById('form-noticia');
            const formData = new FormData(form);

            // Validar campos requeridos
            if (!form.titulo.value || !form.resumen.value || !quill.getText().trim() || !form.portada.files[0]) {
                alert('Por favor complete todos los campos requeridos antes de previsualizar');
                return;
            }

            // Abrir en una nueva pestaña
            const previewWindow = window.open('', '_blank');

            // Enviar datos a un script de previsualización
            fetch('../funciones/vizualizar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    previewWindow.document.write(html);
                    previewWindow.document.close();
                })
                .catch(error => {
                    previewWindow.document.write('<h2>Error al generar la previsualización</h2>');
                    previewWindow.document.close();
                });
        });

        // Control de la interfaz de programación
        document.getElementById('programar-noticia').addEventListener('change', function() {
            const programacionContainer = document.getElementById('programacion-container');
            const btnPublicar = document.getElementById('btn-publicar');
            const btnProgramar = document.getElementById('btn-programar');
            const accionHidden = document.getElementById('accion-hidden');

            if (this.checked) {
                programacionContainer.style.display = 'block';
                btnPublicar.style.display = 'none';
                btnProgramar.style.display = 'inline-block';
                accionHidden.value = 'programar';

                // Mostrar el datetimepicker
                if (window.flatpickrInstances && window.flatpickrInstances.fecha_programada) {
                    window.flatpickrInstances.fecha_programada.open();
                }
            } else {
                programacionContainer.style.display = 'none';
                btnPublicar.style.display = 'inline-block';
                btnProgramar.style.display = 'none';
                accionHidden.value = 'publicar';
                document.getElementById('fecha_programada').value = '';
            }
        });

        // Manejar el clic en el botón Programar
        document.getElementById('btn-programar').addEventListener('click', function() {
            const form = document.getElementById('form-noticia');
            const fechaProgramada = document.getElementById('fecha_programada').value;

            if (!fechaProgramada) {
                alert('Por favor seleccione una fecha y hora para programar la publicación');
                return;
            }

            const ahora = new Date();
            const fechaSeleccionada = new Date(fechaProgramada);
            const diferenciaMinutos = (fechaSeleccionada - ahora) / (1000 * 60);

            if (diferenciaMinutos < 5) {
                alert('La publicación debe programarse con al menos 5 minutos de anticipación');
                return;
            }

            // Enviar el formulario
            form.submit();
        });

        // Inicializar datetimepicker
        window.flatpickrInstances = window.flatpickrInstances || {};
        window.flatpickrInstances.fecha_programada = flatpickr("#fecha_programada", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            minTime: new Date().getHours() + ":" + (new Date().getMinutes() + 5),
            time_24hr: true,
            locale: "es",
            minuteIncrement: 5,
            defaultDate: new Date(Date.now() + 3600000) // 1 hora en el futuro
        });

        // Validación al enviar el formulario
        document.getElementById('form-noticia').addEventListener('submit', function(e) {
            // Asegurar que el contenido del editor se guarde
            document.getElementById('contenido').value = quill.root.innerHTML;

            // Validar campos requeridos
            if (!this.titulo.value || !this.resumen.value || !quill.getText().trim()) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
                return;
            }

            // Validar imagen (requerida siempre)
            if (!this.portada.files[0]) {
                e.preventDefault();
                alert('Por favor seleccione una imagen de portada');
                return;
            }

            // Si está en modo programación, validar fecha
            if (document.getElementById('programar-noticia').checked) {
                const fechaProgramada = document.getElementById('fecha_programada').value;
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
    </script>
</body>

</html>
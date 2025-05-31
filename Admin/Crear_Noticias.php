<?php include 'admin_navbar.php';?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Noticia</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <main>
    <?php include 'slider.php';?>
        <div class="crear">
            <div class="row box-1">
                <div class="col-md-7">
                    <div class="box-content mb-3">
                        <label for="Titulo">TITULO</label>
                        <input type="text" placeholder="Ingrese Titulo">
                    </div>
                    <div class="box-content mb-3">
                        <label for="Contenido">RESUMEN</label>
                        <textarea name="contenido" id="" placeholder="Ingrese breve resumen de la noticia"></textarea>
                    </div>


                    <div class=" box-content">
                        <label for="contenido">CONTENIDO </label>
                        <!-- Reemplazar textarea por editor enriquecido -->
                        <div id="editor" style="height: 320px;"></div>
                        <textarea id="contenido" name="contenido" style="display:none;"></textarea>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="box-content mb-4">
                        <label>FOTO DE PORTADA</label>

                        <!-- Input file oculto -->
                        <input type="file" id="portada" accept="image/*" style="display: none;">

                        <!-- Contenedor clickable para la imagen -->
                        <div class="image-upload-container" id="image-preview-container">
                            <div class="image-preview" id="image-preview">
                                <img id="preview" src="#" alt="Vista previa de la imagen">
                                <div class="upload-placeholder">
                                    <i class="fas fa-camera"></i>
                                    <span>Haz clic para seleccionar una imagen</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row content ">
                        <div class="col-md-6">
                            <div class="tipo-noticia">
                                <label for="tipo-noticia">TIPO DE NOTICIA</label>
                                <select name="" id="" class="form-select">Selecione el tipo
                                    <option value="">Nacional</option>
                                    <option value="">Internacional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="categoria">
                                <label for="Categoria">CATEGORIA</label>
                                <select name="categoria" id="" class="form-select">
                                    <option value="">Seleccione una categoria</option>
                                    <option value="">Deportes</option>
                                    <option value="">Cultura</option>
                                    <option value="">Tecnologia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="botones">
                        <button type="submit" class="btn-primary">
                            <i class="fa fa-paper-plane"></i> PUBLICAR
                        </button>
                        <button type="button" id="guardar-borrador" class="btn-secondary">
                            <i class="fa fa-save"></i> GUARDAR
                        </button>
                        <button type="button" id="previsualizar" class="btn-secondary">
                            <i class="fa fa-eye"></i> PREVISUALIZAR
                        </button>
                        <button type="reset" class="btn-cancel">
                            <i class="fa fa-times"></i> CANCELAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Escriba el contenido de la noticia aquí...'
        });

        quill.on('text-change', function () {
            document.getElementById('contenido').value = quill.root.innerHTML;
        });

        // Vista previa de la imagen
        document.getElementById('portada').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('image-preview');
            const fileName = document.querySelector('.file-name');

            if (file) {
                fileName.textContent = file.name;

                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Manejar el envío del formulario
        document.getElementById('form-noticia').addEventListener('submit', function (e) {
            e.preventDefault();
            // Validación adicional puede ir aquí
            alert('Noticia publicada con éxito!');
            // this.submit(); // Descomentar para enviar realmente el formulario
        });

        // Manejar guardar borrador
        document.getElementById('guardar-borrador').addEventListener('click', function () {
            alert('Borrador guardado correctamente');
        });

        // Manejar previsualización
        document.getElementById('previsualizar').addEventListener('click', function () {
            alert('Esta función abriría una ventana de previsualización');
        });
    </script>

    <script>
        // Elementos del DOM
        const fileInput = document.getElementById('portada');
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('preview');
        const placeholder = document.querySelector('.upload-placeholder');
        const fileName = document.querySelector('.file-name');

        // Cuando se hace clic en el contenedor
        previewContainer.addEventListener('click', function () {
            fileInput.click(); // Simula clic en el input file
        });

        // Cuando se selecciona un archivo
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file && file.type.match('image.*')) {
                const reader = new FileReader();

                reader.onload = function (event) {
                    // Mostrar la imagen
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';

                    // Mostrar nombre del archivo
                    fileName.textContent = file.name;

                    // Opcional: Mostrar dimensiones
                    const img = new Image();
                    img.onload = function () {
                        fileName.textContent = `${file.name} (${this.width}×${this.height}px)`;
                    };
                    img.src = event.target.result;
                }

                reader.readAsDataURL(file);
            } else if (file) {
                alert('Por favor, selecciona un archivo de imagen válido (JPEG, PNG, etc.)');
                resetFileInput();
            }
        });

        // Función para resetear el input
        function resetFileInput() {
            fileInput.value = '';
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            fileName.textContent = 'No hay archivo seleccionado';
        }

        // Opcional: Permitir arrastrar y soltar imágenes
        previewContainer.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = '#2a5f8a';
            this.style.backgroundColor = '#e6f0f7';
        });

        previewContainer.addEventListener('dragleave', function () {
            this.style.borderColor = '#ccc';
            this.style.backgroundColor = '#f9f9f9';
        });

        previewContainer.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '#ccc';
            this.style.backgroundColor = '#f9f9f9';

            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
</body>

</html>
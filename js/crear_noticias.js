        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Escriba el contenido de la noticia aquí...'
        });
        
        // Actualizar textarea oculto cuando el editor cambia
        quill.on('text-change', function() {
            document.getElementById('contenido').value = quill.root.innerHTML;
        });
        
        // Vista previa de la imagen
        document.getElementById('portada').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('image-preview');
            const fileName = document.querySelector('.file-name');
            
            if (file) {
                fileName.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Manejar el envío del formulario
        document.getElementById('form-noticia').addEventListener('submit', function(e) {
            e.preventDefault();
            // Validación adicional puede ir aquí
            alert('Noticia publicada con éxito!');
            // this.submit(); // Descomentar para enviar realmente el formulario
        });
        
        // Manejar guardar borrador
        document.getElementById('guardar-borrador').addEventListener('click', function() {
            alert('Borrador guardado correctamente');
        });
        
        // Manejar previsualización
        document.getElementById('previsualizar').addEventListener('click', function() {
            alert('Esta función abriría una ventana de previsualización');
        });
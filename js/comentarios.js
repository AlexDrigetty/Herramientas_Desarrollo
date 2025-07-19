document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de respuesta
    document.querySelectorAll('.btn-respuesta').forEach(btn => {
        btn.addEventListener('click', function() {
            const comentarioId = this.getAttribute('data-comentario');
            const formRespuesta = document.getElementById(`form-respuesta-${comentarioId}`);
            
            // Ocultar todos los formularios primero
            document.querySelectorAll('.form-respuesta').forEach(form => {
                form.style.display = 'none';
            });
            
            // Mostrar el formulario correspondiente
            formRespuesta.style.display = 'block';
            formRespuesta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
    
    // Manejar botones de cancelar
    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', function() {
            const comentarioId = this.getAttribute('data-comentario');
            document.getElementById(`form-respuesta-${comentarioId}`).style.display = 'none';
        });
    });
    
    // Scroll a los comentarios si hay hash
    if (window.location.hash === '#comentarios') {
        document.getElementById('lista-comentarios').scrollIntoView();
    }
});
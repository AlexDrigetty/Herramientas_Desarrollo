document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');
    
    // Función para buscar noticias
    function buscarNoticias() {
        const textoBusqueda = searchInput.value.trim().toLowerCase();
        
        if (textoBusqueda === '') {
            // Recargar la página para mostrar todas las noticias
            window.location.reload();
            return;
        }
        
        // Obtener todas las tarjetas de noticias
        const noticias = document.querySelectorAll('.news');
        
        noticias.forEach(noticia => {
            const titulo = noticia.querySelector('h3').textContent.toLowerCase();
            const resumen = noticia.querySelector('p').textContent.toLowerCase();
            
            if (titulo.includes(textoBusqueda) || resumen.includes(textoBusqueda)) {
                noticia.closest('.col-6').style.display = 'block';
            } else {
                noticia.closest('.col-6').style.display = 'none';
            }
        });
    }
    
    // Event listeners
    searchButton.addEventListener('click', buscarNoticias);
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarNoticias();
        }
    });
});
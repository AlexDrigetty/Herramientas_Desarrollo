document.addEventListener('DOMContentLoaded', function () {
    // Configuración de paginación
    const noticiasPorPagina = 8;
    const paginasAMostrar = 5;
    let paginaActual = 1;
    let noticiasFiltradas = [];
    let enModoBusqueda = false;

    // Elementos del DOM
    const newsContainer = document.getElementById('news-container');
    const pagination = document.getElementById('pagination');
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    // Mostrar noticias inicialmente
    mostrarNoticias();

    searchButton.addEventListener('click', buscarNoticias);
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') buscarNoticias();
    });

    // Función para mostrar noticias
    function mostrarNoticias() {
        newsContainer.innerHTML = '';

        const noticiasAMostrar = enModoBusqueda ? noticiasFiltradas : noticiasData;
        const noticiasPagina = noticiasAMostrar.slice(
            (paginaActual - 1) * noticiasPorPagina,
            paginaActual * noticiasPorPagina
        );

        if (noticiasPagina.length === 0) {
            mostrarMensajeSinResultados();
            return;
        }

        newsContainer.innerHTML = noticiasPagina.map(noticia => `
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="news">
                    <div class="imagen">
                        <img src="${noticia.imagen}" alt="${noticia.titulo}" 
                             onerror="this.src='https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg'">
                    </div>
                    <div class="contenido-noticia">
                        <div class="contenido-etiqueta mb-2">
                        <span class="categoria nacional">${noticia.tipo}</span>
                            <span class="categoria" style="background: ${noticia.categoria_color}">
                                ${noticia.categoria_nombre}
                            </span>
                            
                        </div>
                        <h3>${noticia.titulo}</h3>
                        <p>${noticia.resumen}</p>
                        <div class="metas mb-1">
                            <span><i class="far fa-clock"></i> ${calcularTiempoTranscurrido(noticia.fecha)}</span>
                            <a href="${noticia.enlace}" class="vermas">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        actualizarPaginacion();
    }

    function actualizarPaginacion() {
        const noticiasTotales = enModoBusqueda ? noticiasFiltradas : noticiasData;
        const totalPaginas = Math.ceil(noticiasTotales.length / noticiasPorPagina);

        pagination.innerHTML = '';

        pagination.appendChild(crearBotonPaginacion(
            'Anterior',
            false,
            () => cambiarPagina(paginaActual - 1),
            false,
            paginaActual === 1
        ));

        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, paginaActual + 2);

        for (let i = inicio; i <= fin; i++) {
            pagination.appendChild(crearBotonPaginacion(
                i,
                i === paginaActual,
                () => cambiarPagina(i),
                true
            ));
        }

        pagination.appendChild(crearBotonPaginacion(
            'Siguiente',
            false,
            () => cambiarPagina(paginaActual + 1),
            false,
            paginaActual === totalPaginas
        ));
    }

    function crearBotonPaginacion(texto, esActivo, onClick, esNumero = true, disabled = false) {
        const li = document.createElement('li');
        li.className = `page-item ${esActivo ? 'active' : ''} ${esNumero ? 'number-page' : ''} ${disabled ? 'disabled' : ''}`;

        const link = document.createElement('a');
        link.className = 'page-link';
        link.href = '#';
        link.textContent = texto;

        if (!disabled) {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                if (!esActivo) onClick();
            });
        }

        li.appendChild(link);
        return li;
    }

    // Función para cambiar de página
    function cambiarPagina(nuevaPagina) {
        paginaActual = nuevaPagina;
        mostrarNoticias();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Función para buscar noticias
    function buscarNoticias() {
        const textoBusqueda = normalizarTexto(searchInput.value.trim());

        if (textoBusqueda === '') {
            enModoBusqueda = false;
        } else {
            enModoBusqueda = true;
            noticiasFiltradas = noticiasData.filter(noticia => {
                const tituloNormalizado = normalizarTexto(noticia.titulo);
                const resumenNormalizado = normalizarTexto(noticia.resumen);
                return tituloNormalizado.includes(textoBusqueda) ||
                    resumenNormalizado.includes(textoBusqueda);
            });
        }

        paginaActual = 1;
        mostrarNoticias();
    }

    // Función para normalizar texto (eliminar acentos y convertir a minúsculas)
    function normalizarTexto(texto) {
        return texto.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    // Función para calcular tiempo transcurrido
    function calcularTiempoTranscurrido(fechaPublicacion) {
        if (!fechaPublicacion) return 'Fecha desconocida';

        const fechaPub = new Date(fechaPublicacion);
        const ahora = new Date();
        const diferencia = ahora - fechaPub;

        const segundos = Math.floor(diferencia / 1000);
        const minutos = Math.floor(segundos / 60);
        const horas = Math.floor(minutos / 60);
        const dias = Math.floor(horas / 24);

        if (dias > 0) {
            return `Hace ${dias} ${dias === 1 ? 'día' : 'días'}`;
        } else if (horas > 0) {
            return `Hace ${horas} ${horas === 1 ? 'hora' : 'horas'}`;
        } else if (minutos > 0) {
            return `Hace ${minutos} ${minutos === 1 ? 'minuto' : 'minutos'}`;
        } else {
            return 'Hace unos momentos';
        }
    }

    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeSinResultados() {
        newsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <h3>No se encontraron noticias nacionales</h3>
                <p>Intenta con otros términos de búsqueda.</p>
            </div>
        `;
        pagination.style.display = 'none';
    }
});
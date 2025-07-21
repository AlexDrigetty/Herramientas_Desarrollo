// Variables globales
const noticiasPorPagina = 8;
let paginaActual = 1;
let noticiasFiltradas = [...noticiasData];
let filtrosActivos = {
    categoria: '',
    fecha: '',
    texto: ''
};

// Elementos del DOM
const categoryFilter = document.getElementById('category-filter');
const dateFilter = document.getElementById('date-filter');
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');

// Inicialización
document.addEventListener('DOMContentLoaded', function () {
    // Procesar fechas para formato relativo
    noticiasFiltradas.forEach(noticia => {
        noticia.fechaRelativa = getFechaRelativa(noticia.fecha);
    });
    
    mostrarNoticias();
    configurarEventListeners();
});

// Configurar event listeners
function configurarEventListeners() {
    categoryFilter.addEventListener('change', aplicarFiltros);
    dateFilter.addEventListener('change', aplicarFiltros);
    searchButton.addEventListener('click', buscarNoticias);
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') buscarNoticias();
    });
}

// Función para normalizar texto (eliminar tildes)
function normalizarTexto(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

// Función para obtener fecha relativa
function getFechaRelativa(fechaString) {
    const ahora = new Date();
    const fechaNoticia = new Date(fechaString);
    const diferencia = new Date(ahora - fechaNoticia);
    
    const segundos = Math.floor(diferencia / 1000);
    const minutos = Math.floor(segundos / 60);
    const horas = Math.floor(minutos / 60);
    const dias = Math.floor(horas / 24);
    
    if (dias > 0) return `Hace ${dias} día${dias > 1 ? 's' : ''}`;
    if (horas > 0) return `Hace ${horas} hora${horas > 1 ? 's' : ''}`;
    if (minutos > 0) return `Hace ${minutos} minuto${minutos > 1 ? 's' : ''}`;
    return "Hace unos segundos";
}

function aplicarFiltros() {
    // Capturar valores de los filtros
    filtrosActivos = {
        categoria: categoryFilter.value,
        fecha: dateFilter.value,
        texto: filtrosActivos.texto
    };

    // Obtener fecha/hora actual para comparaciones
    const ahora = new Date();
    
    // Aplicar todos los filtros
    noticiasFiltradas = noticiasData.filter(noticia => {
        // Filtro por categoría
        if (filtrosActivos.categoria && noticia.categoria_nombre.toLowerCase() !== filtrosActivos.categoria.toLowerCase()) {
            return false;
        }

        // Filtro por fecha
        if (filtrosActivos.fecha) {
            const fechaNoticia = new Date(noticia.fecha);
            const diferenciaHoras = (ahora - fechaNoticia) / (1000 * 60 * 60);
            
            switch (filtrosActivos.fecha) {
                case 'hoy':
                    if (diferenciaHoras >= 24) return false;
                    break;
                case 'ayer':
                    if (diferenciaHoras < 24 || diferenciaHoras >= 48) return false;
                    break;
                case 'ultima-semana':
                    if (diferenciaHoras < 48 || diferenciaHoras >= 168) return false; // 168 horas = 7 días
                    break;
                case 'ultimo-mes':
                    if (diferenciaHoras < 168 || diferenciaHoras >= 720) return false; // 720 horas = 30 días
                    break;
            }
        }

        // Filtro por texto
        if (filtrosActivos.texto) {
            const textoBusqueda = normalizarTexto(filtrosActivos.texto);
            const tituloNormalizado = normalizarTexto(noticia.titulo);
            const resumenNormalizado = normalizarTexto(noticia.resumen);

            if (!tituloNormalizado.includes(textoBusqueda) &&
                !resumenNormalizado.includes(textoBusqueda)) {
                return false;
            }
        }

        return true;
    });

    // Actualizar fechas relativas
    noticiasFiltradas.forEach(noticia => {
        noticia.fechaRelativa = getFechaRelativa(noticia.fecha);
    });
    
    paginaActual = 1;
    mostrarNoticias();
}

// Función de búsqueda
function buscarNoticias() {
    const textoBusqueda = searchInput.value.trim();
    filtrosActivos.texto = textoBusqueda;
    aplicarFiltros();
}

// Función para mostrar noticias
function mostrarNoticias() {
    const newsContainer = document.getElementById('news-container');
    newsContainer.innerHTML = '';

    const inicio = (paginaActual - 1) * noticiasPorPagina;
    const fin = inicio + noticiasPorPagina;
    const noticiasPagina = noticiasFiltradas.slice(inicio, fin);

    if (noticiasPagina.length === 0) {
        newsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <h3>No se encontraron noticias con los filtros aplicados</h3>
            </div>
        `;
    } else {
        noticiasPagina.forEach(noticia => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 mb-4';

            col.innerHTML = `
                <div class="news">
                    <div class="imagen">
                        <img src="${noticia.imagen}" alt="${noticia.titulo}">
                    </div>
                    <div class="contenido-noticia">
                        <div class="contenido-etiqueta mb-2">
                            <span class="categoria nacional">Nacional</span>
                            <span class="categoria" style="background-color: ${noticia.categoria_color}">${noticia.categoria_nombre}</span>
                        </div>
                        <h3>${noticia.titulo}</h3>
                        <p>${noticia.resumen}</p>
                        <div class="metas mb-1">
                            <span><i class="far fa-clock"></i> ${noticia.fechaRelativa}</span>
                            <a href="${noticia.enlace}" class="vermas">Ver más</a>
                        </div>
                    </div>
                </div>
            `;
            newsContainer.appendChild(col);
        });
    }

    actualizarPaginacion();
}

// Función para actualizar la paginación
function actualizarPaginacion() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    const totalPaginas = Math.ceil(noticiasFiltradas.length / noticiasPorPagina);

    if (totalPaginas <= 1) return;

    // Botón Anterior
    const liAnterior = document.createElement('li');
    liAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
    liAnterior.innerHTML = `<a class="page-link" href="#" tabindex="-1">Anterior</a>`;
    liAnterior.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual > 1) {
            paginaActual--;
            mostrarNoticias();
        }
    });
    pagination.appendChild(liAnterior);

    // Números de página
    const maxPaginasVisibles = 10;
    let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxPaginasVisibles / 2));
    let finPaginas = Math.min(totalPaginas, inicioPaginas + maxPaginasVisibles - 1);

    if (finPaginas - inicioPaginas < maxPaginasVisibles - 1) {
        inicioPaginas = Math.max(1, finPaginas - maxPaginasVisibles + 1);
    }

    if (inicioPaginas > 1) {
        const li = document.createElement('li');
        li.className = 'page-item';
        li.innerHTML = `<a class="page-link" href="#">1</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            paginaActual = 1;
            mostrarNoticias();
        });
        pagination.appendChild(li);

        if (inicioPaginas > 2) {
            const liEllipsis = document.createElement('li');
            liEllipsis.className = 'page-item disabled';
            liEllipsis.innerHTML = `<span class="page-link">...</span>`;
            pagination.appendChild(liEllipsis);
        }
    }

    for (let i = inicioPaginas; i <= finPaginas; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            paginaActual = i;
            mostrarNoticias();
        });
        pagination.appendChild(li);
    }

    if (finPaginas < totalPaginas) {
        if (finPaginas < totalPaginas - 1) {
            const liEllipsis = document.createElement('li');
            liEllipsis.className = 'page-item disabled';
            liEllipsis.innerHTML = `<span class="page-link">...</span>`;
            pagination.appendChild(liEllipsis);
        }

        const li = document.createElement('li');
        li.className = 'page-item';
        li.innerHTML = `<a class="page-link" href="#">${totalPaginas}</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            paginaActual = totalPaginas;
            mostrarNoticias();
        });
        pagination.appendChild(li);
    }

    // Botón Siguiente
    const liSiguiente = document.createElement('li');
    liSiguiente.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
    liSiguiente.innerHTML = `<a class="page-link" href="#">Siguiente</a>`;
    liSiguiente.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual < totalPaginas) {
            paginaActual++;
            mostrarNoticias();
        }
    });
    pagination.appendChild(liSiguiente);
}
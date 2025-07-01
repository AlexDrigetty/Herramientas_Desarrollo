const noticiasPorPagina = 20; // Cambiado a 12 noticias por página
const paginasAMostrar = 20;   // Mostrar hasta 10 páginas en la paginación
let paginaActual = 1;
let noticiasFiltradas = [];
let enModoBusqueda = false;
let noticiasCompletas = [];
let categoriaActual = 'todas';

// Configuración de categorías (actualizada para coincidir con NewsAPI)
const configCategorias = {
    'business': {
        nombreES: 'economía',
        claseCSS: 'economía',
        imagen: 'https://fpablovi.org/images/2022/01/31/socioeconomica.jpg'
    },
    'entertainment': {
        nombreES: 'cultura',
        claseCSS: 'cultura',
        imagen: 'https://cdn.pixabay.com/photo/2016/11/22/19/15/dark-1850121_1280.jpg'
    },
    'general': {
        nombreES: 'general',
        claseCSS: '',
        imagen: 'https://via.placeholder.com/300x200?text=Noticia'
    },
    'health': {
        nombreES: 'salud',
        claseCSS: 'salud',
        imagen: 'https://cdn.pixabay.com/photo/2017/10/29/11/30/laboratory-2899658_1280.jpg'
    },
    'science': {
        nombreES: 'ciencia',
        claseCSS: 'ciencia',
        imagen: 'https://cdn.pixabay.com/photo/2016/09/08/21/09/science-1655783_1280.jpg'
    },
    'sports': {
        nombreES: 'deportes',
        claseCSS: 'deportes',
        imagen: 'https://www.wipo.int/documents/d/wipo/getty_476895307_845'
    },
    'technology': {
        nombreES: 'tecnología',
        claseCSS: 'tecnología',
        imagen: 'https://cdn.pixabay.com/photo/2019/02/06/16/32/architect-3979490_1280.jpg'
    },
    // Agrego algunas categorías adicionales que tienes en tu CSS
    'environment': {
        nombreES: 'medio-ambiente',
        claseCSS: 'medio-ambiente',
        imagen: 'https://cdn.pixabay.com/photo/2016/11/29/08/41/apple-1868496_1280.jpg'
    },
    'politics': {
        nombreES: 'política',
        claseCSS: 'política',
        imagen: 'https://www.elviejotopo.com/wp-content/uploads/2016/08/Poder1.jpg'
    }
};

// Función para obtener la imagen adecuada según categoría
function obtenerImagen(noticia) {
    if (noticia.urlToImage) return noticia.urlToImage;
    const categoria = noticia.category ? noticia.category : 'general';
    return configCategorias[categoria]?.imagen || 'https://via.placeholder.com/300x200?text=Noticia';
}

// Función para traducir categoría
function traducirCategoria(categoria) {
    return configCategorias[categoria]?.nombreES || categoria;
}

// Función principal para obtener noticias
async function obtenerNoticiasInternacionales() {
    const apiKey = '5bdd0c4372a34d718d9ef84931150e53';
    let url;

    if (categoriaActual === 'todas') {
        // Obtener noticias generales si no hay categoría seleccionada
        url = `https://newsapi.org/v2/everything?q=mundo&language=es&apiKey=${apiKey}`;
    } else {
        // Obtener noticias específicas de la categoría
        const query = configCategorias[categoriaActual]?.query || categoriaActual;
        url = `https://newsapi.org/v2/top-headlines?country=us&apiKey=${apiKey}`;;
    }

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const data = await response.json();

        if (data.articles?.length > 0) {
            noticiasCompletas = data.articles.map((noticia, index) => {
                return {
                    id: index + 1,
                    titulo: noticia.title || 'Sin título',
                    imagen: obtenerImagen(noticia),
                    resumen: noticia.description || 'Sin descripción',
                    fecha: calcularTiempoTranscurrido(noticia.publishedAt),
                    tipo: "internacional",
                    categoria: traducirCategoria(categoriaActual),
                    categoriaOriginal: categoriaActual,
                    enlace: noticia.url || '#'
                };
            });

            mostrarNoticias();
            actualizarFiltrosCategoria();
        } else {
            mostrarMensajeSinResultados();
        }
    } catch (error) {
        console.error('Error al obtener noticias:', error);
        mostrarMensajeError('No se pudieron cargar las noticias. Intenta más tarde.');
    }
}

// Función para actualizar los filtros de categoría en la UI
function actualizarFiltrosCategoria() {
    const filtroContainer = document.getElementById('filtro-categorias');

    if (!filtroContainer) return;

    filtroContainer.innerHTML = `
        <button class="btn-filtro ${categoriaActual === 'todas' ? 'active' : ''}" 
                data-categoria="todas">Todas</button>
        ${Object.keys(configCategorias).map(cat => `
            <button class="btn-filtro ${categoriaActual === cat ? 'active' : ''}" 
                    data-categoria="${cat}">${traducirCategoria(cat)}</button>
        `).join('')}
    `;

    // Agregar event listeners a los botones
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        btn.addEventListener('click', () => {
            categoriaActual = btn.dataset.categoria;
            obtenerNoticiasInternacionales(); // Volver a cargar noticias con la nueva categoría
        });
    });
}

// Función para mostrar noticias con paginación
function mostrarNoticias() {
    const newsContainer = document.getElementById('news-container');
    newsContainer.innerHTML = '';

    const noticiasAMostrar = enModoBusqueda ? noticiasFiltradas : noticiasCompletas;
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
                     onerror="this.src='${configCategorias[noticia.categoriaOriginal]?.imagen || 'https://via.placeholder.com/300x200?text=Noticia'}'">
            </div>
            <div class="contenido-noticia">
                <div class="contenido-etiqueta mb-2">
                    <span class="categoria internacional">${noticia.tipo}</span>
                    <span class="categoria ${configCategorias[noticia.categoriaOriginal]?.claseCSS || ''}">
                        ${noticia.categoria}
                    </span>
                </div>
                <h3>${noticia.titulo}</h3>
                <p>${noticia.resumen}</p>
                <div class="metas mb-1">
                    <span><i class="far fa-clock"></i> ${noticia.fecha}</span>
                    <a href="${noticia.enlace}" target="_blank" class="vermas">Ver más</a>
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
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Función para calcular el tiempo transcurrido desde la publicación
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
    const newsContainer = document.getElementById('news-container');
    newsContainer.innerHTML = `
        <div class="col-12 text-center py-5">
            <h3>No se encontraron noticias internacionales recientes</h3>
            <p>Intenta actualizar la página más tarde.</p>
        </div>
    `;
    document.getElementById('pagination').style.display = 'none';
}

// Función para mostrar mensaje de error
function mostrarMensajeError(mensaje) {
    const newsContainer = document.getElementById('news-container');
    newsContainer.innerHTML = `
        <div class="col-12 text-center py-5">
            <h3>Error al cargar noticias</h3>
            <p class="text-danger">${mensaje}</p>
            <p>Intenta actualizar la página o vuelve más tarde.</p>
            <button onclick="location.reload()" class="btn btn-primary">Recargar</button>
        </div>
    `;
    document.getElementById('pagination').style.display = 'none';
}

// Función para normalizar texto (para búsquedas)
function normalizarTexto(texto) {
    return texto.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

// Función para buscar noticias
function buscarNoticias() {
    const textoBusqueda = normalizarTexto(document.getElementById('contenido').value.trim());

    if (textoBusqueda === '') {
        enModoBusqueda = false;
    } else {
        enModoBusqueda = true;
        noticiasFiltradas = noticiasCompletas.filter(noticia => {
            const tituloNormalizado = normalizarTexto(noticia.titulo);
            const resumenNormalizado = normalizarTexto(noticia.resumen);
            return tituloNormalizado.includes(textoBusqueda) ||
                resumenNormalizado.includes(textoBusqueda);
        });
    }

    paginaActual = 1;
    mostrarNoticias();
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    obtenerNoticiasInternacionales();

    document.getElementById('buscar').addEventListener('click', buscarNoticias);
    document.getElementById('contenido').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') buscarNoticias();
    });
});
const noticiasPorPagina = 10; // 10 noticias de negocios por página
const paginasAMostrar = 5;    // Mostrar hasta 5 páginas en la paginación
let paginaActual = 1;
let noticiasFiltradas = [];
let enModoBusqueda = false;
let noticiasCompletas = [];
let categoriaActual = 'todas'; // Cambiado a 'todas' por defecto

// Configuración de categorías (actualizada para coincidir con NewsAPI)
const configCategorias = {
    'general': {
        nombreES: 'general',
        claseCSS: '',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'mundo' // Término de búsqueda para noticias generales
    },
    'futbol': {
        nombreES: 'fútbol',
        claseCSS: 'deportes',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'fútbol OR futbol OR "liga española" OR champions'
    },
    'economia': {
        nombreES: 'economía',
        claseCSS: 'economía',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'economía OR economia OR mercado OR bolsa OR finanzas'
    },
    'salud': {
        nombreES: 'salud',
        claseCSS: 'salud',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'salud OR medicina OR hospital OR médico OR vacuna'
    },
    'politica': {
        nombreES: 'política',
        claseCSS: 'política',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'política OR gobierno OR presidente OR congreso OR elecciones'
    },
    'tecnologia': {
        nombreES: 'tecnología',
        claseCSS: 'tecnología',
        imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
        query: 'tecnología OR tecnologia OR "inteligencia artificial" OR robot OR digital'
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


async function obtenerNoticiasPorCategoria(categoria) {
    const apiKey = '5bdd0c4372a34d718d9ef84931150e53';
    const query = configCategorias[categoria].query;
    const url = `https://newsapi.org/v2/everything?q=${encodeURIComponent(query)}&language=es&pageSize=20&apiKey=${apiKey}`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();
        
        if (data.articles?.length > 0) {
            return data.articles.slice(0, 20).map((noticia, index) => ({
                id: noticiasCompletas.length + index + 1,
                titulo: noticia.title || 'Sin título',
                imagen: obtenerImagen(noticia),
                resumen: noticia.description || 'Sin descripción',
                fecha: calcularTiempoTranscurrido(noticia.publishedAt),
                tipo: "internacional",
                categoria: configCategorias[categoria].nombreES,
                categoriaOriginal: categoria,
                enlace: noticia.url || '#'
            }));
        }
        return [];
    } catch (error) {
        console.error(`Error al obtener noticias de ${categoria}:`, error);
        return [];
    }
}

async function obtenerTodasLasNoticias() {
    noticiasCompletas = [];
    const categorias = ['general', 'futbol', 'politica', 'salud', 'economia', 'tecnologia'];
    
    for (const categoria of categorias) {
        const noticias = await obtenerNoticiasPorCategoria(categoria);
        noticiasCompletas = [...noticiasCompletas, ...noticias];
    }

    mostrarNoticias();
    actualizarFiltrosCategoria();
}


// Función principal para obtener noticias




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
            if (categoriaActual === 'todas') {
                mostrarNoticias();
            } else {
                noticiasFiltradas = noticiasCompletas.filter(noticia => 
                    noticia.categoriaOriginal === categoriaActual
                );
                enModoBusqueda = true;
                paginaActual = 1;
                mostrarNoticias();
            }
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

// Función para actualizar la paginación optimizada
function actualizarPaginacion() {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    const noticiasTotales = enModoBusqueda ? noticiasFiltradas : noticiasCompletas;
    const totalPaginas = Math.ceil(noticiasTotales.length / noticiasPorPagina);

    if (totalPaginas <= 1) {
        pagination.style.display = 'none';
        return;
    }

    pagination.style.display = 'flex';
    pagination.innerHTML = '';

    // Calcular rango de páginas a mostrar (máximo 10 páginas)
    let inicio = Math.max(1, paginaActual - Math.floor(paginasAMostrar / 2));
    let fin = Math.min(totalPaginas, inicio + paginasAMostrar - 1);

    if (fin - inicio + 1 < paginasAMostrar) {
        inicio = Math.max(1, fin - paginasAMostrar + 1);
    }

    // Botón Anterior
    pagination.appendChild(crearBotonPaginacion(
        'Anterior', paginaActual === 1, () => {
            if (paginaActual > 1) cambiarPagina(paginaActual - 1);
        }
    ));

    // Páginas
    for (let i = inicio; i <= fin; i++) {
        pagination.appendChild(crearBotonPaginacion(
            i, i === paginaActual, () => cambiarPagina(i)
        ));
    }

    // Botón Siguiente
    pagination.appendChild(crearBotonPaginacion(
        'Siguiente', paginaActual === totalPaginas, () => {
            if (paginaActual < totalPaginas) cambiarPagina(paginaActual + 1);
        }
    ));
}

// Función auxiliar para crear botones de paginación
function crearBotonPaginacion(texto, disabled, onClick) {
    const li = document.createElement('li');
    li.className = `page-item ${disabled ? 'disabled' : ''}`;
    li.innerHTML = `<a class="page-link" href="#">${texto}</a>`;
    li.addEventListener('click', (e) => {
        e.preventDefault();
        onClick();
    });
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
    obtenerTodasLasNoticias(); // Cambiado a la nueva función

    document.getElementById('buscar').addEventListener('click', buscarNoticias);
    document.getElementById('contenido').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') buscarNoticias();
    });
});
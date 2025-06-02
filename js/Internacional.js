const noticias = [
    {
        id: 1,
        titulo: "Presidente anuncia reforma fiscal para apoyar a las pequeñas empresas",
        imagen: "https://radiolaestacion.com.pe/images/2023/Julio/DINA-BOLUARTE-MENSAJE-A-LA-NACION-1.jpg",
        resumen: "El gobierno presentó un paquete de medidas económicas que reducirán impuestos a pymes y emprendedores.",
        fecha: "Hace 2 horas",
        tipo: "nacional",
        categoria: "economía"
    },
    {
        id: 2,
        titulo: "Conflicto en Oriente Medio: ONU convoca reunión de emergencia",
        imagen: "https://global.unitednations.entermediadb.net/assets/mediadb/services/module/asset/downloads/preset/Libraries/Production%20Library/26-10-2023-UN-Photo-PGA-Dennis-Francis-01.jpg/image1170x530cropped.jpg",
        resumen: "El Consejo de Seguridad discutirá el aumento de tensiones en la región tras los últimos enfrentamientos.",
        fecha: "Hace 5 horas",
        tipo: "internacional",
        categoria: "política"
    },
    {
        id: 3,
        titulo: "Ministerio de Salud lanza campaña nacional de vacunación",
        imagen: "https://cdn.www.gob.pe/uploads/document/file/7829354/1133207-foto1.jpeg",
        resumen: "La iniciativa busca inmunizar a 2 millones de personas contra enfermedades prevenibles.",
        fecha: "Ayer",
        tipo: "nacional",
        categoria: "salud"
    },
    {
        id: 4,
        titulo: "Descubren nueva especie marina en las costas del Pacífico",
        imagen: "https://cloudfront-us-east-1.images.arcpublishing.com/infobae/UUJSRQBPWNCLVGND3JG4MPECUQ.jpg",
        resumen: "Científicos nacionales identificaron un tipo de coral único con propiedades medicinales.",
        fecha: "Hace 3 días",
        tipo: "nacional",
        categoria: "medio-ambiente"
    },
    {
        id: 5,
        titulo: "Banco Central Europeo anuncia aumento de tasas de interés",
        imagen: "https://cloudfront-us-east-1.images.arcpublishing.com/infobae/AXT4ZPWGSXFOFCVA2MYLAJD5JQ.jpg",
        resumen: "La medida busca controlar la inflación en la zona euro, que alcanzó el 8.1% interanual.",
        fecha: "Hace 1 día",
        tipo: "internacional",
        categoria: "economía"
    },
    {
        id: 6,
        titulo: "Festival internacional de cine anuncia su programación",
        imagen: "https://images.produ.com/wp-content/uploads/2025/05/09171817/bigwp-FICG2025.jpg",
        resumen: "Se proyectarán 120 películas de 40 países, con énfasis en producciones independientes.",
        fecha: "Hace 6 horas",
        tipo: "internacional",
        categoria: "cultura"
    },
    {
        id: 7,
        titulo: "Gobierno lanza plan de viviendas sociales",
        imagen: "https://cdn.www.gob.pe/uploads/document/file/2015663/WhatsApp%20Image%202021-07-14%20at%205.39.10%20PM.jpeg.jpeg",
        resumen: "Se construirán 15,000 unidades habitacionales en zonas urbanas marginadas.",
        fecha: "Hace 8 horas",
        tipo: "nacional",
        categoria: "política"
    },
    {
        id: 8,
        titulo: "Nueva tecnología permite detectar cáncer en etapa temprana",
        imagen: "https://www.telefonica.com/es/wp-content/uploads/sites/4/2022/02/pexels-artem-podrez-5726794.jpg?w=1224&h=673&crop=1",
        resumen: "Investigadores japoneses desarrollaron un método no invasivo con 95% de precisión.",
        fecha: "Ayer",
        tipo: "internacional",
        categoria: "tecnología"
    },
    {
        id: 9,
        titulo: "Tormenta tropical afecta regiones costeras",
        imagen: "https://imagenes.eltiempo.com/files/og_thumbnail/uploads/2022/09/29/633583aef1261.jpeg",
        resumen: "Defensa Civil activó protocolos de emergencia en 5 estados del litoral.",
        fecha: "Hace 4 horas",
        tipo: "nacional",
        categoria: "clima"
    },
    {
        id: 10,
        titulo: "Acuerdo comercial entre Sudamérica y Asia Pacífico",
        imagen: "https://cloudfront-us-east-1.images.arcpublishing.com/infobae/OK3XOIPAHCRIQ7LVXEZNTOD67E.jpg",
        resumen: "El pacto reducirá aranceles para 500 productos agrícolas y tecnológicos.",
        fecha: "Hace 2 días",
        tipo: "internacional",
        categoria: "economía"
    },
    {
        id: 11,
        titulo: "Universidades públicas anuncian admisiones especiales",
        imagen: "https://portal.andina.pe/EDPFotografia3/thumbnail/2023/08/14/000985039M.jpg",
        resumen: "5,000 cupos adicionales para estudiantes de bajos recursos estarán disponibles.",
        fecha: "Hace 7 horas",
        tipo: "nacional",
        categoria: "educación"
    },
    {
        id: 12,
        titulo: "NASA revela imágenes inéditas de Júpiter",
        imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRY_DSbIW7PaaDtT5T2EFV3bxpX3g6EA2sq2Q&s",
        resumen: "El telescopio James Webb captó detalles nunca vistos de la atmósfera del planeta.",
        fecha: "Ayer",
        tipo: "internacional",
        categoria: "ciencia"
    },
    {
        id: 13,
        titulo: "Nueva ley protege derechos de trabajadores remotos",
        imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9kIvkoyBC4dv-tq5kC9LgOkts-Z6LRJqBDg&s",
        resumen: "Establece jornadas máximas y compensaciones por uso de servicios básicos.",
        fecha: "Hace 3 horas",
        tipo: "nacional",
        categoria: "política"
    },
    {
        id: 14,
        titulo: "Escasez de chips afecta producción automotriz en Europa",
        imagen: "https://cloudfront-us-east-1.images.arcpublishing.com/infobae/Y2MU3HQ7OBBNXNUBP4E4OPI6QE.jpg",
        resumen: "Las principales ensambladoras reducirán su producción en un 15% este trimestre.",
        fecha: "Hace 1 día",
        tipo: "internacional",
        categoria: "tecnología"
    },
    {
        id: 15,
        titulo: "Rescatan arrecife de coral en peligro",
        imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXWkDg6it3WdGs_GpzzVydTWu-46AxMxIUFw&s",
        resumen: "Biólogos marinos lograron recuperar el 60% de un ecosistema dañado por contaminación.",
        fecha: "Hace 5 días",
        tipo: "nacional",
        categoria: "medio-ambiente"
    },
    {
        id: 16,
        titulo: "Tensión diplomática por disputa territorial",
        imagen: "https://cards.algoreducation.com/_next/image?url=https%3A%2F%2Ffiles.algoreducation.com%2Fproduction-ts%2F__S3__5fbc78d3-dfcd-487f-8047-58ffe1650521&w=3840&q=75",
        resumen: "Dos países vecinos retiraron a sus embajadores tras fracasar negociaciones.",
        fecha: "Hace 12 horas",
        tipo: "internacional",
        categoria: "política"
    },
    {
        id: 17,
        titulo: "App local gana premio internacional de innovación",
        imagen: "https://portal.andina.pe/EDPfotografia/Thumbnail/2015/01/28/000279467W.jpg",
        resumen: "La plataforma de educación digital fue reconocida por su impacto social.",
        fecha: "Ayer",
        tipo: "nacional",
        categoria: "tecnología"
    },
    {
        id: 18,
        titulo: "Celebración del Día del Patrimonio Cultural",
        imagen: "https://cdn.www.gob.pe/uploads/document/file/345453/standard_Ministerio_de_Cultura_celebr%C3%B3_D%C3%ADa_Internacional_de_los_Monumentos_y_Sitios20190725-6152-2v06gf.png",
        resumen: "Museos y sitios históricos tendrán entrada gratuita este fin de semana.",
        fecha: "Hace 1 día",
        tipo: "nacional",
        categoria: "cultura"
    },
    {
        id: 19,
        titulo: "Gigante tecnológico lanza nueva inteligencia artificial",
        imagen: "https://wordpress-bucket.nyc3.cdn.digitaloceanspaces.com/2025/01/image-2025-01-27T210116.055.jpg",
        resumen: "El sistema puede traducir en tiempo real 100 idiomas con precisión del 99%.",
        fecha: "Hace 3 horas",
        tipo: "internacional",
        categoria: "tecnología"
    },
    {
        id: 20,
        titulo: "Proyecto de tren rápido avanza a fase final",
        imagen: "https://peruconstruye.net/wp-content/uploads/2024/05/7-1.jpg",
        resumen: "Conectaría 3 ciudades principales en un tiempo récord de 90 minutos.",
        fecha: "Hace 6 días",
        tipo: "nacional",
        categoria: "infraestructura"
    }
];


let noticiasCompletas = [...noticias].filter(noticia => noticia.tipo === "internacional");
const noticiasPorPagina = 8;
let paginaActual = 1;
let noticiasFiltradas = [...noticiasCompletas];
let enModoBusqueda = false;

// Función para normalizar texto (eliminar tildes)
function normalizarTexto(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

// Función para mostrar noticias
function mostrarNoticias() {
    const newsContainer = document.getElementById('news-container');
    newsContainer.innerHTML = '';

    let noticiasAMostrar = [];

    if (enModoBusqueda) {
        if (noticiasFiltradas.length > noticiasPorPagina) {
            const inicio = (paginaActual - 1) * noticiasPorPagina;
            const fin = inicio + noticiasPorPagina;
            noticiasAMostrar = noticiasFiltradas.slice(inicio, fin);
        } else {
            noticiasAMostrar = noticiasFiltradas;
        }
    } else {
        const inicio = (paginaActual - 1) * noticiasPorPagina;
        const fin = inicio + noticiasPorPagina;
        noticiasAMostrar = noticiasCompletas.slice(inicio, fin);
    }

    if (noticiasAMostrar.length === 0) {
        newsContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <h3>No se encontraron noticias nacionales</h3>
            </div>
        `;
    } else {
        noticiasAMostrar.forEach(noticia => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 mb-4';

            col.innerHTML = `
                <div class="news">
                    <div class="imagen">
                        <img src="${noticia.imagen}" alt="${noticia.titulo}">
                    </div>
                    <div class="contenido-noticia">
                        <div class="contenido-etiqueta mb-2">
                                <span class="categoria ${noticia.tipo}">${noticia.tipo}</span>
                                <span class="categoria ${noticia.categoria}">${noticia.categoria}</span>
                            </div>
                        <h3>${noticia.titulo}</h3>
                        <p>${noticia.resumen}</p>
                        <div class="metas mb-1">
                            <span><i class="far fa-clock"></i> ${noticia.fecha}</span>
                            <button class="vermas">Ver más</button>
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

    // Determinar el total de páginas
    let totalPaginas = 1;

    if (enModoBusqueda) {
        // En búsqueda: paginar solo si hay más de 8 resultados
        if (noticiasFiltradas.length > noticiasPorPagina) {
            totalPaginas = Math.ceil(noticiasFiltradas.length / noticiasPorPagina);
            pagination.style.display = 'flex';
        } else {
            pagination.style.display = 'none';
            return;
        }
    } else {
        // Modo normal - usar noticiasCompletas en lugar de noticias
        totalPaginas = Math.ceil(noticiasCompletas.length / noticiasPorPagina);
        pagination.style.display = 'flex';
    }

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
    for (let i = 1; i <= totalPaginas; i++) {
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

// Función para buscar noticias (CORREGIDA)
function buscarNoticias() {
    const textoBusqueda = normalizarTexto(document.getElementById('contenido').value.trim());

    if (textoBusqueda === '') {
        // Salir del modo búsqueda
        enModoBusqueda = false;
        noticiasFiltradas = [...noticiasCompletas]; // Restaurar noticias nacionales
    } else {
        // Entrar en modo búsqueda - filtrar solo entre noticias nacionales
        enModoBusqueda = true;
        noticiasFiltradas = noticiasCompletas.filter(noticia => {
            const tituloNormalizado = normalizarTexto(noticia.titulo);
            const resumenNormalizado = normalizarTexto(noticia.resumen);
            return tituloNormalizado.includes(textoBusqueda) ||
                resumenNormalizado.includes(textoBusqueda);
        });
    }

    paginaActual = 1; // Resetear a la primera página
    mostrarNoticias();
}

// Event listeners
document.getElementById('buscar').addEventListener('click', buscarNoticias);

document.getElementById('contenido').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        buscarNoticias();
    }
});

// Mostrar noticias al cargar la página
mostrarNoticias();

// Menú responsive (mantenido igual)
document.querySelector(".fas").addEventListener("click", () => {
    document.querySelector(".nav-links").classList.toggle("active");
});

document.querySelector(".fas").addEventListener("click", () => {

    document.querySelector(".nav-links").classList.toggle("active");
});
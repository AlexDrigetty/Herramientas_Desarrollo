<?php include 'navar.php' ?>
<?php
include("../Admin/checkout_admin.php");
include("../bd/conexion.php");

if ($admin_true) {
    include '../Admin/admin_navbar.php';
}

// 1. Obtener noticias nacionales de la base de datos
$sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido, 
               c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
        ORDER BY n.fecha_publicacion DESC";

$noticias_nacionales = $conn->query($sql);

// Preparar noticias nacionales para JSON
$noticias_nacionales_array = [];
while ($noticia = $noticias_nacionales->fetch_assoc()) {
    $noticias_nacionales_array[] = [
        'id' => 'local-' . $noticia['id'],
        'titulo' => $noticia['titulo'],
        'imagen' => '../imagenes/' . $noticia['imagen_portada'],
        'resumen' => $noticia['resumen'],
        'contenido' => $noticia['contenido'],
        'fecha' => $noticia['fecha_publicacion'],
        'tipo' => 'nacional',
        'categoria' => $noticia['categoria_nombre'],
        'categoriaOriginal' => strtolower(str_replace(' ', '', $noticia['categoria_nombre'])),
        'enlace' => 'noticia_completa.php?id=' . $noticia['id'],
        'esLocal' => true,
        'autor' => $noticia['autor_nombre'] . ' ' . $noticia['autor_apellido'],
        'colorCategoria' => $noticia['categoria_color']
    ];
}

// 2. Obtener categorías disponibles para los filtros
$sql_categorias = "SELECT DISTINCT c.nombre 
                   FROM categorias c 
                   JOIN noticias n ON c.id = n.categoria_id 
                   WHERE n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
                   ORDER BY c.nombre";
$categorias_db = $conn->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/noticias.css">
    <link rel="stylesheet" href="../Css/inicio.css">
    <style>
        /* Estilos para las categorías dinámicas */
        .categoria.nacional { background-color: #3498db; }
        .categoria.internacional { background-color: #e74c3c; }
        .categoria.política { background-color: #9b59b6; }
        .categoria.economía { background-color: #f39c12; }
        .categoria.salud { background-color: #2ecc71; }
        .categoria.educación { background-color: #1abc9c; }
        .categoria.deportes { background-color: #e67e22; }
        .categoria.tecnología { background-color: #34495e; }
        .categoria.general { background-color: #7f8c8d; }
    </style>
</head>
<body>
    <section class="header-section texto_categoria">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fas fa-newspaper me-2"></i> NOTICIAS POR CATEGORÍAS</h1>
                    
                    <div class="search w-100 py-2 d-flex justify-content-center gap-1">
                        <input type="text" id="contenido" class="w-50 border-1 rounded-1 p-2"
                            placeholder="Comenzar Búsqueda">
                        <button id="buscar" class="rounded-1 border-1 px-5 py-2">Buscar</button>
                    </div>

                    <div class="filters w-100 mt-4">
                        <div class="row container-categoria">
                            <div class="col-md-4 mb-3">
                                <label for="category-filter" class="form-label">Categoría:</label>
                                <select id="category-filter" class="form-select custom-select">
                                    <option value="">Todas las categorías</option>
                                    <?php while($categoria = $categorias_db->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($categoria['nombre']) ?>">
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                    <!-- Categorías para noticias internacionales -->
                                    <option value="tecnología">Tecnología</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
    
                            <div class="col-md-4 mb-3">
                                <label for="date-filter" class="form-label">Fecha de emisión:</label>
                                <select id="date-filter" class="form-select custom-select">
                                    <option value="">Cualquier fecha</option>
                                    <option value="hoy">Hoy</option>
                                    <option value="ayer">Ayer</option>
                                    <option value="ultima-semana">Última semana</option>
                                    <option value="ultimo-mes">Último mes</option>
                                </select>
                            </div>
    
                            <div class="col-md-4 mb-3">
                                <label for="type-filter" class="form-label">Tipo de noticia:</label>
                                <select id="type-filter" class="form-select custom-select">
                                    <option value="">Todos los tipos</option>
                                    <option value="nacional">Nacional</option>
                                    <option value="internacional">Internacional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row news-container" id="news-container">
            <!-- Las noticias se cargarán aquí dinámicamente -->
        </div>

        <div class="row">
            <div class="pagination-container mb-5">
                <ul class="pagination" id="pagination">
                    <!-- La paginación se generará dinámicamente -->
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    
    <script>
        // Configuración de la API de noticias
        const configAPI = {
            apiKey: '5bdd0c4372a34d718d9ef84931150e53', // Reemplaza con tu API key válida
            categorias: {
                'política': {
                    query: 'política OR gobierno OR presidente OR congreso OR elecciones',
                    claseCSS: 'política'
                },
                'economía': {
                    query: 'economía OR economia OR mercado OR bolsa OR finanzas',
                    claseCSS: 'economía'
                },
                'salud': {
                    query: 'salud OR medicina OR hospital OR médico OR vacuna',
                    claseCSS: 'salud'
                },
                'educación': {
                    query: 'educación OR educacion OR escuela OR universidad OR estudiantes',
                    claseCSS: 'educación'
                },
                'deportes': {
                    query: 'fútbol OR futbol OR deportes OR "liga española" OR champions',
                    claseCSS: 'deportes'
                },
                'tecnología': {
                    query: 'tecnología OR tecnologia OR "inteligencia artificial" OR robot OR digital',
                    claseCSS: 'tecnología'
                },
                'general': {
                    query: 'mundo OR internacional OR actualidad',
                    claseCSS: 'general'
                }
            },
            imagenDefault: 'https://via.placeholder.com/300x200?text=Noticia+Global'
        };

        // Pasar las noticias nacionales de PHP a JavaScript
        const noticiasNacionales = <?php echo json_encode($noticias_nacionales_array); ?>;
        
        // Variables globales
        const noticiasPorPagina = 8;
        let paginaActual = 1;
        let noticiasFiltradas = [];
        let noticiasCompletas = [...noticiasNacionales]; // Comienza con noticias nacionales
        let filtrosActivos = {
            categoria: '',
            fecha: '',
            tipo: '',
            texto: ''
        };

        // Elementos del DOM
        const categoryFilter = document.getElementById('category-filter');
        const dateFilter = document.getElementById('date-filter');
        const typeFilter = document.getElementById('type-filter');
        const searchInput = document.getElementById('contenido');
        const searchButton = document.getElementById('buscar');

        // Inicialización
        document.addEventListener('DOMContentLoaded', async function () {
            // Mostrar noticias nacionales primero
            procesarFechasNoticias();
            mostrarNoticias();
            
            // Cargar noticias internacionales en segundo plano
            await cargarNoticiasInternacionales();
            
            // Configurar eventos
            configurarEventListeners();
        });

        // Función para cargar noticias internacionales
        async function cargarNoticiasInternacionales() {
            try {
                for (const [categoria, config] of Object.entries(configAPI.categorias)) {
                    const url = `https://newsapi.org/v2/everything?q=${encodeURIComponent(config.query)}&language=es&pageSize=5&apiKey=${configAPI.apiKey}`;
                    const response = await fetch(url);
                    
                    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                    
                    const data = await response.json();
                    
                    if (data.articles?.length > 0) {
                        const noticiasAPI = data.articles.map((articulo, index) => ({
                            id: `api-${categoria}-${index}`,
                            titulo: articulo.title || 'Sin título',
                            imagen: articulo.urlToImage || configAPI.imagenDefault,
                            resumen: articulo.description || 'Sin descripción',
                            contenido: articulo.content || '',
                            fecha: articulo.publishedAt,
                            tipo: 'internacional',
                            categoria: categoria,
                            categoriaOriginal: categoria.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, ''),
                            enlace: articulo.url || '#',
                            esLocal: false,
                            autor: articulo.author || 'Fuente internacional',
                            colorCategoria: ''
                        }));
                        
                        noticiasCompletas = [...noticiasCompletas, ...noticiasAPI];
                    }
                }
                
                // Ordenar todas las noticias por fecha (más recientes primero)
                noticiasCompletas.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
                procesarFechasNoticias();
                noticiasFiltradas = [...noticiasCompletas];
                mostrarNoticias();
            } catch (error) {
                console.error('Error al cargar noticias internacionales:', error);
                // Mostrar solo noticias nacionales si hay error
                noticiasFiltradas = [...noticiasNacionales];
                mostrarNoticias();
            }
        }

        // Función para procesar fechas a formato relativo
        function procesarFechasNoticias() {
            noticiasCompletas.forEach(noticia => {
                noticia.fechaRelativa = getFechaRelativa(noticia.fecha);
            });
        }

        // Función para obtener fecha relativa
        function getFechaRelativa(fechaString) {
            if (!fechaString) return 'Fecha desconocida';
            
            const fechaPub = new Date(fechaString);
            const ahora = new Date();
            const diferencia = ahora - fechaPub;
            
            const segundos = Math.floor(diferencia / 1000);
            const minutos = Math.floor(segundos / 60);
            const horas = Math.floor(minutos / 60);
            const dias = Math.floor(horas / 24);
            
            if (dias > 0) return `Hace ${dias} ${dias === 1 ? 'día' : 'días'}`;
            if (horas > 0) return `Hace ${horas} ${horas === 1 ? 'hora' : 'horas'}`;
            if (minutos > 0) return `Hace ${minutos} ${minutos === 1 ? 'minuto' : 'minutos'}`;
            return 'Hace unos momentos';
        }

        // Configurar event listeners
        function configurarEventListeners() {
            categoryFilter.addEventListener('change', aplicarFiltros);
            dateFilter.addEventListener('change', aplicarFiltros);
            typeFilter.addEventListener('change', aplicarFiltros);
            searchButton.addEventListener('click', buscarNoticias);
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') buscarNoticias();
            });
        }

        // Función para normalizar texto (eliminar tildes)
        function normalizarTexto(texto) {
            return texto.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        // Función para aplicar filtros
        function aplicarFiltros() {
            // Capturar valores de los filtros
            filtrosActivos = {
                categoria: categoryFilter.value,
                fecha: dateFilter.value,
                tipo: typeFilter.value,
                texto: filtrosActivos.texto
            };

            // Obtener fecha/hora actual para comparaciones
            const ahora = new Date();
            
            // Aplicar todos los filtros
            noticiasFiltradas = noticiasCompletas.filter(noticia => {
                // Filtro por categoría
                if (filtrosActivos.categoria && 
                    normalizarTexto(noticia.categoria) !== normalizarTexto(filtrosActivos.categoria)) {
                    return false;
                }

                // Filtro por tipo
                if (filtrosActivos.tipo && noticia.tipo !== filtrosActivos.tipo) {
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
                            if (diferenciaHoras < 48 || diferenciaHoras >= 168) return false;
                            break;
                        case 'ultimo-mes':
                            if (diferenciaHoras < 168 || diferenciaHoras >= 720) return false;
                            break;
                    }
                }

                // Filtro por texto
                if (filtrosActivos.texto) {
                    const textoBusqueda = normalizarTexto(filtrosActivos.texto);
                    const tituloNormalizado = normalizarTexto(noticia.titulo);
                    const resumenNormalizado = normalizarTexto(noticia.resumen);
                    const contenidoNormalizado = normalizarTexto(noticia.contenido || '');

                    if (!tituloNormalizado.includes(textoBusqueda) &&
                        !resumenNormalizado.includes(textoBusqueda) &&
                        !contenidoNormalizado.includes(textoBusqueda)) {
                        return false;
                    }
                }

                return true;
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
                        <p>Intenta con otros criterios de búsqueda.</p>
                    </div>
                `;
            } else {
                noticiasPagina.forEach(noticia => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4 col-lg-3 mb-4';

                    // Determinar clase CSS para la categoría
                    const claseCategoria = noticia.categoriaOriginal || 
                                          noticia.categoria.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '');

                    col.innerHTML = `
                        <div class="news">
                            <div class="imagen">
                                <img src="${noticia.imagen}" alt="${noticia.titulo}" 
                                     onerror="this.src='${configAPI.imagenDefault}'">
                            </div>
                            <div class="contenido-noticia">
                                <div class="contenido-etiqueta mb-2">
                                    <span class="categoria ${noticia.tipo}">${noticia.tipo}</span>
                                    <span class="categoria ${claseCategoria}" 
                                          style="${noticia.colorCategoria ? 'background-color: ' + noticia.colorCategoria : ''}">
                                        ${noticia.categoria}
                                    </span>
                                </div>
                                <h3>${noticia.titulo}</h3>
                                <p>${noticia.resumen}</p>
                                <div class="metas mb-1">
                                    <span><i class="far fa-clock"></i> ${noticia.fechaRelativa || noticia.fecha}</span>
                                    <a href="${noticia.enlace}" ${noticia.esLocal ? '' : 'target="_blank"'} 
                                       class="vermas">Ver más</a>
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
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
            });
            pagination.appendChild(liAnterior);

            // Números de página
            const maxPaginasVisibles = 5;
            let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxPaginasVisibles / 2));
            let finPaginas = Math.min(totalPaginas, inicioPaginas + maxPaginasVisibles - 1);

            // Ajustar si no hay suficientes páginas visibles
            if (finPaginas - inicioPaginas + 1 < maxPaginasVisibles) {
                inicioPaginas = Math.max(1, finPaginas - maxPaginasVisibles + 1);
            }

            // Primera página y elipsis si es necesario
            if (inicioPaginas > 1) {
                const liPrimera = document.createElement('li');
                liPrimera.className = 'page-item';
                liPrimera.innerHTML = `<a class="page-link" href="#">1</a>`;
                liPrimera.addEventListener('click', (e) => {
                    e.preventDefault();
                    paginaActual = 1;
                    mostrarNoticias();
                    window.scrollTo({top: 0, behavior: 'smooth'});
                });
                pagination.appendChild(liPrimera);

                if (inicioPaginas > 2) {
                    const liEllipsis = document.createElement('li');
                    liEllipsis.className = 'page-item disabled';
                    liEllipsis.innerHTML = `<span class="page-link">...</span>`;
                    pagination.appendChild(liEllipsis);
                }
            }

            // Páginas visibles
            for (let i = inicioPaginas; i <= finPaginas; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', (e) => {
                    e.preventDefault();
                    paginaActual = i;
                    mostrarNoticias();
                    window.scrollTo({top: 0, behavior: 'smooth'});
                });
                pagination.appendChild(li);
            }

            // Última página y elipsis si es necesario
            if (finPaginas < totalPaginas) {
                if (finPaginas < totalPaginas - 1) {
                    const liEllipsis = document.createElement('li');
                    liEllipsis.className = 'page-item disabled';
                    liEllipsis.innerHTML = `<span class="page-link">...</span>`;
                    pagination.appendChild(liEllipsis);
                }

                const liUltima = document.createElement('li');
                liUltima.className = 'page-item';
                liUltima.innerHTML = `<a class="page-link" href="#">${totalPaginas}</a>`;
                liUltima.addEventListener('click', (e) => {
                    e.preventDefault();
                    paginaActual = totalPaginas;
                    mostrarNoticias();
                    window.scrollTo({top: 0, behavior: 'smooth'});
                });
                pagination.appendChild(liUltima);
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
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
            });
            pagination.appendChild(liSiguiente);
        }
    </script>
    <?php include 'footer.php' ?>
</body>
</html>
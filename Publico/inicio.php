<?php include 'navar.php' ?>
<?php
include("../Admin/checkout_admin.php");
include("../bd/conexion.php");

if ($admin_true) {
    include '../Admin/admin_navbar.php';
}

// Obtener noticias locales publicadas (nacionales e internacionales)
$sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido, 
               c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
        ORDER BY n.fecha_publicacion DESC 
        LIMIT 20";

$noticias_locales = $conn->query($sql);

// Preparar noticias locales para JavaScript
$noticias_locales_para_js = [];
while ($noticia = $noticias_locales->fetch_assoc()) {
    $noticias_locales_para_js[] = [
        'id' => $noticia['id'],
        'titulo' => $noticia['titulo'],
        'imagen_portada' => '../Imagenes/' . $noticia['imagen_portada'],
        'resumen' => $noticia['resumen'],
        'contenido' => $noticia['contenido'],
        'fecha_publicacion' => $noticia['fecha_publicacion'],
        'tipo_noticia' => $noticia['tipo_noticia'],
        'categoria_nombre' => $noticia['categoria_nombre'],
        'categoria_color' => $noticia['categoria_color'],
        'slug' => $noticia['slug'],
        'autor' => $noticia['autor_nombre'] . ' ' . $noticia['autor_apellido'],
        'esLocal' => true,
        'enlace' => 'noticia_completa.php?id=' . $noticia['id'] // Añadido para el botón ver más
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/inicio.css">
</head>

<body>
    <main class="mb-5">
        <div class="container">
            <!-- Carrusel de noticias -->
            <div class="row">
                <div class="col-12 ultimo mt-4">
                    <div id="news-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carousel-inner">
                            <!-- Contenido dinámico -->
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#news-carousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#news-carousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Últimas noticias -->
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="sesiones">
                            <h4 class="sesion mb-4">Últimas noticias</h4>
                            <div class="botones">
                                <button class="btn active" data-filtro="all">Todas</button>
                                <button class="btn" data-filtro="nacional">Nacionales</button>
                                <button class="btn" data-filtro="internacional">Internacionales</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <!-- Listado de noticias -->
                    <div class="col-12 col-lg-9">
                        <div class="row fila" id="news-container">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>

                    <!-- Noticias destacadas -->
                    <div class="col-12 col-lg-3 mt-4 mt-lg-0">
                        <div class="destacados">
                            <div class="row">
                                <div class="col-12 noticias">
                                    <h4>Noticias Destacadas</h4>
                                </div>
                            </div>
                            <div class="row" id="featured-news">
                                <!-- Contenido dinámico -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Noticias locales desde PHP
        const noticiasLocales = <?php echo json_encode($noticias_locales_para_js); ?>;
        let noticiasCompletas = [...noticiasLocales];
        let noticiasFiltradas = [];
        let enModoBusqueda = false;

        // Configuración para la API de noticias
        const configCategorias = {
            'general': {
                nombreES: 'General',
                claseCSS: 'general',
                query: 'mundo OR internacional'
            },
            'politica': {
                nombreES: 'Política',
                claseCSS: 'política',
                query: 'política OR politica OR gobierno'
            },
            'economia': {
                nombreES: 'Economía',
                claseCSS: 'economía',
                query: 'economía OR economia OR mercado'
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar carrusel con noticias locales
            cargarCarrusel(noticiasLocales.slice(0, 8));

            // Cargar últimas noticias (locales + API)
            cargarNoticiasAPI().then(() => {
                cargarUltimasNoticias('all');
            });

            // Event listeners para filtros
            document.querySelectorAll('.botones .btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelector('.botones .btn.active').classList.remove('active');
                    this.classList.add('active');
                    const filtro = this.getAttribute('data-filtro');
                    cargarUltimasNoticias(filtro);
                });
            });
        });

        async function cargarNoticiasAPI() {
            try {
                // Obtener noticias de cada categoría
                const categorias = Object.keys(configCategorias);
                for (const categoria of categorias) {
                    const noticias = await obtenerNoticiasAPI(categoria);
                    noticiasCompletas = [...noticiasCompletas, ...noticias];
                }

                // Ordenar todas por fecha
                noticiasCompletas.sort((a, b) =>
                    new Date(b.fecha_publicacion || b.publishedAt) - new Date(a.fecha_publicacion || a.publishedAt)
                );
            } catch (error) {
                console.error('Error al cargar noticias API:', error);
            }
        }

        async function obtenerNoticiasAPI(categoria) {
            const apiKey = '5bdd0c4372a34d718d9ef84931150e53';
            const query = configCategorias[categoria].query;
            const url = `https://newsapi.org/v2/everything?q=${encodeURIComponent(query)}&language=es&pageSize=5&apiKey=${apiKey}`;

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();

                return data.articles.map(article => ({
                    id: 'api-' + Math.random().toString(36).substr(2, 9),
                    titulo: article.title,
                    resumen: article.description,
                    contenido: article.content,
                    imagen_portada: article.urlToImage || 'https://via.placeholder.com/800x500?text=Noticia',
                    fecha_publicacion: article.publishedAt,
                    tipo_noticia: 'internacional',
                    categoria_nombre: configCategorias[categoria].nombreES,
                    categoria_color: '#6c757d',
                    slug: article.url,
                    autor: article.author || 'Desconocido',
                    esLocal: false,
                    enlace: article.url // Añadido para el botón ver más
                }));
            } catch (error) {
                console.error('Error al obtener noticias API:', error);
                return [];
            }
        }

        function cargarCarrusel(noticias) {
            const carouselInner = document.getElementById('carousel-inner');
            carouselInner.innerHTML = '';

            noticias.forEach((noticia, index) => {
                const item = document.createElement('div');
                item.className = `carousel-item ${index === 0 ? 'active' : ''}`;

                item.innerHTML = `
                    <div class="carrusel_contenido">
                        <div class="imagen">
                            <img src="${noticia.imagen_portada}" alt="${noticia.titulo}">
                        </div>
                        <div class="contenido-noticia">
                            <div class="contenido-etiqueta">
                                <span class="categoria ${noticia.tipo_noticia}">${noticia.tipo_noticia === 'nacional' ? 'Nacional' : 'Internacional'}</span>
                                <span class="categoria ${noticia.categoria_nombre.toLowerCase()}">${noticia.categoria_nombre}</span>
                            </div>
                            <h3>${noticia.titulo}</h3>
                            <p>${noticia.resumen}</p>
                            <div class="metas">
                                <span><i class="far fa-clock"></i> ${calcularTiempoTranscurrido(noticia.fecha_publicacion)}</span>
                                <a href="${noticia.esLocal ? noticia.enlace : noticia.slug}" 
                                   ${noticia.esLocal ? '' : 'target="_blank"'} 
                                   class="vermas">
                                    ver más
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                carouselInner.appendChild(item);
            });

            // Cargar destacados con las mismas noticias
            cargarDestacados(noticias);
        }

        function cargarUltimasNoticias(filtro) {
            let noticiasFiltradas = noticiasCompletas;

            if (filtro !== 'all') {
                noticiasFiltradas = noticiasCompletas.filter(noticia =>
                    noticia.tipo_noticia === filtro
                );
            }

            renderizarUltimasNoticias(noticiasFiltradas.slice(0, 12));
        }

        function renderizarUltimasNoticias(noticias) {
            const newsContainer = document.getElementById('news-container');
            newsContainer.innerHTML = '';

            if (noticias.length === 0) {
                newsContainer.innerHTML = '<div class="col-12"><p class="text-center">No hay noticias disponibles</p></div>';
                return;
            }

            noticias.forEach(noticia => {
                const col = document.createElement('div');
                col.className = 'col-12 col-md-6 col-lg-4 mb-4';

                col.innerHTML = `
                    <div class="news">
                        <div class="imagen">
                            <img src="${noticia.imagen_portada}" alt="${noticia.titulo}">
                        </div>
                        <div class="contenido-noticia">
                            <div class="contenido-etiqueta">
                                <span class="categoria ${noticia.tipo_noticia}">${noticia.tipo_noticia === 'nacional' ? 'Nacional' : 'Internacional'}</span>
                                <span class="categoria ${noticia.categoria_nombre.toLowerCase()}">${noticia.categoria_nombre}</span>
                            </div>
                            <h3>${noticia.titulo}</h3>
                            <p>${noticia.resumen}</p>
                            <div class="metas">
                                <span><i class="far fa-clock"></i> ${calcularTiempoTranscurrido(noticia.fecha_publicacion)}</span>
                                <a href="${noticia.esLocal ? noticia.enlace : noticia.slug}" 
                                   ${noticia.esLocal ? '' : 'target="_blank"'} 
                                   class="vermas">
                                    ver más
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                newsContainer.appendChild(col);
            });
        }

        function cargarDestacados(noticias) {
            const featuredContainer = document.getElementById('featured-news');
            featuredContainer.innerHTML = '';

            noticias.slice(0, 8).forEach(noticia => {
                const col = document.createElement('div');
                col.className = 'col-12 mb-3 destacada-item';

                col.innerHTML = `
                    <div class="destacadas">
                        <div class="imagen">
                            <img src="${noticia.imagen_portada}" alt="${noticia.titulo}">
                        </div>
                        <div class="contenido">
                            <h6>${noticia.titulo}</h6>
                            <a style="text-decoration:none; color: #1A2340; href="${noticia.esLocal ? noticia.enlace : noticia.slug}" 
                                   ${noticia.esLocal ? '' : 'target="_blank"'} 
                                   class="vermas">
                                    ver más
                                </a>
                            <div class="metas mt-2">
                                <span><i class="far fa-clock"></i> ${calcularTiempoTranscurrido(noticia.fecha_publicacion)}</span>
                            </div>
                        </div>
                    </div>
                `;

                featuredContainer.appendChild(col);
            });
        }

        function calcularTiempoTranscurrido(fechaString) {
            if (!fechaString) return 'Fecha desconocida';

            const fechaPub = new Date(fechaString);
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php' ?>
</body>

</html>
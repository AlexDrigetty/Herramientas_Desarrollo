<?php include 'navar.php' ?>
<?php
include("../Admin/checkout_admin.php");
include("../bd/conexion.php");

if ($admin_true) {
    include '../Admin/admin_navbar.php';
}

$sql = "SELECT n.*, u.nombre as autor_nombre, u.apellido as autor_apellido, 
               c.nombre as categoria_nombre, c.color as categoria_color
        FROM noticias n
        JOIN usuarios u ON n.autor_id = u.id
        JOIN categorias c ON n.categoria_id = c.id
        WHERE n.tipo_noticia = 'internacional' 
        AND n.estado_id = (SELECT id FROM estados_noticia WHERE nombre = 'Publicado')
        ORDER BY n.fecha_publicacion DESC";

$noticias_locales = $conn->query($sql);

// Preparar noticias locales para mezclarlas con las de la API
$noticias_locales_para_js = [];
while ($noticia = $noticias_locales->fetch_assoc()) {
    $noticias_locales_para_js[] = [
        'id' => 'local-' . $noticia['id'],
        'titulo' => $noticia['titulo'],
        'imagen' => '../imagenes/' . $noticia['imagen_portada'], // Ajustado para usar imagen_portada
        'resumen' => $noticia['resumen'],
        'fecha' => $noticia['fecha_publicacion'],
        'tipo' => 'internacional',
        'categoria' => $noticia['categoria_nombre'],
        'categoriaOriginal' => strtolower(str_replace(' ', '', $noticia['categoria_nombre'])),
        'enlace' => 'noticia_completa.php?id=' . $noticia['id'],
        'esLocal' => true,
        'autor' => $noticia['autor_nombre'] . ' ' . $noticia['autor_apellido']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internacional | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/noticias.css">
    <link rel="stylesheet" href="../Css/inicio.css">
</head>

<body>

    <section class="header-section texto_internacional">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fas fa-newspaper me-2"></i> Noticias Internacionales</h1>
                    <div class="search w-100 py-2 d-flex justify-content-center gap-1">
                        <input type="text" id="contenido" class="w-50 border-1 rounded-1 p-2"
                            placeholder="Comenzar Busqueda">
                        <button id="buscar" class="rounded-1 border-1 px-5 py-2">Buscar</button>
                    </div>

                    <div class="filters w-100 mt-4">
                        <div class="row container-categoria">
                            <div class="col-md-4 mb-3">
                                <label for="category-filter" class="form-label">Categoría:</label>
                                <select id="category-filter" class="form-select custom-select">
                                    <option value="">Seleccione una categoría</option>
                                    <option value="política">Política</option>
                                    <option value="economía">Economía</option>
                                    <option value="salud">Salud</option>
                                    <option value="educación">Educación</option>
                                    <option value="deportes">Deportes</option>
                                </select>
                            </div>
    
                            <div class="col-md-4 mb-3">
                                <label for="date-filter" class="form-label">Fecha de emisión:</label>
                                <select id="date-filter" class="form-select custom-select">
                                    <option value="">Seleccione una fecha</option>
                                    <option value="hoy">Hoy</option>
                                    <option value="ayer">Ayer</option>
                                    <option value="ultima-semana">Última semana</option>
                                    <option value="ultimo-mes">Último mes</option>
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
        </div>

        <div class="row">
            <div class="pagination-container mb-5">
                <ul class="pagination" id="pagination">
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'footer.php'?>
    <script>
        const noticiasPorPagina = 8;
        const paginasAMostrar = 5;
        let paginaActual = 1;
        let noticiasFiltradas = [];
        let enModoBusqueda = false;
        let noticiasCompletas = <?php echo json_encode($noticias_locales_para_js); ?>;
        let categoriaActual = 'todas';

        const configCategorias = {
            'general': {
                nombreES: 'general',
                claseCSS: 'general',
                imagen: 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg',
                query: 'mundo'
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

        function obtenerImagen(noticia) {
            if (noticia.imagen) return noticia.imagen;
            if (noticia.urlToImage) return noticia.urlToImage;
            const categoria = noticia.categoriaOriginal ? noticia.categoriaOriginal : 'general';
            return configCategorias[categoria]?.imagen || 'https://via.placeholder.com/300x200?text=Noticia';
        }

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
                        id: 'api-' + (noticiasCompletas.length + index + 1),
                        titulo: noticia.title || 'Sin título',
                        imagen: obtenerImagen(noticia),
                        resumen: noticia.description || 'Sin descripción',
                        fecha: noticia.publishedAt,
                        tipo: "internacional",
                        categoria: configCategorias[categoria].nombreES,
                        categoriaOriginal: categoria,
                        enlace: noticia.url || '#',
                        esLocal: false
                    }));
                }
                return [];
            } catch (error) {
                console.error(`Error al obtener noticias de ${categoria}:`, error);
                return [];
            }
        }

        async function obtenerTodasLasNoticiasAPI() {
            const categorias = ['general', 'futbol', 'politica', 'salud', 'economia', 'tecnologia'];

            for (const categoria of categorias) {
                const noticias = await obtenerNoticiasPorCategoria(categoria);
                noticiasCompletas = [...noticiasCompletas, ...noticias];
            }

            // Ordenar todas las noticias por fecha (más recientes primero)
            noticiasCompletas.sort((a, b) => {
                return new Date(b.fecha) - new Date(a.fecha);
            });

            mostrarNoticias();
        }

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
                    <img src="${obtenerImagen(noticia)}" alt="${noticia.titulo}" 
                         onerror="this.src='${configCategorias[noticia.categoriaOriginal]?.imagen || 'https://marketplace.canva.com/EAFrDm3ydqw/1/0/1600w/canva-presentaci%C3%B3n-noticias-telediario-corporativo-azul-rojo-Vh4S5Wt7FD4.jpg'}'">
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
                        <span><i class="far fa-clock"></i> ${calcularTiempoTranscurrido(noticia.fecha)}</span>
                        <a href="${noticia.enlace}" ${noticia.esLocal ? '' : 'target="_blank"'} class="vermas">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
        `).join('');

            actualizarPaginacion();
        }

function actualizarPaginacion() {
            const pagination = document.getElementById('pagination');
            if (!pagination) return;

            const noticiasTotales = enModoBusqueda ? noticiasFiltradas : noticiasCompletas;
            const totalPaginas = Math.ceil(noticiasTotales.length / noticiasPorPagina);

            pagination.innerHTML = '';

            // Botón Anterior - SIEMPRE visible
            pagination.appendChild(crearBotonPaginacion(
                'Anterior',
                false, // No es activo
                () => cambiarPagina(paginaActual - 1),
                false, // No es número
                paginaActual === 1 // Deshabilitado en primera página
            ));

            // Números de página
            let inicio = Math.max(1, paginaActual - Math.floor(paginasAMostrar / 2));
            let fin = Math.min(totalPaginas, paginaActual + Math.floor(paginasAMostrar / 2));

            if (fin - inicio + 1 < paginasAMostrar) {
                inicio = Math.max(1, fin - paginasAMostrar + 1);
            }

            for (let i = inicio; i <= fin; i++) {
                pagination.appendChild(crearBotonPaginacion(
                    i,
                    i === paginaActual, // Activo si es la página actual
                    () => cambiarPagina(i),
                    true // Es número
                ));
            }

            // Botón Siguiente - SIEMPRE visible
            pagination.appendChild(crearBotonPaginacion(
                'Siguiente',
                false, // No es activo
                () => cambiarPagina(paginaActual + 1),
                false, // No es número
                paginaActual === totalPaginas // Deshabilitado en última página
            ));
        }

        // Función para crear botones de paginación (MODIFICADA)
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
                    onClick();
                });
            }
            
            li.appendChild(link);
            return li;
        }

        function cambiarPagina(nuevaPagina) {
            paginaActual = nuevaPagina;
            mostrarNoticias();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

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

        function normalizarTexto(texto) {
            return texto.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

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
            obtenerTodasLasNoticiasAPI();

            document.getElementById('buscar').addEventListener('click', buscarNoticias);
            document.getElementById('contenido').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') buscarNoticias();
            });
        });
    </script>
</body>

</html>
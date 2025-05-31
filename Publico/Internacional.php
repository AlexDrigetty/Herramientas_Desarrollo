<?php include 'navar.php'?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias Nacionales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/internacional.css">
</head>
<body>
    <section class="header-section ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fas fa-newspaper me-2"></i> Noticias Nacionales</h1>
                    <div class="search w-100 py-2 d-flex justify-content-center gap-1">
                        <input type="text" id="contenido" class="w-50 border-1 rounded-1 p-2" placeholder="Comenzar Busqueda">
                        <button id="buscar" class="rounded-1 border-1 px-5 py-2" >Buscar</button>
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
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item active"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php include 'footer.php'?>
    <script>
        const noticias = [
    {
        id: 1,
        titulo: "EE.UU. anuncia nuevas sanciones económicas contra Rusia por conflicto en Ucrania",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies tincidunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies tincidunt.",
        fecha: "Hace 3 horas"
    },
    {
        id: 2,
        titulo: "Reino Unido: Nueva primera ministra anuncia plan de emergencia para crisis energética",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.",
        fecha: "Hace 5 horas"
    },
    {
        id: 3,
        titulo: "Francia enfrenta nueva ola de protestas por aumento en costo de vida",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud.",
        fecha: "Hace 7 horas"
    },
    {
        id: 4,
        titulo: "Alemania aprueba paquete de €65 mil millones para proteger a consumidores",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit.",
        fecha: "Ayer"
    },
    {
        id: 5,
        titulo: "Italia elige a Giorgia Meloni como primera mujer primera ministra",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Excepteur sint occaecat cupidatat non. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Excepteur sint occaecat cupidatat non.",
        fecha: "Ayer"
    },
    {
        id: 6,
        titulo: "España: Grave sequía afecta al 60% del territorio nacional",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquip ex ea commodo consequat.",
        fecha: "Hace 2 días"
    },
    {
        id: 7,
        titulo: "Canadá anuncia plan para recibir 500,000 inmigrantes anuales para 2025",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sunt in culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sunt in culpa qui officia deserunt.",
        fecha: "Hace 2 días"
    },
    {
        id: 8,
        titulo: "México y Argentina refuerzan lazos comerciales con nuevo acuerdo bilateral",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit in voluptate. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit in voluptate.",
        fecha: "Hace 3 días"
    },
    {
        id: 6,
        titulo: "España: Grave sequía afecta al 60% del territorio nacional",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquip ex ea commodo consequat.",
        fecha: "Hace 2 días"
    },
    {
        id: 7,
        titulo: "Canadá anuncia plan para recibir 500,000 inmigrantes anuales para 2025",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sunt in culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sunt in culpa qui officia deserunt.",
        fecha: "Hace 2 días"
    },
    {
        id: 8,
        titulo: "México y Argentina refuerzan lazos comerciales con nuevo acuerdo bilateral",
        imagen: "https://thumbs.dreamstime.com/b/icono-del-vector-de-las-noticias-aislado-en-el-fondo-blanco-92670938.jpg",
        resumen: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit in voluptate. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis aute irure dolor in reprehenderit in voluptate.",
        fecha: "Hace 3 días"
    }
    ];
        const noticiasPorPagina = 8;
        let paginaActual = 1;

        // Función para mostrar noticias
        function mostrarNoticias(pagina) {
            const inicio = (pagina - 1) * noticiasPorPagina;
            const fin = inicio + noticiasPorPagina;
            const noticiasPagina = noticias.slice(inicio, fin);
            
            const newsContainer = document.getElementById('news-container');
            newsContainer.innerHTML = '';
            
            noticiasPagina.forEach(noticia => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 mb-4';
                
                col.innerHTML = `
                    <div class="news">
                        <div class="imagen">
                            <img src="${noticia.imagen}" alt="${noticia.titulo}">
                        </div>
                        <div class="contenido-noticia">
                            <span class="categoria internacional mb-2">Internacionales</span>
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
            
            actualizarPaginacion();
        }

        // Función para actualizar la paginación
        function actualizarPaginacion() {
            const totalPaginas = Math.ceil(noticias.length / noticiasPorPagina);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            
            // Botón Anterior
            const liAnterior = document.createElement('li');
            liAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
            liAnterior.innerHTML = `<a class="page-link" href="#" tabindex="-1">Anterior</a>`;
            liAnterior.addEventListener('click', (e) => {
                e.preventDefault();
                if (paginaActual > 1) {
                    paginaActual--;
                    mostrarNoticias(paginaActual);
                }
            });
            pagination.appendChild(liAnterior);
            
            for (let i = 1; i <= totalPaginas; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', (e) => {
                    e.preventDefault();
                    paginaActual = i;
                    mostrarNoticias(paginaActual);
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
                    mostrarNoticias(paginaActual);
                }
            });
            pagination.appendChild(liSiguiente);
        }
        mostrarNoticias(paginaActual);
    </script>
    <script>
        document.querySelector(".fas").addEventListener("click", () => {

            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>
</body>
</html>
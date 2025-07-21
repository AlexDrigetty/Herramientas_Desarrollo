document.addEventListener("DOMContentLoaded", function () {
    //Ordenar noticias por fecha (más recientes primero)
    const noticiasOrdenadas = [...noticias].sort((a, b) => {
        return new Date(b.fecha) - new Date(a.fecha);
    });

    //Configurar carrusel con las 6 noticias más recientes
    const carruselInner = document.getElementById('carousel-inner');
    const noticiasCarrusel = noticiasOrdenadas.slice(0, 6);

    noticiasCarrusel.forEach((noticia, index) => {
        const carruselItem = document.createElement('div');
        carruselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;

        carruselItem.innerHTML = `
            <div class="carrusel_contenido">
                <div class="imagen mb-4">
                    <img src="${noticia.imagen}" alt="${noticia.titulo}">
                </div>
                <div class="contenido-noticia">
                    <span class="categoria ${noticia.tipo}">${noticia.tipo}</span>
                    <h3 class="py-3">${noticia.titulo}</h3>
                    <p>${noticia.resumen}</p>
                    <div class="metas mb-1">
                        <span><i class="far fa-clock"></i> ${noticia.fecha}</span>
                        <button class="vermas">Ver más</button>
                    </div>
                </div>
            </div>
        `;

        carruselInner.appendChild(carruselItem);
    });

    //Cargar últimas noticias
    const newsContainer = document.getElementById('news-container');
    noticiasOrdenadas.slice(6).forEach(noticia => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-lg-4 columna mb-3';
        col.setAttribute('data-categoria', noticia.tipo);

        col.innerHTML = `
            <div class="news">
                <div class="imagen mb-2">
                    <img src="${noticia.imagen}" alt="${noticia.titulo}">
                </div>
                <div class="contenido-noticia">
                        <div class="contenido-etiqueta mb-2">
                            <span class="categoria ${noticia.tipo}">${noticia.tipo}</span>
                            <span class="categoria ${noticia.categoria}">${noticia.categoria}</span>
                        </div>
                    <h3>${noticia.titulo}</h3>
                    <p>${noticia.resumen}</p>
                    <div class="metas">
                        <span><i class="far fa-clock"></i> ${noticia.fecha}</span>
                        <button class="vermas">Ver más</button>
                    </div>
                </div>
            </div>
        `;

        newsContainer.appendChild(col);
    });

    // 3. Cargar noticias destacadas (las primeras 8 noticias)
    const featuredNews = document.getElementById('featured-news');
    const noticiasDestacadas = noticiasOrdenadas.slice(0, 8); // Tomamos las primeras 8 noticias

    noticiasDestacadas.forEach(noticia => {
        const destacada = document.createElement('div');
        destacada.className = 'col-12 mb-3 destacada-item'; // Añadí mb-3 para margen inferior

        destacada.innerHTML = `
            <div class="destacadas d-flex">
                <div class="imagen me-2" style="width: 100px; height: 70px; overflow: hidden;">
                    <img src="${noticia.imagen}" alt="${noticia.titulo}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="contenido" style="flex: 1;">
                    <div class="titulo">
                        <h6 style="font-size: 0.9rem; margin: 0;">${noticia.titulo}</h6>
                    </div>
                    <div class="metas">
                        <span style="font-size: 0.7rem;"><i class="far fa-clock"></i> ${noticia.fecha}</span>
                    </div>
                </div>
            </div>
        `;

        featuredNews.appendChild(destacada);
    });

    // Filtrado de noticias
    document.querySelectorAll(".botones .btn").forEach(button => {
        button.addEventListener("click", () => {
            document.querySelectorAll(".botones .btn").forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            const filtro = button.getAttribute("data-filtro");

            document.querySelectorAll(".columna").forEach(caja => {
                if (filtro == "all") {
                    caja.style.display = "block";
                } else {
                    if (caja.getAttribute("data-categoria") == filtro) {
                        caja.style.display = "block";
                    } else {
                        caja.style.display = "none";
                    }
                }
            });
        });
    });
});

// Menú responsive
document.querySelector(".fas").addEventListener("click", () => {
    document.querySelector(".nav-links").classList.toggle("active");
});
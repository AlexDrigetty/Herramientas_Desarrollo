<?php include 'navar.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias-Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/nav.css">
    <link rel="stylesheet" href="../Css/noticias.css">
</head>
<body>
    <section class="header-section texto_categoria">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fas fa-newspaper me-2"></i> CATEGORIAS</h1>
                    
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
    
                            <div class="col-md-4 mb-3">
                                <label for="type-filter" class="form-label">Tipo de noticia:</label>
                                <select id="type-filter" class="form-select custom-select">
                                    <option value="">Seleccione tipo</option>
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
    <script src="../js/Categoria.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <?php include 'footer.php'?>
</body>

</html>
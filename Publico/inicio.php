<?php include 'navar.php'?>
<?php
include("../Admin/checkout_admin.php");

if($admin_true) {
    include '../Admin/admin_navbar.php'; // Ruta relativa correcta
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
            <div class="row">
                <div class="col-12 ultimo mt-4">
                    <div id="news-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carousel-inner">
                            <!-- Contenedor de noticiass -->
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
                            <!-- Noticias se cargarán aquí -->
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
                                <!-- Noticias destacadas se cargarán aquí -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/Inicio.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'?>
</body>
</html>
<?php include 'navar.php'?><?php
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
    <script src="../js/Internacional.js"></script>
</body>

</html>
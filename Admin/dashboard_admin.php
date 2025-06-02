<?php include 'admin_navbar.php';?>
<?php 
session_start();

// Redirige si no está logueado o no es ADMIN (en mayúsculas)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    header("Location: ../Publico/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>
    <main>
        <?php include 'slider.php';?>
        </div>
        <div class="panel_control">
            <div class="title py-4">
                <h3>Panel de Control</h3>
                <button class="crear"><i class="fa fa-plus"></i> Crear Noticia</button>
            </div>

            <div class="dashboard-cards">
                <div class="cads-news">
                    <div class="cards-header">
                        <h5>NOTICIAS TOTALES</h5>
                        <i class="fa fa-newspaper"></i>
                    </div>
                    <div class="cards-body">
                        <span>536</span>
                    </div>
                </div>

                <div class="cads-news">
                    <div class="cards-header">
                        <h5>PENDIENTES</h5>
                        <i class="fa fa-clock"></i>
                    </div>
                    <div class="cards-body">
                        <span>20</span>
                    </div>
                </div>
                <div class="cads-news">
                    <div class="cards-header">
                        <h5>PUBLICADAS HOY</h5>
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="cards-body">
                        <span>12</span>
                    </div>
                </div>
                <div class="cads-news">
                    <div class="cards-header">
                        <h5>INTERNACIONALES</h5>
                        <i class="fa fa-globe"></i>
                    </div>
                    <div class="cards-body">
                        <span>200</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="recent">
            <div class="recent-top">
                <h3>Noticias Recientes</h3>
                <button class="ver-todas">Ver Todo</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>TÍTULO</th>
                        <th>TIPO</th>
                        <th>CATEGORIA</th>
                        <th>FECHA</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</td>
                        <td>Internacional</td>
                        <td>Política</td>
                        <td>2023-10-01</td>
                        <td><span class="pendiente">Pendiente</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</td>
                        <td>Nacional</td>
                        <td>Deportes</td>
                        <td>2023-10-01</td>
                        <td><span class="publicada">Publicada</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</td>
                        <td>Nacional</td>
                        <td>Deportes</td>
                        <td>2023-10-01</td>
                        <td><span class="publicada">Publicada</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</td>
                        <td>Nacional</td>
                        <td>Deportes</td>
                        <td>2023-10-01</td>
                        <td><span class="publicada">Publicada</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</td>
                        <td>Internacional</td>
                        <td>Política</td>
                        <td>2023-10-01</td>
                        <td><span class="pendiente">Pendiente</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
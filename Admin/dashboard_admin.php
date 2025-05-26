<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="slider">
        <div class="slider-content">
            <h4>FocoGlobal Admin</h4>
        </div>

        <div class="menu">
            <h3>Principal</h3>
            <ul>
                <li class="active"><a href="../Admin/dashboard_admin.html"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href=""><i class="fa fa-globe"></i>Pagina Principal</a></li>
            </ul>

            <h3>Contenido</h3>
            <ul>
                <li><a href=""> <i class="fa fa-newspaper"></i>Todas las Noticias</a></li>
                <li><a href="../Admin/noticias/Crear_Noticias.html"><i class="fa fa-plus-circle"></i>Crear Noticia</a></li>
                <li><a href=""><i class="fa fa-clock"></i>Programadas</a></li>
            </ul>

            <h3>Administración</h3>
            <ul>
                <li><a href=""><i class="fa fa-users"></i>Usuarios</a></li>
                <li><a href=""><i class="fa fa-comments"></i>Comentarios</a></li>
            </ul>
        </div>
    </div>

    <main>
        <div class="navbar">
            <div class="search">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Buscar noticia nacional, internacional...">
            </div>

            <div class="user-admin">
                <i class="fa fa-user"></i>
                <div class="content">
                    <h4>Admin</h4>
                    <p>Editor principal</p>
                </div>
            </div>
        </div>

        <div class="panel_control">
            <div class="title">
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
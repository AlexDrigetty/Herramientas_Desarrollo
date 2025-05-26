<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Noticia</title>
    <link rel="stylesheet" href="../../Css/admin.css">
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
                <li class="active"><a href="../dashboard_admin.html"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li><a href=""><i class="fa fa-globe"></i>Pagina Principal</a></li>
            </ul>

            <h3>Contenido</h3>
            <ul>
                <li><a href=""> <i class="fa fa-newspaper"></i>Todas las Noticias</a></li>
                <li><a href="../noticias/Crear_Noticias.html"><i class="fa fa-plus-circle"></i>Crear Noticia</a></li>
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


        <div class="crear">
            <h3>CREAR NOTICIA</h3>

            <div class="box-1">
                <div class="box-content">
                    <label for="Titulo">Titulo</label>
                    <input type="text" placeholder="Ingrese Titulo">
                </div>
                <div class="box-content">
                    <label for="Contenido">CONTENIDO</label>
                    <textarea name="contenido" id="" placeholder="Ingrese Contenido de la noticia"></textarea>
                </div>
                <div class="box-content">
                    <label for="Portada">FOTO DE PORTADA</label>
                    <input type="file" class="img">
                </div>

                <div class="content">
                    <div class="tipo-noticia">
                        <label for="tipo-noticia">TIPO DE NOTICIA</label>
                        <select name="" id="">Selecione el tipo
                            <option value="">Nacional</option>
                            <option value="">Internacional</option>
                        </select>
                    </div>
                    <div class="categoria">
                        <label for="Categoria">CATEGORIA</label>
                        <select name="categoria" id="">
                            <option value="">Seleccione una categoria</option>
                            <option value="">Deportes</option>
                            <option value="">Cultura</option>
                            <option value="">Tecnologia</option>
                        </select>
                    </div>

                </div>
                <div class="botones">
                    <button><i class="fa fa-plus"></i>PUBLICAR</button>
                    <button>PROGRAMAR FECHA</button>
                    <button><i class="fa fa-x"></i>CANCELAR</button>
                </div>
            </div>

        </div>

    </main>

</body>

</html>
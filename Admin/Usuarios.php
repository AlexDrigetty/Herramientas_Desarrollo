<?php include 'admin_navbar.php';?>
<?php include 'admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Noticias Globales</title>
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="../Css/Internacional.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>

    <main>
        <?php include 'slider.php';?>

        <div class="todo">
            <div class="boto mb-4">
                <a class="crear"><i class="fa fa-plus"></i>Agregar Usuario</a>
            </div>

            <table class="mb-3">
                <thead>
                    <tr>
                        <th>NOMBRE</th>
                        <th>APELLIDOS</th>
                        <th>CORREO</th>
                        <th>FECHA DE CREACION</th>
                        <th>ESTADO</th>
                        <th>ACCION</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>YAIR ALEJANDRO</td>
                        <td>ZAPATA CORREA</td>
                        <td>yair@ejemplo.com</td>
                        <td>2023-10-01</td>
                        <td><span class="pendiente">ACTIVO</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>CARLA MARIA</td>
                        <td>SAAVEDRA MIO</td>
                        <td>yair@ejemplo.com</td>
                        <td>2023-10-01</td>
                        <td><span class="pendiente">ACTIVO</span></td>
                        <td>
                            <button class="editar"><i class="fa fa-edit"></i></button>
                            <button class="eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="pagination-container">
                    <ul class="pagination" id="pagination">
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item active"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                    </ul>
                </div>
            </div>
    </main>
</body>

</html>
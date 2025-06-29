<?php include 'admin_navbar.php'; ?>
<?php include 'admin_auth.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Noticias Globales</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="../Css/Internacional.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>

        <div class="todo mt-4">
            <!-- Botón para abrir modal -->
            <div class="boto mb-4">
                <button type="button" class="crear" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                    <i class="fas fa-plus me-2"></i>Agregar Usuario
                </button>
            </div>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>NOMBRE</th>
                            <th>APELLIDOS</th>
                            <th>CORREO</th>
                            <th>ROL</th>
                            <th>FECHA REGISTRO</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../bd/conexion.php';
                        
                        $sql = "SELECT u.id, u.nombre, u.apellido, u.correo, u.fecha_registro, r.nombre as rol 
                                FROM usuarios u 
                                JOIN roles r ON u.rol_id = r.id
                                ORDER BY u.fecha_registro DESC";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>'.htmlspecialchars($row['nombre']).'</td>';
                                echo '<td>'.htmlspecialchars($row['apellido']).'</td>';
                                echo '<td>'.htmlspecialchars($row['correo']).'</td>';
                                echo '<td>'.htmlspecialchars($row['rol']).'</td>';
                                echo '<td>'.htmlspecialchars($row['fecha_registro']).'</td>';
                                echo '<td><span class="badge bg-success">ACTIVO</span></td>';
                                echo '<td>
                                        <button class="btn btn-sm btn-warning editar" data-id="'.$row['id'].'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger eliminar ms-2" data-id="'.$row['id'].'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                      </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">No se encontraron usuarios</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Modal para crear usuario -->
        <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="crearUsuarioModalLabel">Nuevo Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formCrearUsuario" action="../funciones/procesar_usuario.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                            </div>
                            <div class="mb-3">
                                <label for="rol" class="form-label">Tipo de Usuario</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="0">Administrador</option>
                                    <option value="1" selected>Usuario</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Mostrar mensaje en consola para verificar que el script se carga
            console.log("Script de usuarios cargado correctamente");
            
            // Validación del formulario
            $('#formCrearUsuario').on('submit', function(e) {
                const password = $('#contrasena').val();
                const confirmPassword = $('#confirmar_contrasena').val();
                
                if (password !== confirmPassword) {
                    alert('Las contraseñas no coinciden');
                    e.preventDefault();
                    return false;
                }
                
                if (password.length < 8) {
                    alert('La contraseña debe tener al menos 8 caracteres');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
            
            // Manejo de botones de editar
            $('.editar').on('click', function() {
                const userId = $(this).data('id');
                alert('Editar usuario ID: ' + userId);
                // Aquí puedes implementar la lógica para editar
            });
            
            // Manejo de botones de eliminar
            $('.eliminar').on('click', function() {
                const userId = $(this).data('id');
                if (confirm('¿Estás seguro de eliminar este usuario?')) {
                    $.post('procesar_usuario.php', {
                        action: 'eliminar',
                        id: userId
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }, 'json').fail(function() {
                        alert('Error al conectar con el servidor');
                    });
                }
            });
        });
    </script>
</body>
</html>
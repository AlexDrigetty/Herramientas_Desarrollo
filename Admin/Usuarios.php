<?php include 'admin_navbar.php'; ?>
<?php include 'admin_Auth.php'; ?>
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
    <!-- SweetAlert2 para mensajes bonitos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="todo mt-4">
                <!-- Botón para abrir modal -->
                <div class="boto mb-4">
                    <button type="button" class="crear" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                        <i class="fas fa-plus me-2"></i>Agregar Usuario
                    </button>
                </div>

                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead >
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

                            $sql = "SELECT u.id, u.nombre, u.apellido, u.correo, u.fecha_registro, r.nombre as rol, r.id as rol_id 
                                FROM usuarios u 
                                JOIN roles r ON u.rol_id = r.id
                                ORDER BY u.fecha_registro DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['apellido']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['rol']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['fecha_registro']) . '</td>';
                                    echo '<td><span class="publicada">Activo</span></td>';
                                    echo '<td>
                                        <button class=" editar" data-id="' . $row['id'] . '" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="eliminar ms-2" data-id="' . $row['id'] . '" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
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
        </div>

        <!-- Modal para crear usuario -->
        <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #003366;">
                        <h5 class="modal-title" id="crearUsuarioModalLabel">Nuevo Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formCrearUsuario">
                        <input type="hidden" name="action" value="crear">
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
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required minlength="8">
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required minlength="8">
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
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cancelar</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #003366;">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar usuario -->
        <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #003366;">
                        <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formEditarUsuario">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" name="id" id="usuario-id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="edit-apellido" name="apellido" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="edit-correo" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-rol" class="form-label">Tipo de Usuario</label>
                                <select class="form-select" id="edit-rol" name="rol" required>
                                    <option value="1">Usuario</option>
                                    <option value="0">Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" style="background-color: #003366; border-color: #003366;">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #003366;">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar este usuario permanentemente?</p>
                        <p class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button id="confirmDeleteBtn" class="btn btn-primary" style="background-color: #003366; border-color: #003366;">
                            <i class="fas fa-trash-alt me-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 para mensajes bonitos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            console.log("Script de usuarios cargado correctamente");

            function showAlert(icon, title, text) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: icon,
                    title: title,
                    text: text
                });
            }

            // Manejar el envío del formulario de creación
            $('#formCrearUsuario').on('submit', function(e) {
                e.preventDefault();

                // Validación de contraseñas
                const password = $('#contrasena').val();
                const confirmPassword = $('#confirmar_contrasena').val();

                if (password !== confirmPassword) {
                    showAlert('error', 'Error', 'Las contraseñas no coinciden');
                    return false;
                }

                const formData = $(this).serialize();

                $.ajax({
                    url: '../funciones/procesar_usuario.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Éxito', response.message);
                            $('#crearUsuarioModal').modal('hide');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert('error', 'Error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error', 'Error al procesar la solicitud');
                    }
                });
            });

            // Manejar el envío del formulario de edición
            $('#formEditarUsuario').on('submit', function(e) {
                e.preventDefault();

                // Validación de contraseñas si se proporcionaron
                const password = $('#edit-contrasena').val();
                const confirmPassword = $('#edit-confirmar_contrasena').val();

                if (password && password !== confirmPassword) {
                    showAlert('error', 'Error', 'Las contraseñas no coinciden');
                    return false;
                }

                const formData = $(this).serialize();

                $.ajax({
                    url: '../funciones/procesar_usuario.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Éxito', response.message);
                            $('#editarUsuarioModal').modal('hide');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert('error', 'Error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error', 'Error al procesar la solicitud');
                    }
                });
            });

            // Manejar el modal de edición de usuario
            $('#editarUsuarioModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const userId = button.data('id');

                // Mostrar cargando
                $('#edit-nombre').val('Cargando...');
                $('#edit-apellido').val('Cargando...');
                $('#edit-correo').val('Cargando...');

                // Obtener datos del usuario
                $.ajax({
                    url: '../funciones/obtener_usuario.php',
                    type: 'GET',
                    data: {
                        id: userId
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            showAlert('error', 'Error', data.error);
                            return;
                        }

                        // Llenar los campos del formulario
                        $('#usuario-id').val(data.id);
                        $('#edit-nombre').val(data.nombre);
                        $('#edit-apellido').val(data.apellido);
                        $('#edit-correo').val(data.correo);
                        $('#edit-rol').val(data.rol_id);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error', 'Error al cargar los datos del usuario');
                    }
                });
            });

            // Manejar el modal de confirmación de eliminación
            $('#confirmDeleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const userId = button.data('id');

                // Configurar el botón de eliminar
                $('#confirmDeleteBtn').off('click').on('click', function() {
                    $.ajax({
                        url: '../funciones/procesar_usuario.php',
                        type: 'POST',
                        data: {
                            action: 'eliminar',
                            id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', 'Éxito', response.message);
                                $('#confirmDeleteModal').modal('hide');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showAlert('error', 'Error', response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            showAlert('error', 'Error', 'Error al procesar la solicitud');
                        }
                    });
                });
            });

            // Limpiar formularios al cerrar los modales
            $('#crearUsuarioModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            $('#editarUsuarioModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>

</body>

</html>
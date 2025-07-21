<?php 
include 'admin_navbar.php';
include 'admin_Auth.php';

require_once '../bd/conexion.php';

// Función para remover parámetros de la URL
function remove_query_param($param) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    unset($query_params[$param]);
    $new_query = http_build_query($query_params);
    return $url['path'] . ($new_query ? '?' . $new_query : '');
}

// Función para generar enlaces de paginación con filtros
function get_pagination_link($pagina) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    $query_params['pagina'] = $pagina;
    return $url['path'] . '?' . http_build_query($query_params);
}

// Obtener roles para el filtro
$sql_roles = "SELECT id, nombre FROM roles ORDER BY nombre";
$resultado_roles = $conn->query($sql_roles);
$roles = $resultado_roles->fetch_all(MYSQLI_ASSOC);

// Estados para el filtro
$estados = [
    ['id' => 1, 'nombre' => 'Activo'],
    ['id' => 0, 'nombre' => 'Inactivo']
];

// Obtener parámetros de filtrado
$rol_filtro = isset($_GET['rol']) ? (int)$_GET['rol'] : null;
$estado_filtro = isset($_GET['estado']) ? (int)$_GET['estado'] : null;

// Configuración de paginación
$usuarios_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $usuarios_por_pagina;

// Construir consulta base con filtros
$sql_base = "SELECT u.id, u.nombre, u.apellido, u.correo, u.fecha_registro, u.estado, 
             r.nombre as rol, r.id as rol_id 
             FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id";

// Aplicar filtros si existen
$where_conditions = [];
$params = [];
$types = '';

if ($rol_filtro !== null && $rol_filtro !== '') {
    $where_conditions[] = "u.rol_id = ?";
    $params[] = $rol_filtro;
    $types .= 'i';
}

if ($estado_filtro !== null && $estado_filtro !== '') {
    $where_conditions[] = "u.estado = ?";
    $params[] = $estado_filtro;
    $types .= 'i';
}

if (!empty($where_conditions)) {
    $sql_base .= " WHERE " . implode(" AND ", $where_conditions);
}

// Consulta para contar total de usuarios (para paginación)
$sql_total = "SELECT COUNT(*) as total FROM usuarios u";
if (!empty($where_conditions)) {
    $sql_total .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$resultado_total = $stmt_total->get_result();
$total_usuarios = $resultado_total->fetch_assoc()['total'];
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

// Consulta para obtener usuarios con ordenación
$sql = $sql_base . " ORDER BY u.fecha_registro DESC LIMIT ?, ?";

// Preparar y ejecutar consulta con parámetros
$types .= 'ii';
$params[] = $offset;
$params[] = $usuarios_por_pagina;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$usuarios = $stmt->get_result();

if (!$usuarios) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Noticias Globales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/admin.css">
    <link rel="stylesheet" href="../Css/Internacional.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .filtros-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filtros-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .buttons-wrapper {
            display: flex;
            gap: 10px;
        }
        .publicada { color: #28a745; font-weight: bold; }
        .pendiente { color: #dc3545; font-weight: bold; }
    </style>
</head>

<body>
    <main>
        <?php include 'slider.php'; ?>
        <div id="main-content">
            <div class="todo mt-4">
                <div class="header-actions">
                    <div class="filtros-wrapper">
                        <form method="get" action="usuarios.php" id="filtros-form" class="row g-3 align-items-center">
                            <div class="col-auto">
                                <select name="rol" class="form-select" id="filtro-rol">
                                    <option value="">Todos los roles</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>" <?= ($rol_filtro == $rol['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rol['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="estado" class="form-select" id="filtro-estado">
                                    <option value="">Todos los estados</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id'] ?>" <?= ($estado_filtro == $estado['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="usuarios.php" class="btn btn-secondary ms-2">Limpiar</a>
                            </div>
                        </form>
                    </div>

                    <div class="buttons-wrapper">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                            <i class="fas fa-plus me-2"></i>Agregar Usuario
                        </button>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            <?php if ($usuarios->num_rows > 0): ?>
                                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                        <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                                        <td>
                                            <span class="<?= $usuario['estado'] ? 'publicada' : 'pendiente' ?>">
                                                <?= $usuario['estado'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning editar" data-id="<?= $usuario['id'] ?>" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger eliminar ms-2" data-id="<?= $usuario['id'] ?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-3"></i><br>
                                        No se encontraron usuarios <?= ($rol_filtro !== null || $estado_filtro !== null) ? 'con los filtros seleccionados' : '' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= get_pagination_link($pagina_actual - 1) ?>">&laquo; Anterior</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                <a class="page-link" href="<?= get_pagination_link($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= get_pagination_link($pagina_actual + 1) ?>">Siguiente &raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
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
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" style="background-color: #003366;">
                                <i class="fas fa-save me-2"></i>Guardar Usuario
                            </button>
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
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit-estado" class="form-label">Estado</label>
                                <select class="form-select" id="edit-estado" name="estado" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
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
                        $('#edit-estado').val(data.estado);
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
                $('#estado').val('1'); // Establecer estado activo por defecto
            });

            $('#editarUsuarioModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>
</body>
</html>
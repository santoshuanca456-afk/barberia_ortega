<?php
/**
 * Listado de Usuarios
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();
requireRole(ROLE_ADMIN); // Solo administradores

$db = getDB();

// Obtener todos los usuarios
$stmt = $db->query("
    SELECT u.*,
           COUNT(r.id_reserva) as total_reservas,
           COUNT(CASE WHEN r.estado = 'finalizada' THEN 1 END) as reservas_finalizadas
    FROM usuarios u
    LEFT JOIN reservas r ON u.id_usuario = r.id_usuario
    GROUP BY u.id_usuario
    ORDER BY u.fecha_registro DESC
");
$usuarios = $stmt->fetchAll();

// Estadísticas generales
$stmtStats = $db->query("
    SELECT 
        COUNT(*) as total_usuarios,
        COUNT(CASE WHEN rol = 'barbero' THEN 1 END) as barberos,
        COUNT(CASE WHEN rol = 'administrador' THEN 1 END) as administradores,
        COUNT(CASE WHEN rol = 'apoyo' THEN 1 END) as apoyo,
        COUNT(CASE WHEN estado = 'activo' THEN 1 END) as activos,
        COUNT(CASE WHEN estado = 'inactivo' THEN 1 END) as inactivos
    FROM usuarios
");
$stats = $stmtStats->fetch();

$pageTitle = "Usuarios";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-users-cog text-primary"></i> 
                Gestión de Usuarios
            </h1>
            <p class="text-muted">Administra barberos, personal de apoyo y administradores</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="crear.php" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0 text-primary"><?php echo $stats['total_usuarios']; ?></h3>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-cut fa-2x text-success mb-2"></i>
                        <h3 class="mb-0 text-success"><?php echo $stats['barberos']; ?></h3>
                        <small class="text-muted">Barberos</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-hands-helping fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0 text-warning"><?php echo $stats['apoyo']; ?></h3>
                        <small class="text-muted">Apoyo</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0 text-danger"><?php echo $stats['administradores']; ?></h3>
                        <small class="text-muted">Admins</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                        <h3 class="mb-0 text-info"><?php echo $stats['activos']; ?></h3>
                        <small class="text-muted">Activos</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-secondary shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-center">
                        <i class="fas fa-ban fa-2x text-secondary mb-2"></i>
                        <h3 class="mb-0 text-secondary"><?php echo $stats['inactivos']; ?></h3>
                        <small class="text-muted">Inactivos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> Lista de Usuarios del Sistema
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Reservas</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><strong>#<?php echo $usuario['id_usuario']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <?php if ($usuario['rol'] == 'administrador'): ?>
                                            <i class="fas fa-user-shield fa-2x text-danger"></i>
                                        <?php elseif ($usuario['rol'] == 'barbero'): ?>
                                            <i class="fas fa-cut fa-2x text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-hands-helping fa-2x text-warning"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo $usuario['usuario']; ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td>
                                <?php if ($usuario['telefono']): ?>
                                    <div>
                                        <i class="fas fa-phone text-success"></i>
                                        <a href="tel:<?php echo $usuario['telefono']; ?>">
                                            <?php echo $usuario['telefono']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($usuario['correo']): ?>
                                    <div>
                                        <i class="fas fa-envelope text-info"></i>
                                        <a href="mailto:<?php echo $usuario['correo']; ?>">
                                            <?php echo $usuario['correo']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!$usuario['telefono'] && !$usuario['correo']): ?>
                                    <span class="text-muted">Sin contacto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario['rol'] == 'administrador'): ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-user-shield"></i> Administrador
                                    </span>
                                <?php elseif ($usuario['rol'] == 'barbero'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-cut"></i> Barbero
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-hands-helping"></i> Apoyo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario['estado'] == 'activo'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-ban"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario['rol'] == 'barbero'): ?>
                                    <span class="badge bg-info">
                                        <?php echo $usuario['reservas_finalizadas']; ?> / <?php echo $usuario['total_reservas']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo formatDate($usuario['fecha_registro']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="ver.php?id=<?php echo $usuario['id_usuario']; ?>" 
                                       class="btn btn-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?php echo $usuario['id_usuario']; ?>" 
                                       class="btn btn-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($usuario['id_usuario'] != $_SESSION['user_id']): ?>
                                        <a href="procesar.php?action=cambiar_estado&id=<?php echo $usuario['id_usuario']; ?>&estado=<?php echo $usuario['estado'] == 'activo' ? 'inactivo' : 'activo'; ?>" 
                                           class="btn btn-<?php echo $usuario['estado'] == 'activo' ? 'secondary' : 'success'; ?>" 
                                           data-bs-toggle="tooltip" 
                                           title="<?php echo $usuario['estado'] == 'activo' ? 'Desactivar' : 'Activar'; ?>">
                                            <i class="fas fa-<?php echo $usuario['estado'] == 'activo' ? 'ban' : 'check'; ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Información de Roles -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Descripción de Roles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-user-shield"></i> Administrador</h6>
                                <ul class="mb-0 small">
                                    <li>Acceso total al sistema</li>
                                    <li>Gestión de usuarios</li>
                                    <li>Reportes financieros</li>
                                    <li>Configuración del sistema</li>
                                    <li>Auditoría completa</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-cut"></i> Barbero</h6>
                                <ul class="mb-0 small">
                                    <li>Gestión de reservas propias</li>
                                    <li>Gestión de clientes</li>
                                    <li>Registro de servicios</li>
                                    <li>Ver agenda personal</li>
                                    <li>Alquiler de estaciones</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-hands-helping"></i> Apoyo</h6>
                                <ul class="mb-0 small">
                                    <li>Gestión de turnos</li>
                                    <li>Limpieza y apertura/cierre</li>
                                    <li>Ver reservas del día</li>
                                    <li>Notificaciones internas</li>
                                    <li>Tareas operativas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
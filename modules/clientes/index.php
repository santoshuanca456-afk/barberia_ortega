<?php
/**
 * Listado de Clientes
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener todos los clientes
$stmt = $db->query("
    SELECT c.*,
           COUNT(r.id_reserva) as total_reservas,
           MAX(r.fecha_inicio) as ultima_visita
    FROM clientes c
    LEFT JOIN reservas r ON c.id_cliente = r.id_cliente
    GROUP BY c.id_cliente
    ORDER BY c.fecha_registro DESC
");
$clientes = $stmt->fetchAll();

// Estadísticas generales
$stmtStats = $db->query("
    SELECT 
        COUNT(*) as total_clientes,
        COUNT(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 END) as nuevos_hoy,
        COUNT(CASE WHEN DATE(fecha_registro) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as nuevos_semana,
        COUNT(CASE WHEN DATE(fecha_registro) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as nuevos_mes
    FROM clientes
");
$stats = $stmtStats->fetch();

$pageTitle = "Clientes";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-users text-primary"></i> 
                Gestión de Clientes
            </h1>
            <p class="text-muted">Administra la base de datos de clientes</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="crear.php" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted text-uppercase">Total Clientes</small>
                            <h3 class="mb-0 text-primary"><?php echo $stats['total_clientes']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted text-uppercase">Nuevos Hoy</small>
                            <h3 class="mb-0 text-success"><?php echo $stats['nuevos_hoy']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted text-uppercase">Esta Semana</small>
                            <h3 class="mb-0 text-info"><?php echo $stats['nuevos_semana']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted text-uppercase">Este Mes</small>
                            <h3 class="mb-0 text-warning"><?php echo $stats['nuevos_mes']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> Lista de Clientes
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable" id="tablaClientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Total Visitas</th>
                            <th>Última Visita</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><strong>#<?php echo $cliente['id_cliente']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-primary me-2">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <strong><?php echo $cliente['nombre']; ?></strong>
                                </div>
                            </td>
                            <td>
                                <?php if ($cliente['telefono']): ?>
                                    <a href="tel:<?php echo $cliente['telefono']; ?>" class="text-decoration-none">
                                        <i class="fas fa-phone text-success"></i>
                                        <?php echo $cliente['telefono']; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Sin teléfono</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cliente['correo']): ?>
                                    <a href="mailto:<?php echo $cliente['correo']; ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope text-info"></i>
                                        <?php echo $cliente['correo']; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Sin correo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $cliente['total_reservas']; ?> visitas
                                </span>
                            </td>
                            <td>
                                <?php if ($cliente['ultima_visita']): ?>
                                    <?php echo formatDate($cliente['ultima_visita']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin visitas</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo formatDate($cliente['fecha_registro']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="ver.php?id=<?php echo $cliente['id_cliente']; ?>" 
                                       class="btn btn-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?php echo $cliente['id_cliente']; ?>" 
                                       class="btn btn-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (hasRole(ROLE_ADMIN)): ?>
                                        <a href="#" 
                                           onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $cliente['id_cliente']; ?>', '¿Eliminar este cliente y todo su historial?')" 
                                           class="btn btn-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Eliminar">
                                            <i class="fas fa-trash"></i>
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
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php include '../../includes/footer.php'; ?>
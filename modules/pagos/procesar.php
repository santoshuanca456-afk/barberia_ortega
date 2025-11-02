<?php
/**
 * Listado de Pagos
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Filtros
$filtroFechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$filtroFechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$filtroMetodo = isset($_GET['metodo']) ? $_GET['metodo'] : '';
$filtroTipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'servicios';

// Obtener pagos según el tipo
if ($filtroTipo == 'servicios') {
    $query = "
        SELECT p.*, 
               r.id_reserva,
               c.nombre as cliente_nombre,
               s.nombre as servicio_nombre,
               u.nombre as usuario_nombre
        FROM pagos p
        LEFT JOIN reservas r ON p.id_reserva = r.id_reserva
        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
        LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
        LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE DATE(p.fecha_pago) BETWEEN ? AND ?
    ";
    
    $params = [$filtroFechaInicio, $filtroFechaFin];
    
    if ($filtroMetodo) {
        $query .= " AND p.metodo = ?";
        $params[] = $filtroMetodo;
    }
    
    $query .= " ORDER BY p.fecha_pago DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $pagos = $stmt->fetchAll();
    
} else {
    // Pagos de alquileres
    $query = "
        SELECT pa.*, 
               a.id_alquiler,
               e.nombre as estacion_nombre,
               u.nombre as usuario_nombre
        FROM pagos_alquiler pa
        LEFT JOIN alquileres a ON pa.id_alquiler = a.id_alquiler
        LEFT JOIN estaciones e ON a.id_estacion = e.id_estacion
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
        WHERE DATE(pa.fecha_pago) BETWEEN ? AND ?
    ";
    
    $params = [$filtroFechaInicio, $filtroFechaFin];
    
    if ($filtroMetodo) {
        $query .= " AND pa.metodo = ?";
        $params[] = $filtroMetodo;
    }
    
    $query .= " ORDER BY pa.fecha_pago DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $pagos = $stmt->fetchAll();
}

// Obtener reservas pendientes de pago para el botón rápido
$reservasPendientes = [];
if ($filtroTipo == 'servicios') {
    $stmtReservas = $db->query("
        SELECT r.id_reserva, c.nombre as cliente_nombre, s.nombre as servicio_nombre, s.precio
        FROM reservas r
        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
        LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
        WHERE r.pagado = FALSE 
        AND r.estado IN ('confirmada', 'finalizada')
        ORDER BY r.fecha_inicio DESC
        LIMIT 10
    ");
    $reservasPendientes = $stmtReservas->fetchAll();
}

// Estadísticas del período
if ($filtroTipo == 'servicios') {
    $stmtStats = $db->prepare("
        SELECT 
            COUNT(*) as total_pagos,
            SUM(monto) as total_ingresos,
            COUNT(CASE WHEN metodo = 'efectivo' THEN 1 END) as efectivo_count,
            SUM(CASE WHEN metodo = 'efectivo' THEN monto ELSE 0 END) as efectivo_total,
            COUNT(CASE WHEN metodo = 'tarjeta' THEN 1 END) as tarjeta_count,
            SUM(CASE WHEN metodo = 'tarjeta' THEN monto ELSE 0 END) as tarjeta_total,
            COUNT(CASE WHEN metodo = 'qr' THEN 1 END) as qr_count,
            SUM(CASE WHEN metodo = 'qr' THEN monto ELSE 0 END) as qr_total
        FROM pagos 
        WHERE DATE(fecha_pago) BETWEEN ? AND ?
    ");
} else {
    $stmtStats = $db->prepare("
        SELECT 
            COUNT(*) as total_pagos,
            SUM(monto) as total_ingresos,
            COUNT(CASE WHEN metodo = 'efectivo' THEN 1 END) as efectivo_count,
            SUM(CASE WHEN metodo = 'efectivo' THEN monto ELSE 0 END) as efectivo_total,
            COUNT(CASE WHEN metodo = 'tarjeta' THEN 1 END) as tarjeta_count,
            SUM(CASE WHEN metodo = 'tarjeta' THEN monto ELSE 0 END) as tarjeta_total,
            COUNT(CASE WHEN metodo = 'qr' THEN 1 END) as qr_count,
            SUM(CASE WHEN metodo = 'qr' THEN monto ELSE 0 END) as qr_total
        FROM pagos_alquiler 
        WHERE DATE(fecha_pago) BETWEEN ? AND ?
    ");
}

$stmtStats->execute([$filtroFechaInicio, $filtroFechaFin]);
$stats = $stmtStats->fetch();

$pageTitle = "Pagos";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-dollar-sign text-primary"></i> 
                Gestión de Pagos
            </h1>
            <p class="text-muted">Administra los pagos de servicios y alquileres</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalRegistrarPago">
                    <i class="fas fa-plus"></i> Registrar Pago
                </button>
                <?php if ($filtroTipo == 'servicios' && count($reservasPendientes) > 0): ?>
                <button type="button" class="btn btn-success btn-lg dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Pagos Rápidos</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Pagos Rápidos - Servicios</h6></li>
                    <?php foreach ($reservasPendientes as $reserva): ?>
                    <li>
                        <a class="dropdown-item" href="procesar.php?action=marcar_pagado_rapido&id=<?php echo $reserva['id_reserva']; ?>" 
                           onclick="return confirm('¿Marcar como pagada la reserva #<?php echo $reserva['id_reserva']; ?>?')">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small><strong>#<?php echo $reserva['id_reserva']; ?></strong> - <?php echo $reserva['cliente_nombre']; ?></small><br>
                                    <small class="text-muted"><?php echo $reserva['servicio_nombre']; ?> - <?php echo formatMoney($reserva['precio']); ?></small>
                                </div>
                                <span class="badge bg-success">Efectivo</span>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="registrar_servicio.php">Ver todas las reservas...</a></li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabs de Tipo de Pago -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $filtroTipo == 'servicios' ? 'active' : ''; ?>" 
               href="?tipo=servicios&fecha_inicio=<?php echo $filtroFechaInicio; ?>&fecha_fin=<?php echo $filtroFechaFin; ?>">
                <i class="fas fa-scissors"></i> Pagos de Servicios
                <?php if ($filtroTipo == 'servicios'): ?>
                <span class="badge bg-primary ms-1"><?php echo count($pagos); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filtroTipo == 'alquileres' ? 'active' : ''; ?>" 
               href="?tipo=alquileres&fecha_inicio=<?php echo $filtroFechaInicio; ?>&fecha_fin=<?php echo $filtroFechaFin; ?>">
                <i class="fas fa-chair"></i> Pagos de Alquileres
                <?php if ($filtroTipo == 'alquileres'): ?>
                <span class="badge bg-primary ms-1"><?php echo count($pagos); ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted text-uppercase">Total Ingresos</small>
                            <h3 class="mb-0 text-primary"><?php echo formatMoney($stats['total_ingresos']); ?></h3>
                            <small class="text-muted"><?php echo $stats['total_pagos']; ?> pagos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-primary"></i>
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
                            <small class="text-muted text-uppercase">Efectivo</small>
                            <h3 class="mb-0 text-success"><?php echo formatMoney($stats['efectivo_total']); ?></h3>
                            <small class="text-muted"><?php echo $stats['efectivo_count']; ?> pagos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
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
                            <small class="text-muted text-uppercase">Tarjeta</small>
                            <h3 class="mb-0 text-info"><?php echo formatMoney($stats['tarjeta_total']); ?></h3>
                            <small class="text-muted"><?php echo $stats['tarjeta_count']; ?> pagos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-info"></i>
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
                            <small class="text-muted text-uppercase">QR</small>
                            <h3 class="mb-0 text-warning"><?php echo formatMoney($stats['qr_total']); ?></h3>
                            <small class="text-muted"><?php echo $stats['qr_count']; ?> pagos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-qrcode fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="tipo" value="<?php echo $filtroTipo; ?>">
                
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $filtroFechaInicio; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo $filtroFechaFin; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="metodo" class="form-select">
                        <option value="">Todos</option>
                        <option value="efectivo" <?php echo $filtroMetodo == 'efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="tarjeta" <?php echo $filtroMetodo == 'tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
                        <option value="qr" <?php echo $filtroMetodo == 'qr' ? 'selected' : ''; ?>>QR</option>
                        <option value="otro" <?php echo $filtroMetodo == 'otro' ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> 
                Lista de Pagos - <?php echo $filtroTipo == 'servicios' ? 'Servicios' : 'Alquileres'; ?>
                <small class="float-end">Mostrando <?php echo count($pagos); ?> pago(s)</small>
            </h6>
        </div>
        <div class="card-body">
            <?php if (count($pagos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <?php if ($filtroTipo == 'servicios'): ?>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                <?php else: ?>
                                    <th>Estación</th>
                                    <th>Arrendatario</th>
                                <?php endif; ?>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Registrado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td>
                                    <strong>
                                        #<?php echo $filtroTipo == 'servicios' ? $pago['id_pago'] : $pago['id_pago_alquiler']; ?>
                                    </strong>
                                </td>
                                <td><?php echo formatDateTime($pago['fecha_pago']); ?></td>
                                
                                <?php if ($filtroTipo == 'servicios'): ?>
                                    <td>
                                        <i class="fas fa-user text-primary"></i>
                                        <?php echo $pago['cliente_nombre'] ?? 'N/A'; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-scissors text-info"></i>
                                        <?php echo $pago['servicio_nombre'] ?? 'N/A'; ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <i class="fas fa-chair text-primary"></i>
                                        <?php echo $pago['estacion_nombre'] ?? 'N/A'; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-user text-info"></i>
                                        <?php echo $pago['usuario_nombre'] ?? 'N/A'; ?>
                                    </td>
                                <?php endif; ?>
                                
                                <td><strong class="text-success"><?php echo formatMoney($pago['monto']); ?></strong></td>
                                <td>
                                    <?php
                                    $iconos = [
                                        'efectivo' => 'money-bill-wave',
                                        'tarjeta' => 'credit-card',
                                        'qr' => 'qrcode',
                                        'otro' => 'coins'
                                    ];
                                    $icon = $iconos[$pago['metodo']] ?? 'dollar-sign';
                                    ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($pago['metodo']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo $pago['usuario_nombre'] ?? 'Sistema'; ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-info" 
                                                onclick="verDetallePago(<?php echo htmlspecialchars(json_encode($pago)); ?>, '<?php echo $filtroTipo; ?>')"
                                                data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (hasRole(ROLE_ADMIN)): ?>
                                            <?php if ($filtroTipo == 'servicios'): ?>
                                                <a href="procesar.php?action=eliminar_pago_servicio&id=<?php echo $pago['id_pago']; ?>" 
                                                   class="btn btn-danger"
                                                   onclick="return confirm('¿Está seguro de eliminar este pago?')"
                                                   data-bs-toggle="tooltip" title="Eliminar pago">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="procesar.php?action=eliminar_pago_alquiler&id=<?php echo $pago['id_pago_alquiler']; ?>" 
                                                   class="btn btn-danger"
                                                   onclick="return confirm('¿Está seguro de eliminar este pago?')"
                                                   data-bs-toggle="tooltip" title="Eliminar pago">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-success">
                                <th colspan="<?php echo $filtroTipo == 'servicios' ? '4' : '4'; ?>" class="text-end">
                                    <strong>TOTAL:</strong>
                                </th>
                                <th>
                                    <strong><?php echo formatMoney($stats['total_ingresos']); ?></strong>
                                </th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay pagos registrados en este período</p>
                    <?php if ($filtroTipo == 'servicios'): ?>
                        <a href="registrar_servicio.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> Registrar Primer Pago de Servicio
                        </a>
                    <?php else: ?>
                        <a href="registrar_alquiler.php" class="btn btn-success mt-3">
                            <i class="fas fa-plus"></i> Registrar Primer Pago de Alquiler
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gráfico de Métodos de Pago -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Distribución por Método de Pago
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMetodos"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Resumen de Ingresos
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartIngresos"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-dollar-sign"></i> Registrar Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-4 text-center">Seleccione el tipo de pago que desea registrar:</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-scissors fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Pago de Servicio</h5>
                                <p class="card-text">Registrar pago por servicios de barbería</p>
                                <a href="registrar_servicio.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-arrow-right"></i> Acceder
                                </a>
                                <?php if (count($reservasPendientes) > 0): ?>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        <?php echo count($reservasPendientes); ?> reservas pendientes
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-chair fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Pago de Alquiler</h5>
                                <p class="card-text">Registrar pago por alquiler de estaciones</p>
                                <a href="registrar_alquiler.php" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-arrow-right"></i> Acceder
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (count($reservasPendientes) > 0): ?>
                <div class="mt-4">
                    <h6 class="text-muted border-bottom pb-2">
                        <i class="fas fa-clock"></i> Reservas Pendientes de Pago
                    </h6>
                    <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach (array_slice($reservasPendientes, 0, 5) as $reserva): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <small class="fw-bold text-primary">#<?php echo $reserva['id_reserva']; ?></small>
                                <small class="d-block"><?php echo $reserva['cliente_nombre']; ?></small>
                                <small class="text-muted"><?php echo $reserva['servicio_nombre']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning"><?php echo formatMoney($reserva['precio']); ?></span>
                                <a href="procesar.php?action=marcar_pagado_rapido&id=<?php echo $reserva['id_reserva']; ?>" 
                                   class="btn btn-sm btn-success mt-1"
                                   onclick="return confirm('¿Marcar como pagada la reserva #<?php echo $reserva['id_reserva']; ?>?')"
                                   data-bs-toggle="tooltip" title="Pago rápido en efectivo">
                                    <i class="fas fa-bolt"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($reservasPendientes) > 5): ?>
                        <div class="list-group-item text-center">
                            <small class="text-muted">
                                Y <?php echo count($reservasPendientes) - 5; ?> reservas más...
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalDetallePago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt"></i> Detalle del Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContenido">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Configurar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gráfico de métodos de pago
const ctxMetodos = document.getElementById('chartMetodos').getContext('2d');
new Chart(ctxMetodos, {
    type: 'doughnut',
    data: {
        labels: ['Efectivo', 'Tarjeta', 'QR', 'Otro'],
        datasets: [{
            data: [
                <?php echo $stats['efectivo_total']; ?>,
                <?php echo $stats['tarjeta_total']; ?>,
                <?php echo $stats['qr_total']; ?>,
                0
            ],
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#858796']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de ingresos
const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
new Chart(ctxIngresos, {
    type: 'bar',
    data: {
        labels: ['Efectivo', 'Tarjeta', 'QR'],
        datasets: [{
            label: 'Ingresos (Bs)',
            data: [
                <?php echo $stats['efectivo_total']; ?>,
                <?php echo $stats['tarjeta_total']; ?>,
                <?php echo $stats['qr_total']; ?>
            ],
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function verDetallePago(pago, tipo) {
    let html = '<dl class="row">';
    
    html += `<dt class="col-sm-4">ID Pago:</dt><dd class="col-sm-8">#${tipo == 'servicios' ? pago.id_pago : pago.id_pago_alquiler}</dd>`;
    html += `<dt class="col-sm-4">Fecha:</dt><dd class="col-sm-8">${formatearFechaHora(pago.fecha_pago)}</dd>`;
    html += `<dt class="col-sm-4">Monto:</dt><dd class="col-sm-8"><strong class="text-success">Bs ${parseFloat(pago.monto).toFixed(2)}</strong></dd>`;
    html += `<dt class="col-sm-4">Método:</dt><dd class="col-sm-8"><span class="badge bg-secondary">${pago.metodo.toUpperCase()}</span></dd>`;
    
    if (tipo == 'servicios') {
        html += `<dt class="col-sm-4">Cliente:</dt><dd class="col-sm-8">${pago.cliente_nombre || 'N/A'}</dd>`;
        html += `<dt class="col-sm-4">Servicio:</dt><dd class="col-sm-8">${pago.servicio_nombre || 'N/A'}</dd>`;
        html += `<dt class="col-sm-4">ID Reserva:</dt><dd class="col-sm-8">#${pago.id_reserva || 'N/A'}</dd>`;
    } else {
        html += `<dt class="col-sm-4">Estación:</dt><dd class="col-sm-8">${pago.estacion_nombre || 'N/A'}</dd>`;
        html += `<dt class="col-sm-4">Arrendatario:</dt><dd class="col-sm-8">${pago.usuario_nombre || 'N/A'}</dd>`;
        html += `<dt class="col-sm-4">ID Alquiler:</dt><dd class="col-sm-8">#${pago.id_alquiler || 'N/A'}</dd>`;
    }
    
    html += `<dt class="col-sm-4">Registrado por:</dt><dd class="col-sm-8">${pago.usuario_nombre || 'Sistema'}</dd>`;
    html += '</dl>';
    
    document.getElementById('detalleContenido').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetallePago')).show();
}

function formatearFechaHora(fecha) {
    return new Date(fecha).toLocaleString('es-BO');
}
</script>

<?php include '../../includes/footer.php'; ?>
<?php
/**
 * Dashboard de Servicios
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$today = date('Y-m-d');

// ==================== ESTADÍSTICAS DE SERVICIOS ====================

// Reservas del día
$stmt = $db->prepare("SELECT COUNT(*) as total FROM reservas WHERE DATE(fecha_inicio) = ?");
$stmt->execute([$today]);
$reservasHoy = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente' AND DATE(fecha_inicio) = ?");
$stmt->execute([$today]);
$reservasPendientes = $stmt->fetchColumn();

// Ingresos del día (solo servicios)
$stmt = $db->prepare("SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE DATE(fecha_pago) = ?");
$stmt->execute([$today]);
$ingresosDia = $stmt->fetchColumn();

// Clientes atendidos
$stmt = $db->prepare("SELECT COUNT(*) as total FROM reservas WHERE estado = 'finalizada' AND DATE(fecha_inicio) = ?");
$stmt->execute([$today]);
$clientesAtendidos = $stmt->fetchColumn();

// Total clientes
$stmt = $db->query("SELECT COUNT(*) FROM clientes");
$totalClientes = $stmt->fetchColumn();

// Servicios más solicitados (últimos 30 días)
$stmt = $db->query("
    SELECT s.nombre, COUNT(r.id_reserva) as total
    FROM servicios s
    LEFT JOIN reservas r ON s.id_servicio = r.id_servicio
    WHERE DATE(r.fecha_inicio) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY s.id_servicio
    ORDER BY total DESC
    LIMIT 5
");
$serviciosTop = $stmt->fetchAll();

// Próximas reservas del día
$stmt = $db->prepare("
    SELECT r.*, 
           c.nombre as cliente_nombre, 
           u.nombre as barbero_nombre, 
           s.nombre as servicio_nombre,
           s.precio
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE DATE(r.fecha_inicio) = ? AND r.fecha_inicio >= NOW()
    ORDER BY r.fecha_inicio ASC
    LIMIT 8
");
$stmt->execute([$today]);
$proximasReservas = $stmt->fetchAll();

// Ingresos de la semana
$stmt = $db->query("
    SELECT DATE(fecha_pago) as fecha, SUM(monto) as total
    FROM pagos
    WHERE DATE(fecha_pago) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha_pago)
    ORDER BY fecha ASC
");
$ingresosSemana = $stmt->fetchAll();

// Reservas pendientes de pago
$stmt = $db->prepare("
    SELECT COUNT(*) as total
    FROM reservas
    WHERE pagado = FALSE 
    AND estado IN ('confirmada', 'finalizada')
    AND DATE(fecha_inicio) = ?
");
$stmt->execute([$today]);
$reservasSinPagar = $stmt->fetchColumn();

$pageTitle = "Dashboard - Servicios";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="mb-2">
                <i class="fas fa-scissors text-primary"></i> 
                Panel de Servicios
            </h1>
            <p class="text-muted">Bienvenido, <?php echo $_SESSION['user_name']; ?> (<?php echo ucfirst($_SESSION['user_role']); ?>)</p>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 text-center">
                    <h4 class="mb-0" id="currentTime"></h4>
                    <small id="currentDate"></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-end">
            <a href="dashboard_operaciones.php" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-building"></i> Operaciones Internas
            </a>
        </div>
    </div>

    <!-- Métricas de Servicios -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Reservas Hoy
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $reservasHoy; ?></div>
                            <small class="text-muted"><?php echo $reservasPendientes; ?> pendientes</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ingresos del Día
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo formatMoney($ingresosDia); ?></div>
                            <small class="text-muted"><?php echo $clientesAtendidos; ?> clientes atendidos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Clientes
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $totalClientes; ?></div>
                            <small class="text-muted">Base de datos</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendientes Pago
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?php echo $reservasSinPagar; ?></div>
                            <small class="text-muted">Reservas sin pagar</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximas Reservas -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calendar-check"></i> Próximas Reservas del Día
                    </h6>
                    <span class="badge bg-light text-primary">LIVE</span>
                </div>
                <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                    <?php if (count($proximasReservas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Barbero</th>
                                        <th>Servicio</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proximasReservas as $reserva): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary">
                                                <?php echo date('H:i', strtotime($reserva['fecha_inicio'])); ?>
                                            </strong>
                                        </td>
                                        <td><?php echo $reserva['cliente_nombre'] ?? 'Sin nombre'; ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo $reserva['barbero_nombre'] ?? 'Sin asignar'; ?>
                                            </small>
                                        </td>
                                        <td><?php echo $reserva['servicio_nombre'] ?? 'N/A'; ?></td>
                                        <td><strong><?php echo formatMoney($reserva['precio']); ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo $reserva['estado'] == 'confirmada' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($reserva['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>No hay reservas pendientes para hoy</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="../reservas/" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver Todas las Reservas
                        </a>
                        <a href="../reservas/crear.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nueva Reserva
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios Más Solicitados -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-star"></i> Top Servicios (30 días)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (count($serviciosTop) > 0): ?>
                        <?php foreach ($serviciosTop as $index => $servicio): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <span class="badge bg-primary">#<?php echo $index + 1; ?></span>
                                        <strong><?php echo $servicio['nombre']; ?></strong>
                                    </div>
                                    <span class="badge bg-success"><?php echo $servicio['total']; ?> veces</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <?php 
                                    $maxServicios = $serviciosTop[0]['total'] ?? 1;
                                    $porcentaje = ($servicio['total'] / $maxServicios) * 100;
                                    ?>
                                    <div class="progress-bar bg-success" style="width: <?php echo $porcentaje; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p class="mb-0">No hay datos disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="../servicios/" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-eye"></i> Ver Todos los Servicios
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Ingresos Semanales -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line"></i> Ingresos por Servicios - Últimos 7 Días
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartIngresos" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt"></i> Accesos Rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="../reservas/crear.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-plus"></i> Nueva Reserva
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../clientes/crear.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-plus"></i> Nuevo Cliente
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../pagos/registrar_servicio.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-dollar-sign"></i> Registrar Pago
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../servicios/" class="btn btn-outline-warning w-100">
                                <i class="fas fa-scissors"></i> Gestionar Servicios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Actualizar reloj
function updateClock() {
    const now = new Date();
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    
    document.getElementById('currentTime').textContent = now.toLocaleTimeString('es-BO', timeOptions);
    document.getElementById('currentDate').textContent = now.toLocaleDateString('es-BO', dateOptions);
}

updateClock();
setInterval(updateClock, 1000);

// Gráfico de ingresos
const ctx = document.getElementById('chartIngresos').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            // Llenar con los últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $fecha = date('Y-m-d', strtotime("-$i days"));
                echo "'" . date('d/m', strtotime($fecha)) . "',";
            }
            ?>
        ],
        datasets: [{
            label: 'Ingresos (Bs)',
            data: [
                <?php 
                // Crear array de ingresos por fecha
                $ingresosPorFecha = [];
                foreach ($ingresosSemana as $ingreso) {
                    $ingresosPorFecha[$ingreso['fecha']] = $ingreso['total'];
                }
                
                // Llenar datos para cada día
                for ($i = 6; $i >= 0; $i--) {
                    $fecha = date('Y-m-d', strtotime("-$i days"));
                    echo isset($ingresosPorFecha[$fecha]) ? $ingresosPorFecha[$fecha] : 0;
                    echo ',';
                }
                ?>
            ],
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderColor: '#1cc88a',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1cc88a',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Bs ' + context.parsed.y.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Bs ' + value;
                    }
                }
            }
        }
    }
});

// Auto-refresh cada 60 segundos
setTimeout(function() {
    location.reload();
}, 60000);
</script>

<?php include '../../includes/footer.php'; ?>
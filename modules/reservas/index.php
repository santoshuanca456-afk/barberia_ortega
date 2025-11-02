<?php
/**
 * Listado de Reservas
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Filtros
$filtroFecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtroBarbero = isset($_GET['barbero']) ? $_GET['barbero'] : '';

// Construir query con filtros
$query = "
    SELECT r.*, 
           c.nombre as cliente_nombre, c.telefono as cliente_telefono,
           u.nombre as barbero_nombre,
           s.nombre as servicio_nombre, s.duracion_minutos, s.precio
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE DATE(r.fecha_inicio) = ?
";

$params = [$filtroFecha];

if ($filtroEstado) {
    $query .= " AND r.estado = ?";
    $params[] = $filtroEstado;
}

if ($filtroBarbero) {
    $query .= " AND r.id_usuario = ?";
    $params[] = $filtroBarbero;
}

$query .= " ORDER BY r.fecha_inicio ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$reservas = $stmt->fetchAll();

// Obtener lista de barberos
$stmtBarberos = $db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'barbero' AND estado = 'activo' ORDER BY nombre");
$barberos = $stmtBarberos->fetchAll();

// Estadísticas del día seleccionado
$stmtStats = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
        SUM(CASE WHEN estado = 'finalizada' THEN 1 ELSE 0 END) as finalizadas,
        SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas
    FROM reservas 
    WHERE DATE(fecha_inicio) = ?
");
$stmtStats->execute([$filtroFecha]);
$stats = $stmtStats->fetch();

$pageTitle = "Reservas";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-calendar-check text-primary"></i> 
                Sistema de Reservas
            </h1>
            <p class="text-muted">Gestiona las citas y reservas de la barbería</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="crear.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Nueva Reserva
            </a>
        </div>
    </div>

    <!-- Estadísticas del día -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total</small>
                            <h3 class="mb-0"><?php echo $stats['total']; ?></h3>
                        </div>
                        <i class="fas fa-calendar fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pendientes</small>
                            <h3 class="mb-0"><?php echo $stats['pendientes']; ?></h3>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Confirmadas</small>
                            <h3 class="mb-0"><?php echo $stats['confirmadas']; ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Finalizadas</small>
                            <h3 class="mb-0"><?php echo $stats['finalizadas']; ?></h3>
                        </div>
                        <i class="fas fa-check-double fa-2x text-info"></i>
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
                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo $filtroFecha; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo $filtroEstado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="confirmada" <?php echo $filtroEstado == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                        <option value="finalizada" <?php echo $filtroEstado == 'finalizada' ? 'selected' : ''; ?>>Finalizada</option>
                        <option value="cancelada" <?php echo $filtroEstado == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Barbero</label>
                    <select name="barbero" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($barberos as $barbero): ?>
                            <option value="<?php echo $barbero['id_usuario']; ?>" 
                                    <?php echo $filtroBarbero == $barbero['id_usuario'] ? 'selected' : ''; ?>>
                                <?php echo $barbero['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> 
                Reservas del <?php echo formatDate($filtroFecha); ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Servicio</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Pagado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($reservas) > 0): ?>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo date('H:i', strtotime($reserva['fecha_inicio'])); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo date('H:i', strtotime($reserva['fecha_fin'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo $reserva['cliente_nombre'] ?? 'Sin nombre'; ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> 
                                            <?php echo $reserva['cliente_telefono'] ?? 'N/A'; ?>
                                        </small>
                                    </td>
                                    <td><?php echo $reserva['barbero_nombre'] ?? 'Sin asignar'; ?></td>
                                    <td><?php echo $reserva['servicio_nombre'] ?? 'N/A'; ?></td>
                                    <td><?php echo $reserva['duracion_minutos']; ?> min</td>
                                    <td><?php echo formatMoney($reserva['precio']); ?></td>
                                    <td>
                                        <span class="badge status-<?php echo $reserva['estado']; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($reserva['pagado']): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Sí
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> No
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="ver.php?id=<?php echo $reserva['id_reserva']; ?>" 
                                               class="btn btn-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?php echo $reserva['id_reserva']; ?>" 
                                               class="btn btn-warning" 
                                               data-bs-toggle="tooltip" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (hasRole(ROLE_ADMIN)): ?>
                                                <a href="#" 
                                                   onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $reserva['id_reserva']; ?>', '¿Eliminar esta reserva?')" 
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
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>No hay reservas para esta fecha</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
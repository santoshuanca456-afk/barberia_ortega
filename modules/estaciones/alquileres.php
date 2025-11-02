<?php
/**
 * Gestión de Alquileres
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Filtros
$filtroEstacion = isset($_GET['estacion']) ? (int)$_GET['estacion'] : 0;
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir query con filtros
$query = "
    SELECT a.*, 
           e.nombre as estacion_nombre,
           u.nombre as usuario_nombre
    FROM alquileres a
    LEFT JOIN estaciones e ON a.id_estacion = e.id_estacion
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE 1=1
";

$params = [];

if ($filtroEstacion) {
    $query .= " AND a.id_estacion = ?";
    $params[] = $filtroEstacion;
}

if ($filtroEstado) {
    $query .= " AND a.estado = ?";
    $params[] = $filtroEstado;
}

$query .= " ORDER BY a.fecha_inicio DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$alquileres = $stmt->fetchAll();

// Obtener lista de estaciones
$stmtEstaciones = $db->query("SELECT id_estacion, nombre FROM estaciones ORDER BY nombre");
$estaciones = $stmtEstaciones->fetchAll();

// Estadísticas
$stmtStats = $db->query("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN estado = 'vigente' THEN 1 END) as vigentes,
        COUNT(CASE WHEN estado = 'vencido' THEN 1 END) as vencidos,
        COUNT(CASE WHEN estado = 'cancelado' THEN 1 END) as cancelados,
        SUM(CASE WHEN estado = 'vigente' THEN monto ELSE 0 END) as ingresos_activos
    FROM alquileres
");
$stats = $stmtStats->fetch();

$pageTitle = "Alquileres";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-file-contract text-primary"></i> 
                Gestión de Alquileres
            </h1>
            <p class="text-muted">Administra los contratos de alquiler de estaciones</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Estaciones
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-file-contract fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0 text-primary"><?php echo $stats['total']; ?></h3>
                    <small class="text-muted">Total Alquileres</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-0 text-success"><?php echo $stats['vigentes']; ?></h3>
                    <small class="text-muted">Vigentes</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h3 class="mb-0 text-danger"><?php echo $stats['vencidos']; ?></h3>
                    <small class="text-muted">Vencidos</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                    <h3 class="mb-0 text-info"><?php echo formatMoney($stats['ingresos_activos']); ?></h3>
                    <small class="text-muted">Ingresos Activos</small>
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
                <div class="col-md-5">
                    <label class="form-label">Estación</label>
                    <select name="estacion" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($estaciones as $estacion): ?>
                            <option value="<?php echo $estacion['id_estacion']; ?>" 
                                    <?php echo $filtroEstacion == $estacion['id_estacion'] ? 'selected' : ''; ?>>
                                <?php echo $estacion['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="vigente" <?php echo $filtroEstado == 'vigente' ? 'selected' : ''; ?>>Vigente</option>
                        <option value="vencido" <?php echo $filtroEstado == 'vencido' ? 'selected' : ''; ?>>Vencido</option>
                        <option value="cancelado" <?php echo $filtroEstado == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
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

    <!-- Tabla de Alquileres -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> Lista de Alquileres
            </h6>
        </div>
        <div class="card-body">
            <?php if (count($alquileres) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estación</th>
                                <th>Arrendatario</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Contrato</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alquileres as $alquiler): ?>
                            <?php
                            $dias_restantes = (strtotime($alquiler['fecha_fin']) - time()) / (60*60*24);
                            $alerta = $dias_restantes <= 7 && $alquiler['estado'] == 'vigente';
                            ?>
                            <tr class="<?php echo $alerta ? 'table-warning' : ''; ?>">
                                <td><strong>#<?php echo $alquiler['id_alquiler']; ?></strong></td>
                                <td>
                                    <i class="fas fa-chair text-primary"></i>
                                    <?php echo $alquiler['estacion_nombre']; ?>
                                </td>
                                <td>
                                    <i class="fas fa-user"></i>
                                    <?php echo $alquiler['usuario_nombre']; ?>
                                </td>
                                <td><?php echo formatDate($alquiler['fecha_inicio']); ?></td>
                                <td>
                                    <?php echo formatDate($alquiler['fecha_fin']); ?>
                                    <?php if ($alerta): ?>
                                        <br><span class="badge bg-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Vence en <?php echo round($dias_restantes); ?> día(s)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo formatMoney($alquiler['monto']); ?></strong></td>
                                <td>
                                    <?php if ($alquiler['estado'] == 'vigente'): ?>
                                        <span class="badge bg-success">Vigente</span>
                                    <?php elseif ($alquiler['estado'] == 'vencido'): ?>
                                        <span class="badge bg-danger">Vencido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Cancelado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($alquiler['contrato_pdf']): ?>
                                        <a href="<?php echo UPLOADS_URL . $alquiler['contrato_pdf']; ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-file-pdf"></i> Ver PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="ver_alquiler.php?id=<?php echo $alquiler['id_alquiler']; ?>" 
                                           class="btn btn-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($alquiler['estado'] == 'vigente' && hasRole(ROLE_ADMIN)): ?>
                                            <a href="#" 
                                               onclick="return confirmarEliminacion('procesar.php?action=finalizar_alquiler&id=<?php echo $alquiler['id_alquiler']; ?>', '¿Finalizar este alquiler?')" 
                                               class="btn btn-danger" 
                                               data-bs-toggle="tooltip" 
                                               title="Finalizar">
                                                <i class="fas fa-stop"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay alquileres registrados con estos filtros</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
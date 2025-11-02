<?php
/**
 * Listado de Servicios
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener todos los servicios
$stmt = $db->query("
    SELECT s.*,
           COUNT(r.id_reserva) as total_reservas,
           SUM(CASE WHEN r.estado = 'finalizada' THEN 1 ELSE 0 END) as reservas_finalizadas
    FROM servicios s
    LEFT JOIN reservas r ON s.id_servicio = r.id_servicio
    GROUP BY s.id_servicio
    ORDER BY s.fecha_registro DESC
");
$servicios = $stmt->fetchAll();

// Estadísticas generales
$stmtStats = $db->query("
    SELECT 
        COUNT(*) as total_servicios,
        AVG(precio) as precio_promedio,
        MIN(precio) as precio_minimo,
        MAX(precio) as precio_maximo,
        AVG(duracion_minutos) as duracion_promedio
    FROM servicios
");
$stats = $stmtStats->fetch();

$pageTitle = "Servicios";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-scissors text-primary"></i> 
                Catálogo de Servicios
            </h1>
            <p class="text-muted">Administra los servicios ofrecidos en la barbería</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="crear.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Nuevo Servicio
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
                            <small class="text-muted text-uppercase">Total Servicios</small>
                            <h3 class="mb-0 text-primary"><?php echo $stats['total_servicios']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-primary"></i>
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
                            <small class="text-muted text-uppercase">Precio Promedio</small>
                            <h3 class="mb-0 text-success"><?php echo formatMoney($stats['precio_promedio']); ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
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
                            <small class="text-muted text-uppercase">Duración Promedio</small>
                            <h3 class="mb-0 text-info"><?php echo round($stats['duracion_promedio']); ?> min</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-info"></i>
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
                            <small class="text-muted text-uppercase">Rango de Precios</small>
                            <h3 class="mb-0 text-warning" style="font-size: 1.3rem;">
                                <?php echo formatMoney($stats['precio_minimo']); ?> - <?php echo formatMoney($stats['precio_maximo']); ?>
                            </h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Servicios -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> Lista de Servicios
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Popularidad</th>
                            <th>Registrado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><strong>#<?php echo $servicio['id_servicio']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-primary me-2">
                                        <i class="fas fa-scissors text-white"></i>
                                    </div>
                                    <strong><?php echo $servicio['nombre']; ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <i class="fas fa-clock"></i> <?php echo $servicio['duracion_minutos']; ?> min
                                </span>
                            </td>
                            <td>
                                <strong class="text-success"><?php echo formatMoney($servicio['precio']); ?></strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <i class="fas fa-chart-bar text-primary"></i>
                                        <?php echo $servicio['reservas_finalizadas']; ?> / <?php echo $servicio['total_reservas']; ?>
                                    </div>
                                    <?php if ($servicio['total_reservas'] > 0): ?>
                                        <div class="progress" style="width: 100px; height: 8px;">
                                            <?php 
                                            $porcentaje = ($servicio['reservas_finalizadas'] / $servicio['total_reservas']) * 100;
                                            ?>
                                            <div class="progress-bar bg-success" 
                                                 style="width: <?php echo $porcentaje; ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo formatDate($servicio['fecha_registro']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="editar.php?id=<?php echo $servicio['id_servicio']; ?>" 
                                       class="btn btn-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (hasRole(ROLE_ADMIN)): ?>
                                        <a href="#" 
                                           onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $servicio['id_servicio']; ?>', '¿Eliminar este servicio?')" 
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

    <!-- Servicios Más Populares -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-star"></i> Top 5 Servicios Más Solicitados
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $stmtTop = $db->query("
                            SELECT s.*, COUNT(r.id_reserva) as total
                            FROM servicios s
                            LEFT JOIN reservas r ON s.id_servicio = r.id_servicio
                            WHERE r.estado = 'finalizada'
                            GROUP BY s.id_servicio
                            ORDER BY total DESC
                            LIMIT 5
                        ");
                        $topServicios = $stmtTop->fetchAll();
                        
                        if (count($topServicios) > 0):
                            foreach ($topServicios as $index => $top):
                        ?>
                            <div class="col-md-2 mb-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <div class="display-4 text-primary">#<?php echo $index + 1; ?></div>
                                        <h6><?php echo $top['nombre']; ?></h6>
                                        <p class="mb-0 text-muted"><?php echo $top['total']; ?> servicios</p>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <div class="col-md-12 text-center text-muted py-4">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <p>No hay datos suficientes para mostrar estadísticas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
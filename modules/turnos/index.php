<?php
/**
 * Listado de Turnos
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Filtros
$filtroFecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$filtroTipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtroCumplido = isset($_GET['cumplido']) ? $_GET['cumplido'] : '';

// Construir query con filtros
$query = "
    SELECT t.*, u.nombre as usuario_nombre
    FROM turnos t
    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
    WHERE t.fecha = ?
";

$params = [$filtroFecha];

if ($filtroTipo) {
    $query .= " AND t.tipo = ?";
    $params[] = $filtroTipo;
}

if ($filtroCumplido !== '') {
    $query .= " AND t.cumplido = ?";
    $params[] = (int)$filtroCumplido;
}

$query .= " ORDER BY 
    CASE 
        WHEN t.tipo = 'apertura' THEN 1
        WHEN t.tipo = 'limpieza' THEN 2
        WHEN t.tipo = 'cierre' THEN 3
    END,
    t.id_turno ASC
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$turnos = $stmt->fetchAll();

// Estadísticas del día
$stmtStats = $db->prepare("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN cumplido = TRUE THEN 1 END) as cumplidos,
        COUNT(CASE WHEN cumplido = FALSE THEN 1 END) as pendientes,
        COUNT(CASE WHEN tipo = 'apertura' THEN 1 END) as apertura,
        COUNT(CASE WHEN tipo = 'cierre' THEN 1 END) as cierre,
        COUNT(CASE WHEN tipo = 'limpieza' THEN 1 END) as limpieza
    FROM turnos 
    WHERE fecha = ?
");
$stmtStats->execute([$filtroFecha]);
$stats = $stmtStats->fetch();

// Obtener usuarios de apoyo
$stmtUsuarios = $db->query("
    SELECT id_usuario, nombre 
    FROM usuarios 
    WHERE (rol = 'apoyo' OR rol = 'administrador' OR rol = 'barbero') 
    AND estado = 'activo' 
    ORDER BY nombre
");
$usuarios = $stmtUsuarios->fetchAll();

$pageTitle = "Turnos";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-clock text-primary"></i> 
                Gestión de Turnos
            </h1>
            <p class="text-muted">Administra turnos de apertura, cierre y limpieza</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAsignarTurno">
                <i class="fas fa-plus"></i> Asignar Turno
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0 text-primary"><?php echo $stats['total']; ?></h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-0 text-success"><?php echo $stats['cumplidos']; ?></h3>
                    <small class="text-muted">Cumplidos</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0 text-warning"><?php echo $stats['pendientes']; ?></h3>
                    <small class="text-muted">Pendientes</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-door-open fa-2x text-info mb-2"></i>
                    <h3 class="mb-0 text-info"><?php echo $stats['apertura']; ?></h3>
                    <small class="text-muted">Apertura</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-danger shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-door-closed fa-2x text-danger mb-2"></i>
                    <h3 class="mb-0 text-danger"><?php echo $stats['cierre']; ?></h3>
                    <small class="text-muted">Cierre</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-secondary shadow-sm h-100">
                <div class="card-body py-3 text-center">
                    <i class="fas fa-broom fa-2x text-secondary mb-2"></i>
                    <h3 class="mb-0 text-secondary"><?php echo $stats['limpieza']; ?></h3>
                    <small class="text-muted">Limpieza</small>
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
                    <label class="form-label">Tipo de Turno</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="apertura" <?php echo $filtroTipo == 'apertura' ? 'selected' : ''; ?>>Apertura</option>
                        <option value="cierre" <?php echo $filtroTipo == 'cierre' ? 'selected' : ''; ?>>Cierre</option>
                        <option value="limpieza" <?php echo $filtroTipo == 'limpieza' ? 'selected' : ''; ?>>Limpieza</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="cumplido" class="form-select">
                        <option value="">Todos</option>
                        <option value="0" <?php echo $filtroCumplido === '0' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="1" <?php echo $filtroCumplido === '1' ? 'selected' : ''; ?>>Cumplido</option>
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

    <!-- Lista de Turnos -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list"></i> 
                Turnos del <?php echo formatDate($filtroFecha); ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (count($turnos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Asignado a</th>
                                <th>Fecha</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turnos as $turno): ?>
                            <tr class="<?php echo !$turno['cumplido'] && $turno['tipo'] == 'cierre' ? 'table-danger' : ''; ?>">
                                <td><strong>#<?php echo $turno['id_turno']; ?></strong></td>
                                <td>
                                    <?php if ($turno['tipo'] == 'apertura'): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-door-open"></i> Apertura
                                        </span>
                                    <?php elseif ($turno['tipo'] == 'cierre'): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-door-closed"></i> Cierre
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-broom"></i> Limpieza
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($turno['usuario_nombre']): ?>
                                        <i class="fas fa-user text-primary"></i> <?php echo $turno['usuario_nombre']; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($turno['fecha']); ?></td>
                                <td>
                                    <?php if ($turno['observaciones']): ?>
                                        <small><?php echo htmlspecialchars($turno['observaciones']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($turno['cumplido']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Cumplido
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if (!$turno['cumplido']): ?>
                                            <a href="procesar.php?action=marcar_cumplido&id=<?php echo $turno['id_turno']; ?>" 
                                               class="btn btn-success" 
                                               data-bs-toggle="tooltip" 
                                               title="Marcar como cumplido">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" 
                                                class="btn btn-warning" 
                                                data-bs-toggle="tooltip" 
                                                title="Editar"
                                                onclick="editarTurno(<?php echo htmlspecialchars(json_encode($turno)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if (hasRole(ROLE_ADMIN)): ?>
                                            <a href="#" 
                                               onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $turno['id_turno']; ?>', '¿Eliminar este turno?')" 
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
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>No hay turnos asignados para esta fecha</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsignarTurno">
                        <i class="fas fa-plus"></i> Asignar Primer Turno
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Asignar Turno -->
<div class="modal fade" id="modalAsignarTurno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Asignar Turno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="procesar.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="crear">
                <input type="hidden" name="id_turno" id="edit_id_turno">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Turno *</label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="apertura">Apertura</option>
                            <option value="cierre">Cierre</option>
                            <option value="limpieza">Limpieza</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Asignar a *</label>
                        <select name="id_usuario" id="id_usuario" class="form-select" required>
                            <option value="">Seleccione un usuario...</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id_usuario']; ?>">
                                    <?php echo $usuario['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha *</label>
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               class="form-control" 
                               value="<?php echo $filtroFecha; ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" 
                                  id="observaciones" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Notas o instrucciones especiales..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarTurno(turno) {
    // Cambiar el action del formulario
    document.querySelector('#modalAsignarTurno form').querySelector('input[name="action"]').value = 'editar';
    
    // Llenar los campos
    document.getElementById('edit_id_turno').value = turno.id_turno;
    document.getElementById('tipo').value = turno.tipo;
    document.getElementById('id_usuario').value = turno.id_usuario;
    document.getElementById('fecha').value = turno.fecha;
    document.getElementById('observaciones').value = turno.observaciones || '';
    
    // Cambiar título del modal
    document.querySelector('#modalAsignarTurno .modal-title').innerHTML = '<i class="fas fa-edit"></i> Editar Turno';
    
    // Abrir modal
    new bootstrap.Modal(document.getElementById('modalAsignarTurno')).show();
}

// Resetear formulario al cerrar modal
document.getElementById('modalAsignarTurno').addEventListener('hidden.bs.modal', function() {
    document.querySelector('#modalAsignarTurno form').reset();
    document.querySelector('#modalAsignarTurno form').querySelector('input[name="action"]').value = 'crear';
    document.getElementById('edit_id_turno').value = '';
    document.querySelector('#modalAsignarTurno .modal-title').innerHTML = '<i class="fas fa-plus-circle"></i> Asignar Turno';
});
</script>

<?php include '../../includes/footer.php'; ?>
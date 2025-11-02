<?php
/**
 * Listado de Estaciones
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener todas las estaciones con información de alquiler actual
$stmt = $db->query("
    SELECT e.*,
           a.id_alquiler,
           a.id_usuario as alquiler_usuario_id,
           a.fecha_inicio as alquiler_inicio,
           a.fecha_fin as alquiler_fin,
           a.monto as alquiler_monto,
           a.estado as alquiler_estado,
           u.nombre as usuario_nombre,
           DATEDIFF(a.fecha_fin, CURDATE()) as dias_restantes
    FROM estaciones e
    LEFT JOIN alquileres a ON e.id_estacion = a.id_estacion 
        AND a.estado = 'vigente'
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    ORDER BY e.id_estacion ASC
");
$estaciones = $stmt->fetchAll();

// Estadísticas generales
$stmtStats = $db->query("
    SELECT 
        COUNT(*) as total_estaciones,
        COUNT(CASE WHEN disponible = TRUE THEN 1 END) as disponibles,
        COUNT(CASE WHEN disponible = FALSE THEN 1 END) as ocupadas
    FROM estaciones
");
$stats = $stmtStats->fetch();

// Alquileres activos
$stmtAlquileres = $db->query("
    SELECT COUNT(*) as total
    FROM alquileres
    WHERE estado = 'vigente'
");
$alquileresActivos = $stmtAlquileres->fetchColumn();

// Alquileres por vencer (próximos 7 días)
$stmtProximos = $db->query("
    SELECT COUNT(*) as total
    FROM alquileres
    WHERE estado = 'vigente' 
    AND fecha_fin <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
");
$alquileresProximos = $stmtProximos->fetchColumn();

$pageTitle = "Estaciones";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-chair text-primary"></i> 
                Gestión de Estaciones
            </h1>
            <p class="text-muted">Administra las estaciones de trabajo y sus alquileres</p>
        </div>
        <div class="col-md-4 text-end">
            <<a href="#" class="btn btn-primary btn-lg" onclick="abrirModalCrear(); return false;">
    <i class="fas fa-plus"></i> Nueva Estación
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
                            <small class="text-muted text-uppercase">Total Estaciones</small>
                            <h3 class="mb-0 text-primary"><?php echo $stats['total_estaciones']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chair fa-2x text-primary"></i>
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
                            <small class="text-muted text-uppercase">Disponibles</small>
                            <h3 class="mb-0 text-success"><?php echo $stats['disponibles']; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
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
                            <small class="text-muted text-uppercase">Alquileres Activos</small>
                            <h3 class="mb-0 text-info"><?php echo $alquileresActivos; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-info"></i>
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
                            <small class="text-muted text-uppercase">Por Vencer (7 días)</small>
                            <h3 class="mb-0 text-warning"><?php echo $alquileresProximos; ?></h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Estaciones -->
    <div class="row">
        <?php foreach ($estaciones as $estacion): ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 <?php echo $estacion['alquiler_estado'] == 'vigente' ? 'border-info' : ''; ?>">
                <div class="card-header <?php echo $estacion['disponible'] ? 'bg-success' : 'bg-secondary'; ?> text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-chair"></i> <?php echo $estacion['nombre']; ?>
                        </h6>
                        <span class="badge <?php echo $estacion['disponible'] ? 'bg-light text-success' : 'bg-danger'; ?>">
                            <?php echo $estacion['disponible'] ? 'Disponible' : 'Ocupada'; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($estacion['descripcion']): ?>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($estacion['descripcion']); ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($estacion['alquiler_estado'] == 'vigente'): ?>
                        <!-- Información de Alquiler Activo -->
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-file-contract"></i> Alquiler Activo
                            </h6>
                            <hr>
                            <p class="mb-1">
                                <strong><i class="fas fa-user"></i> Arrendatario:</strong><br>
                                <?php echo $estacion['usuario_nombre']; ?>
                            </p>
                            <p class="mb-1">
                                <strong><i class="fas fa-calendar-alt"></i> Vigencia:</strong><br>
                                <?php echo formatDate($estacion['alquiler_inicio']); ?> - 
                                <?php echo formatDate($estacion['alquiler_fin']); ?>
                            </p>
                            <p class="mb-1">
                                <strong><i class="fas fa-dollar-sign"></i> Monto:</strong> 
                                <?php echo formatMoney($estacion['alquiler_monto']); ?>
                            </p>
                            <?php if ($estacion['dias_restantes'] <= 7): ?>
                                <div class="alert alert-warning mt-2 mb-0 py-2">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Vence en <?php echo $estacion['dias_restantes']; ?> día(s)</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="alquileres.php?estacion=<?php echo $estacion['id_estacion']; ?>" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver Detalles del Alquiler
                            </a>
                            <a href="alquileres.php?action=renovar&id=<?php echo $estacion['id_alquiler']; ?>" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-redo"></i> Renovar Alquiler
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Sin Alquiler Activo -->
                        <div class="alert alert-light text-center mb-3">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="mb-0 text-muted">Sin alquiler activo</p>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="button" 
                                    class="btn btn-primary btn-sm" 
                                    onclick="asignarAlquiler(<?php echo $estacion['id_estacion']; ?>, '<?php echo $estacion['nombre']; ?>')">
                                <i class="fas fa-file-signature"></i> Crear Alquiler
                            </button>
                            <a href="alquileres.php?estacion=<?php echo $estacion['id_estacion']; ?>" 
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-history"></i> Ver Historial
                            </a>
                        </div>
                    <?php endif; ?>

                    <hr>
                    
                    <!-- Acciones de Estación -->
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-warning btn-sm" 
                                onclick="editarEstacion(<?php echo htmlspecialchars(json_encode($estacion)); ?>)">
                            <i class="fas fa-edit"></i> Editar Estación
                        </button>
                        <button type="button" 
                                class="btn btn-<?php echo $estacion['disponible'] ? 'secondary' : 'success'; ?> btn-sm"
                                onclick="toggleDisponibilidad(<?php echo $estacion['id_estacion']; ?>, <?php echo $estacion['disponible'] ? 'false' : 'true'; ?>)">
                            <i class="fas fa-<?php echo $estacion['disponible'] ? 'ban' : 'check'; ?>"></i> 
                            <?php echo $estacion['disponible'] ? 'Marcar No Disponible' : 'Marcar Disponible'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (count($estaciones) == 0): ?>
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chair fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay estaciones registradas</h4>
                    <p class="text-muted">Comienza agregando la primera estación</p>
                    <a href="#" class="btn btn-primary" onclick="abrirModalCrear(); return false;">
    <i class="fas fa-plus"></i> Crear Primera Estación
</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Crear/Editar Estación -->
<div class="modal fade" id="modalCrearEstacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">
                    <i class="fas fa-plus-circle"></i> Nueva Estación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="procesar.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="crear" id="form_action">
                <input type="hidden" name="id_estacion" id="form_id_estacion">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Estación *</label>
                        <input type="text" 
                               name="nombre" 
                               id="form_nombre" 
                               class="form-control" 
                               placeholder="Ej: Estación 1, Estación Principal"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" 
                                  id="form_descripcion" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Características, ubicación, equipamiento..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="disponible" 
                                   id="form_disponible" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label" for="form_disponible">
                                Estación disponible
                            </label>
                        </div>
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

<!-- Modal Asignar Alquiler -->
<div class="modal fade" id="modalAsignarAlquiler" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-signature"></i> Crear Alquiler - <span id="alquiler_estacion_nombre"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="procesar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="crear_alquiler">
                <input type="hidden" name="id_estacion" id="alquiler_id_estacion">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Barbero/Arrendatario *</label>
                            <select name="id_usuario" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php
                                $stmtBarberos = $db->query("
                                    SELECT id_usuario, nombre 
                                    FROM usuarios 
                                    WHERE rol = 'barbero' AND estado = 'activo' 
                                    ORDER BY nombre
                                ");
                                $barberos = $stmtBarberos->fetchAll();
                                foreach ($barberos as $barbero):
                                ?>
                                    <option value="<?php echo $barbero['id_usuario']; ?>">
                                        <?php echo $barbero['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin *</label>
                            <input type="date" 
                                   name="fecha_fin" 
                                   class="form-control" 
                                   value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>"
                                   required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Monto del Alquiler (Bs) *</label>
                            <input type="number" 
                                   name="monto" 
                                   class="form-control" 
                                   placeholder="1000.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Contrato PDF (Opcional)</label>
                            <input type="file" 
                                   name="contrato_pdf" 
                                   class="form-control" 
                                   accept=".pdf">
                            <small class="text-muted">Tamaño máximo: 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Alquiler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarEstacion(estacion) {
    document.getElementById('form_action').value = 'editar';
    document.getElementById('form_id_estacion').value = estacion.id_estacion;
    document.getElementById('form_nombre').value = estacion.nombre;
    document.getElementById('form_descripcion').value = estacion.descripcion || '';
    document.getElementById('form_disponible').checked = estacion.disponible == 1;
    document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-edit"></i> Editar Estación';
    
    new bootstrap.Modal(document.getElementById('modalCrearEstacion')).show();
}

function asignarAlquiler(id_estacion, nombre) {
    document.getElementById('alquiler_id_estacion').value = id_estacion;
    document.getElementById('alquiler_estacion_nombre').textContent = nombre;
    
    new bootstrap.Modal(document.getElementById('modalAsignarAlquiler')).show();
}

function toggleDisponibilidad(id_estacion, disponible) {
    const mensaje = disponible ? '¿Marcar esta estación como disponible?' : '¿Marcar esta estación como no disponible?';
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `procesar.php?action=toggle_disponibilidad&id=${id_estacion}&disponible=${disponible}`;
        }
    });
}

// Resetear formularios al cerrar modales
document.getElementById('modalCrearEstacion').addEventListener('hidden.bs.modal', function() {
    document.querySelector('#modalCrearEstacion form').reset();
    document.getElementById('form_action').value = 'crear';
    document.getElementById('form_id_estacion').value = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-plus-circle"></i> Nueva Estación';
});
</script>

<?php include '../../includes/footer.php'; ?>
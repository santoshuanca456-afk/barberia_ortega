<?php
/**
 * Editar Servicio
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$id_servicio = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_servicio) {
    setAlert('error', 'Error', 'ID de servicio no válido');
    header('Location: index.php');
    exit();
}

// Obtener datos del servicio
$stmt = $db->prepare("SELECT * FROM servicios WHERE id_servicio = ?");
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch();

if (!$servicio) {
    setAlert('error', 'Error', 'Servicio no encontrado');
    header('Location: index.php');
    exit();
}

// Obtener estadísticas de uso
$stmtStats = $db->prepare("
    SELECT 
        COUNT(*) as total_reservas,
        COUNT(CASE WHEN estado = 'finalizada' THEN 1 END) as completadas
    FROM reservas 
    WHERE id_servicio = ?
");
$stmtStats->execute([$id_servicio]);
$stats = $stmtStats->fetch();

$pageTitle = "Editar Servicio";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-edit text-warning"></i> 
                    Editar Servicio #<?php echo $id_servicio; ?>
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Estadísticas de Uso -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-left-primary shadow-sm">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Total Reservas</small>
                                    <h4 class="mb-0 text-primary"><?php echo $stats['total_reservas']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-success shadow-sm">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Completadas</small>
                                    <h4 class="mb-0 text-success"><?php echo $stats['completadas']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Edición
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formServicio" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" name="id_servicio" value="<?php echo $id_servicio; ?>">

                        <div class="row">
                            <!-- Nombre del Servicio -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-scissors"></i> Nombre del Servicio *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($servicio['nombre']); ?>"
                                       required 
                                       autofocus>
                                <div class="invalid-feedback">El nombre del servicio es obligatorio</div>
                            </div>

                            <!-- Duración -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Duración (minutos) *
                                </label>
                                <input type="number" 
                                       name="duracion_minutos" 
                                       id="duracion_minutos" 
                                       class="form-control" 
                                       value="<?php echo $servicio['duracion_minutos']; ?>"
                                       min="5"
                                       max="300"
                                       step="5"
                                       required>
                                <small class="text-muted">Entre 5 y 300 minutos (5 horas)</small>
                                <div class="invalid-feedback">Ingrese una duración válida</div>
                            </div>

                            <!-- Precio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio (Bs) *
                                </label>
                                <input type="number" 
                                       name="precio" 
                                       id="precio" 
                                       class="form-control" 
                                       value="<?php echo number_format($servicio['precio'], 2, '.', ''); ?>"
                                       min="0"
                                       step="0.01"
                                       required>
                                <small class="text-muted">Precio en Bolivianos</small>
                                <div class="invalid-feedback">Ingrese un precio válido</div>
                            </div>
                        </div>

                        <!-- Vista Previa -->
                        <div class="alert alert-info mt-3" id="preview">
                            <h6><i class="fas fa-eye"></i> Vista Previa</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 id="previewNombre" class="mb-1"><?php echo $servicio['nombre']; ?></h5>
                                                    <span class="badge bg-info" id="previewDuracion">
                                                        <i class="fas fa-clock"></i> <?php echo $servicio['duracion_minutos']; ?> min
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="text-success mb-0" id="previewPrecio"><?php echo formatMoney($servicio['precio']); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advertencia si tiene reservas -->
                        <?php if ($stats['total_reservas'] > 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Advertencia:</strong> Este servicio tiene <?php echo $stats['total_reservas']; ?> reserva(s) asociada(s). 
                            Los cambios afectarán a las reservas existentes.
                        </div>
                        <?php endif; ?>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información del Servicio
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>Registrado:</strong> <?php echo formatDateTime($servicio['fecha_registro']); ?></p>
                    <p><strong>ID del Servicio:</strong> #<?php echo $servicio['id_servicio']; ?></p>
                    <?php if ($stats['total_reservas'] > 0): ?>
                        <p><strong>Tasa de Completitud:</strong> 
                            <?php 
                            $tasa = ($stats['completadas'] / $stats['total_reservas']) * 100;
                            echo round($tasa, 1) . '%';
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Actualizar vista previa en tiempo real
    function actualizarPreview() {
        const nombre = $('#nombre').val();
        const duracion = $('#duracion_minutos').val();
        const precio = $('#precio').val();
        
        $('#previewNombre').text(nombre || 'Nombre del Servicio');
        $('#previewDuracion').html(`<i class="fas fa-clock"></i> ${duracion || 0} min`);
        $('#previewPrecio').text(`Bs ${parseFloat(precio || 0).toFixed(2)}`);
    }
    
    $('#nombre, #duracion_minutos, #precio').on('input', actualizarPreview);
    
    // Validación del formulario
    $('#formServicio').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor complete correctamente todos los campos obligatorios',
                confirmButtonColor: '#4e73df'
            });
        }
        $(this).addClass('was-validated');
    });
    
    // Capitalizar nombre automáticamente
    $('#nombre').on('blur', function() {
        let nombre = $(this).val();
        let nombreCapitalizado = nombre.split(' ')
            .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase())
            .join(' ');
        $(this).val(nombreCapitalizado);
        actualizarPreview();
    });
    
    // Formatear precio automáticamente
    $('#precio').on('blur', function() {
        if ($(this).val()) {
            $(this).val(parseFloat($(this).val()).toFixed(2));
            actualizarPreview();
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
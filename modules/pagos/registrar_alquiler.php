<?php
/**
 * Registrar Pago de Alquiler
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener alquileres vigentes
$stmt = $db->query("
    SELECT a.*, 
           e.nombre as estacion_nombre,
           u.nombre as usuario_nombre,
           DATEDIFF(a.fecha_fin, CURDATE()) as dias_restantes
    FROM alquileres a
    LEFT JOIN estaciones e ON a.id_estacion = e.id_estacion
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE a.estado = 'vigente'
    ORDER BY a.fecha_fin ASC
");
$alquileres = $stmt->fetchAll();

$pageTitle = "Registrar Pago de Alquiler";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-cash-register text-success"></i> 
                    Registrar Pago de Alquiler
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Pago
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formPago" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="registrar_alquiler">

                        <div class="row">
                            <!-- Seleccionar Alquiler -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-file-contract"></i> Seleccionar Alquiler *
                                </label>
                                <select name="id_alquiler" id="id_alquiler" class="form-select" required>
                                    <option value="">Seleccione un alquiler...</option>
                                    <?php foreach ($alquileres as $alquiler): ?>
                                        <option value="<?php echo $alquiler['id_alquiler']; ?>" 
                                                data-monto="<?php echo $alquiler['monto']; ?>"
                                                data-estacion="<?php echo $alquiler['estacion_nombre']; ?>"
                                                data-usuario="<?php echo $alquiler['usuario_nombre']; ?>"
                                                data-inicio="<?php echo $alquiler['fecha_inicio']; ?>"
                                                data-fin="<?php echo $alquiler['fecha_fin']; ?>"
                                                data-dias="<?php echo $alquiler['dias_restantes']; ?>">
                                            #<?php echo $alquiler['id_alquiler']; ?> - 
                                            <?php echo $alquiler['estacion_nombre']; ?> - 
                                            <?php echo $alquiler['usuario_nombre']; ?> - 
                                            <?php echo formatMoney($alquiler['monto']); ?>
                                            <?php if ($alquiler['dias_restantes'] <= 7): ?>
                                                ⚠️ (Vence en <?php echo $alquiler['dias_restantes']; ?> días)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un alquiler</div>
                            </div>

                            <!-- Información del Alquiler -->
                            <div class="col-md-12 mb-3" id="infoAlquiler" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle"></i> Información del Alquiler
                                    </h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Estación:</strong> <span id="info_estacion"></span></p>
                                            <p class="mb-1"><strong>Arrendatario:</strong> <span id="info_usuario"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Período:</strong> <span id="info_periodo"></span></p>
                                            <p class="mb-0"><strong>Vence en:</strong> <span id="info_dias" class="badge bg-warning"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Monto (Bs) *
                                </label>
                                <input type="number" 
                                       name="monto" 
                                       id="monto" 
                                       class="form-control" 
                                       step="0.01"
                                       min="0"
                                       required>
                                <small class="text-muted">Puede modificar el monto si es necesario</small>
                                <div class="invalid-feedback">Ingrese un monto válido</div>
                            </div>

                            <!-- Método de Pago -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card"></i> Método de Pago *
                                </label>
                                <select name="metodo" id="metodo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="tarjeta">💳 Tarjeta</option>
                                    <option value="qr">📱 QR</option>
                                    <option value="otro">💰 Otro</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un método de pago</div>
                            </div>

                            <!-- Concepto -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note"></i> Concepto / Notas
                                </label>
                                <textarea name="concepto" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Pago de alquiler mensual, adelanto, etc."></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alquileres Vigentes -->
            <?php if (count($alquileres) == 0): ?>
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No hay alquileres vigentes</strong>
                <p class="mb-0">No hay contratos de alquiler activos en este momento.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mostrar información al seleccionar alquiler
    $('#id_alquiler').on('change', function() {
        const selected = $(this).find(':selected');
        
        if (selected.val()) {
            const monto = selected.data('monto');
            const estacion = selected.data('estacion');
            const usuario = selected.data('usuario');
            const inicio = selected.data('inicio');
            const fin = selected.data('fin');
            const dias = selected.data('dias');
            
            $('#monto').val(parseFloat(monto).toFixed(2));
            $('#info_estacion').text(estacion);
            $('#info_usuario').text(usuario);
            $('#info_periodo').text(formatearFecha(inicio) + ' - ' + formatearFecha(fin));
            $('#info_dias').text(dias + ' días');
            
            if (dias <= 7) {
                $('#info_dias').removeClass('bg-warning').addClass('bg-danger');
            }
            
            $('#infoAlquiler').fadeIn();
        } else {
            $('#monto').val('');
            $('#infoAlquiler').fadeOut();
        }
    });
    
    // Validación del formulario
    $('#formPago').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor complete todos los campos obligatorios',
                confirmButtonColor: '#4e73df'
            });
        }
        $(this).addClass('was-validated');
    });
    
    function formatearFecha(fecha) {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-BO', { 
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
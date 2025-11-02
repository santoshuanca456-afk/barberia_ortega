<?php
/**
 * Registrar Pago de Servicio
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener reservas sin pagar
$stmt = $db->query("
    SELECT r.*, 
           c.nombre as cliente_nombre,
           u.nombre as barbero_nombre,
           s.nombre as servicio_nombre,
           s.precio
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE r.pagado = FALSE 
    AND r.estado IN ('confirmada', 'finalizada')
    ORDER BY r.fecha_inicio DESC
    LIMIT 50
");
$reservas = $stmt->fetchAll();

$pageTitle = "Registrar Pago de Servicio";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-cash-register text-primary"></i> 
                    Registrar Pago de Servicio
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Pago
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formPago" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="registrar_servicio">

                        <div class="row">
                            <!-- Seleccionar Reserva -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-check"></i> Seleccionar Reserva *
                                </label>
                                <select name="id_reserva" id="id_reserva" class="form-select" required>
                                    <option value="">Seleccione una reserva...</option>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <option value="<?php echo $reserva['id_reserva']; ?>" 
                                                data-precio="<?php echo $reserva['precio']; ?>"
                                                data-cliente="<?php echo $reserva['cliente_nombre']; ?>"
                                                data-servicio="<?php echo $reserva['servicio_nombre']; ?>"
                                                data-barbero="<?php echo $reserva['barbero_nombre']; ?>"
                                                data-fecha="<?php echo $reserva['fecha_inicio']; ?>">
                                            #<?php echo $reserva['id_reserva']; ?> - 
                                            <?php echo $reserva['cliente_nombre']; ?> - 
                                            <?php echo $reserva['servicio_nombre']; ?> - 
                                            <?php echo formatDate($reserva['fecha_inicio']); ?> - 
                                            <?php echo formatMoney($reserva['precio']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione una reserva</div>
                            </div>

                            <!-- Información de la Reserva -->
                            <div class="col-md-12 mb-3" id="infoReserva" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle"></i> Información de la Reserva
                                    </h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Cliente:</strong> <span id="info_cliente"></span></p>
                                            <p class="mb-1"><strong>Servicio:</strong> <span id="info_servicio"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Barbero:</strong> <span id="info_barbero"></span></p>
                                            <p class="mb-1"><strong>Fecha:</strong> <span id="info_fecha"></span></p>
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
                                       required
                                       readonly>
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

                            <!-- Monto Recibido (solo para efectivo) -->
                            <div class="col-md-6 mb-3" id="divMontoRecibido" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-hand-holding-usd"></i> Monto Recibido (Bs)
                                </label>
                                <input type="number" 
                                       name="monto_recibido" 
                                       id="monto_recibido" 
                                       class="form-control" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                                <small class="text-muted">Opcional - Para calcular cambio</small>
                            </div>

                            <!-- Cambio -->
                            <div class="col-md-6 mb-3" id="divCambio" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-exchange-alt"></i> Cambio
                                </label>
                                <input type="text" 
                                       id="cambio" 
                                       class="form-control" 
                                       readonly
                                       style="background-color: #e9ecef; font-weight: bold; color: #28a745;">
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

            <!-- Reservas Pendientes -->
            <?php if (count($reservas) == 0): ?>
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No hay reservas pendientes de pago</strong>
                <p class="mb-0">Todas las reservas confirmadas y finalizadas están pagadas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mostrar información al seleccionar reserva
    $('#id_reserva').on('change', function() {
        const selected = $(this).find(':selected');
        
        if (selected.val()) {
            const precio = selected.data('precio');
            const cliente = selected.data('cliente');
            const servicio = selected.data('servicio');
            const barbero = selected.data('barbero');
            const fecha = selected.data('fecha');
            
            $('#monto').val(parseFloat(precio).toFixed(2));
            $('#info_cliente').text(cliente);
            $('#info_servicio').text(servicio);
            $('#info_barbero').text(barbero);
            $('#info_fecha').text(formatearFecha(fecha));
            $('#infoReserva').fadeIn();
        } else {
            $('#monto').val('');
            $('#infoReserva').fadeOut();
        }
    });
    
    // Mostrar campos adicionales según método de pago
    $('#metodo').on('change', function() {
        if ($(this).val() === 'efectivo') {
            $('#divMontoRecibido, #divCambio').fadeIn();
        } else {
            $('#divMontoRecibido, #divCambio').fadeOut();
            $('#monto_recibido').val('');
            $('#cambio').val('');
        }
    });
    
    // Calcular cambio
    $('#monto_recibido').on('input', function() {
        const monto = parseFloat($('#monto').val()) || 0;
        const recibido = parseFloat($(this).val()) || 0;
        const cambio = recibido - monto;
        
        if (cambio >= 0) {
            $('#cambio').val('Bs ' + cambio.toFixed(2));
            $('#cambio').css('color', '#28a745');
        } else {
            $('#cambio').val('Falta: Bs ' + Math.abs(cambio).toFixed(2));
            $('#cambio').css('color', '#dc3545');
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
        } else {
            // Validar que el monto recibido sea suficiente si es efectivo
            if ($('#metodo').val() === 'efectivo' && $('#monto_recibido').val()) {
                const monto = parseFloat($('#monto').val()) || 0;
                const recibido = parseFloat($('#monto_recibido').val()) || 0;
                
                if (recibido < monto) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Monto insuficiente',
                        text: 'El monto recibido es menor al monto a pagar',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }
            }
        }
        $(this).addClass('was-validated');
    });
    
    function formatearFecha(fecha) {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-BO', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
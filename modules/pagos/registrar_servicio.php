<?php
/**
 * Registrar Pago de Servicio
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener reservas pendientes de pago
$stmt = $db->query("
    SELECT r.id_reserva, 
           c.nombre as cliente_nombre,
           s.nombre as servicio_nombre, 
           s.precio,
           u.nombre as barbero_nombre,
           r.fecha_inicio
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.pagado = FALSE 
    AND r.estado IN ('confirmada', 'finalizada')
    ORDER BY r.fecha_inicio DESC
");
$reservasPendientes = $stmt->fetchAll();

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
                                    <option value="">Seleccione una reserva pendiente...</option>
                                    <?php foreach ($reservasPendientes as $reserva): ?>
                                        <option value="<?php echo $reserva['id_reserva']; ?>" 
                                                data-precio="<?php echo $reserva['precio']; ?>"
                                                data-cliente="<?php echo $reserva['cliente_nombre']; ?>"
                                                data-servicio="<?php echo $reserva['servicio_nombre']; ?>"
                                                data-barbero="<?php echo $reserva['barbero_nombre']; ?>"
                                                data-fecha="<?php echo $reserva['fecha_inicio']; ?>">
                                            #<?php echo $reserva['id_reserva']; ?> - 
                                            <?php echo $reserva['cliente_nombre']; ?> - 
                                            <?php echo $reserva['servicio_nombre']; ?> - 
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
                                            <p class="mb-0"><strong>Fecha:</strong> <span id="info_fecha"></span></p>
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
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Reservas Pendientes -->
            <?php if (count($reservasPendientes) > 0): ?>
            <div class="card shadow mt-4">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="fas fa-clock"></i> Reservas Pendientes de Pago (<?php echo count($reservasPendientes); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Barbero</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservasPendientes as $reserva): ?>
                                <tr>
                                    <td><strong>#<?php echo $reserva['id_reserva']; ?></strong></td>
                                    <td><?php echo $reserva['cliente_nombre']; ?></td>
                                    <td><?php echo $reserva['servicio_nombre']; ?></td>
                                    <td><?php echo $reserva['barbero_nombre']; ?></td>
                                    <td><?php echo formatDateTime($reserva['fecha_inicio']); ?></td>
                                    <td><strong class="text-success"><?php echo formatMoney($reserva['precio']); ?></strong></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="seleccionarReserva(<?php echo $reserva['id_reserva']; ?>)">
                                            <i class="fas fa-check"></i> Seleccionar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No hay reservas pendientes de pago</strong>
                <p class="mb-0">Todas las reservas confirmadas o finalizadas ya están pagadas.</p>
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
            $('#info_fecha').text(formatearFechaHora(fecha));
            
            $('#infoReserva').fadeIn();
        } else {
            $('#monto').val('');
            $('#infoReserva').fadeOut();
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
});

function seleccionarReserva(idReserva) {
    $('#id_reserva').val(idReserva).trigger('change');
    $('html, body').animate({
        scrollTop: $('#formPago').offset().top - 100
    }, 500);
}

function formatearFechaHora(fecha) {
    return new Date(fecha).toLocaleString('es-BO');
}
</script>

<?php include '../../includes/footer.php'; ?>
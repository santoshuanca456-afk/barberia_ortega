<?php
/**
 * Registrar Pago de Servicio
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener reservas pendientes de pago - INCLUYENDO 'pendiente'
$stmt = $db->query("
    SELECT r.id_reserva, 
           c.nombre as cliente_nombre,
           s.nombre as servicio_nombre, 
           s.precio,
           u.nombre as barbero_nombre,
           r.fecha_inicio,
           r.estado,
           r.pagado
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.pagado = FALSE 
    AND r.estado IN ('confirmada', 'finalizada', 'pendiente')
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

            <?php if (count($reservasPendientes) > 0): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>¡Perfecto!</strong> Se encontraron <?php echo count($reservasPendientes); ?> reservas pendientes de pago.
            </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Pago
                        <?php if (count($reservasPendientes) > 0): ?>
                            <span class="badge bg-success ms-2"><?php echo count($reservasPendientes); ?> disponibles</span>
                        <?php endif; ?>
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
                                    <?php if (count($reservasPendientes) > 0): ?>
                                        <?php foreach ($reservasPendientes as $reserva): ?>
                                            <option value="<?php echo $reserva['id_reserva']; ?>" 
                                                    data-precio="<?php echo $reserva['precio']; ?>"
                                                    data-cliente="<?php echo htmlspecialchars($reserva['cliente_nombre']); ?>"
                                                    data-servicio="<?php echo htmlspecialchars($reserva['servicio_nombre']); ?>"
                                                    data-barbero="<?php echo htmlspecialchars($reserva['barbero_nombre']); ?>"
                                                    data-fecha="<?php echo $reserva['fecha_inicio']; ?>"
                                                    data-estado="<?php echo $reserva['estado']; ?>">
                                                #<?php echo $reserva['id_reserva']; ?> - 
                                                <?php echo $reserva['cliente_nombre']; ?> - 
                                                <?php echo $reserva['servicio_nombre']; ?> - 
                                                <?php echo formatMoney($reserva['precio']); ?>
                                                (<?php echo ucfirst($reserva['estado']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay reservas pendientes de pago</option>
                                    <?php endif; ?>
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
                                            <p class="mb-0"><strong>Precio Servicio:</strong> <span id="info_precio_servicio" class="text-success fw-bold"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Barbero:</strong> <span id="info_barbero"></span></p>
                                            <p class="mb-1"><strong>Fecha:</strong> <span id="info_fecha"></span></p>
                                            <p class="mb-0"><strong>Estado:</strong> <span id="info_estado" class="badge"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Monto a Pagar (Bs) *
                                </label>
                                <input type="number" 
                                       name="monto" 
                                       id="monto" 
                                       class="form-control" 
                                       step="0.01"
                                       min="0"
                                       required
                                       placeholder="0.00">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    El monto se completa automáticamente con el precio del servicio
                                </small>
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
                            <button type="submit" class="btn btn-primary btn-lg" <?php echo count($reservasPendientes) == 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-check"></i> Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Reservas Pendientes -->
            <?php if (count($reservasPendientes) > 0): ?>
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
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
                                    <th>Estado</th>
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
                                        <span class="badge bg-<?php 
                                            echo $reserva['estado'] == 'confirmada' ? 'warning' : 
                                                 ($reserva['estado'] == 'finalizada' ? 'success' : 
                                                 ($reserva['estado'] == 'pendiente' ? 'secondary' : 'info')); 
                                        ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// MÉTODO 1: Usando jQuery con múltiples eventos
$(document).ready(function() {
    console.log('=== INICIANDO SCRIPT DE MONTO AUTOMÁTICO ===');
    console.log('Reservas pendientes: <?php echo count($reservasPendientes); ?>');
    
    // MÉTODO 1A: Evento change del select
    $('#id_reserva').on('change', function() {
        console.log('🔹 MÉTODO 1A: Evento change activado');
        actualizarMontoDesdeSelect();
    });
    
    // MÉTODO 1B: Evento click en cualquier parte del select
    $('#id_reserva').on('click', function() {
        console.log('🔹 MÉTODO 1B: Evento click activado');
        // Forzar actualización después de un pequeño delay para que el cambio se complete
        setTimeout(actualizarMontoDesdeSelect, 100);
    });
    
    // MÉTODO 1C: Evento input (para cambios rápidos)
    $('#id_reserva').on('input', function() {
        console.log('🔹 MÉTODO 1C: Evento input activado');
        actualizarMontoDesdeSelect();
    });
    
    // MÉTODO 2: Usando eventos nativos de JavaScript
    document.getElementById('id_reserva').addEventListener('change', function() {
        console.log('🔹 MÉTODO 2: Evento nativo change activado');
        actualizarMontoDesdeSelect();
    });
    
    // MÉTODO 3: Verificación periódica (fallback)
    setInterval(function() {
        const select = document.getElementById('id_reserva');
        const monto = document.getElementById('monto');
        if (select.value && (!monto.value || monto.value == '0.00')) {
            console.log('🔹 MÉTODO 3: Verificación periódica - monto vacío, actualizando...');
            actualizarMontoDesdeSelect();
        }
    }, 500);
    
    // MÉTODO 4: Forzar actualización al hacer hover en las filas de la tabla
    $('.btn-outline-primary').hover(function() {
        console.log('🔹 MÉTODO 4: Hover en botón seleccionar');
        // No actualizamos aquí, solo preparamos
    });
    
    function actualizarMontoDesdeSelect() {
        const selected = $('#id_reserva').find(':selected');
        console.log('🔄 Actualizando monto...');
        console.log('Selected option:', selected.val());
        
        if (selected.val() && selected.val() !== '') {
            const precio = selected.data('precio');
            const cliente = selected.data('cliente');
            const servicio = selected.data('servicio');
            const barbero = selected.data('barbero');
            const fecha = selected.data('fecha');
            const estado = selected.data('estado');
            
            console.log('📊 Datos obtenidos:', { 
                precio: precio, 
                cliente: cliente, 
                servicio: servicio,
                barbero: barbero,
                fecha: fecha,
                estado: estado
            });
            
            // MÉTODO A: Usando jQuery
            $('#monto').val(parseFloat(precio).toFixed(2));
            
            // MÉTODO B: Usando JavaScript nativo (backup)
            document.getElementById('monto').value = parseFloat(precio).toFixed(2);
            
            // MÉTODO C: Forzar el evento input
            $('#monto').trigger('input');
            
            console.log('✅ Monto actualizado a:', parseFloat(precio).toFixed(2));
            console.log('✅ Valor actual en campo monto:', $('#monto').val());
            
            // Actualizar información de la reserva
            $('#info_cliente').text(cliente);
            $('#info_servicio').text(servicio);
            $('#info_barbero').text(barbero);
            $('#info_fecha').text(formatearFechaHora(fecha));
            $('#info_precio_servicio').text('Bs ' + parseFloat(precio).toFixed(2));
            
            // Mostrar estado con badge de color
            const estadoBadge = $('#info_estado');
            estadoBadge.text(estado);
            estadoBadge.removeClass('bg-warning bg-success bg-secondary bg-info');
            
            if (estado === 'confirmada') {
                estadoBadge.addClass('bg-warning');
            } else if (estado === 'finalizada') {
                estadoBadge.addClass('bg-success');
            } else if (estado === 'pendiente') {
                estadoBadge.addClass('bg-secondary');
            } else {
                estadoBadge.addClass('bg-info');
            }
            
            $('#infoReserva').fadeIn();
        } else {
            console.log('❌ No hay reserva seleccionada, limpiando campos...');
            $('#monto').val('');
            $('#infoReserva').fadeOut();
        }
    }
    
    // Validación del formulario
    $('#formPago').on('submit', function(e) {
        console.log('📝 Validando formulario...');
        
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            console.log('❌ Formulario inválido');
            
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor complete todos los campos obligatorios',
                confirmButtonColor: '#4e73df'
            });
        } else {
            const monto = parseFloat($('#monto').val());
            console.log('💰 Monto a validar:', monto);
            
            if (monto <= 0) {
                e.preventDefault();
                console.log('❌ Monto inválido:', monto);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Monto inválido',
                    text: 'El monto debe ser mayor a cero',
                    confirmButtonColor: '#4e73df'
                });
            } else {
                console.log('✅ Formulario válido, enviando...');
            }
        }
        $(this).addClass('was-validated');
    });
    
    // Inicializar si hay solo una reserva
    <?php if (count($reservasPendientes) == 1): ?>
        console.log('🎯 Solo una reserva, seleccionando automáticamente...');
        setTimeout(function() {
            $('#id_reserva').val(<?php echo $reservasPendientes[0]['id_reserva']; ?>).trigger('change');
        }, 500);
    <?php endif; ?>
});

// MÉTODO 5: Función para seleccionar desde la tabla
function seleccionarReserva(idReserva) {
    console.log('🎯 Seleccionando reserva desde tabla:', idReserva);
    
    // MÉTODO A: Usando jQuery
    $('#id_reserva').val(idReserva);
    
    // MÉTODO B: Usando JavaScript nativo
    document.getElementById('id_reserva').value = idReserva;
    
    // MÉTODO C: Disparar múltiples eventos
    $('#id_reserva').trigger('change');
    $('#id_reserva').trigger('input');
    $('#id_reserva').trigger('click');
    
    // MÉTODO D: Forzar actualización después de un delay
    setTimeout(function() {
        const selected = $('#id_reserva').find(':selected');
        if (selected.val() == idReserva) {
            const precio = selected.data('precio');
            console.log('💰 Forzando monto a:', precio);
            $('#monto').val(parseFloat(precio).toFixed(2));
            document.getElementById('monto').value = parseFloat(precio).toFixed(2);
        }
    }, 200);
    
    $('html, body').animate({
        scrollTop: $('#formPago').offset().top - 100
    }, 500);
}

function formatearFechaHora(fecha) {
    if (!fecha) return 'N/A';
    try {
        return new Date(fecha).toLocaleString('es-BO');
    } catch (e) {
        console.error('Error formateando fecha:', e);
        return fecha;
    }
}

// MÉTODO 6: Debug manual - función para probar
function debugMonto() {
    console.log('=== DEBUG MANUAL ===');
    const select = $('#id_reserva');
    const selected = select.find(':selected');
    console.log('Select value:', select.val());
    console.log('Selected option:', selected);
    console.log('Precio data:', selected.data('precio'));
    console.log('Monto field value:', $('#monto').val());
    
    if (selected.val()) {
        const precio = selected.data('precio');
        $('#monto').val(parseFloat(precio).toFixed(2));
        console.log('Monto actualizado manualmente a:', precio);
    }
}

// Ejecutar debug al cargar la página
$(document).ready(function() {
    console.log('=== PÁGINA CARGADA ===');
    // Forzar una verificación inicial después de 1 segundo
    setTimeout(function() {
        const select = document.getElementById('id_reserva');
        if (select.value) {
            console.log('🔄 Verificación inicial - hay reserva seleccionada');
            actualizarMontoDesdeSelect();
        }
    }, 1000);
});
</script>

<?php include '../../includes/footer.php'; ?>
<?php
/**
 * Registrar Pago Ordinario (Sin Reserva)
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener servicios activos
$stmtServicios = $db->query("
    SELECT id_servicio, nombre, precio, duracion_minutos 
    FROM servicios 
    WHERE estado = 'activo'
    ORDER BY nombre
");
$servicios = $stmtServicios->fetchAll();

// Obtener barberos activos
$stmtBarberos = $db->query("
    SELECT id_usuario, nombre 
    FROM usuarios 
    WHERE rol = 'barbero' AND estado = 'activo'
    ORDER BY nombre
");
$barberos = $stmtBarberos->fetchAll();

$pageTitle = "Registrar Pago Ordinario";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-walkie-talkie text-success"></i> 
                    Registrar Pago Ordinario
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Pago Ordinario
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formPagoOrdinario" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="registrar_ordinario">

                        <div class="row">
                            <!-- Información del Cliente -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2">
                                    <i class="fas fa-user"></i> Información del Cliente
                                </h6>
                                
                                <!-- Tipo de Cliente -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="fas fa-users"></i> Tipo de Cliente *
                                        </label>
                                        <select name="tipo_cliente" id="tipo_cliente" class="form-select" required>
                                            <option value="">Seleccione...</option>
                                            <option value="nuevo">👤 Cliente Nuevo</option>
                                            <option value="existente">👥 Cliente Existente</option>
                                        </select>
                                        <div class="invalid-feedback">Seleccione el tipo de cliente</div>
                                    </div>
                                </div>

                                <!-- Campos para Cliente Existente -->
                                <div id="clienteExistente" class="row" style="display: none;">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-search"></i> Buscar Cliente
                                        </label>
                                        <select name="id_cliente" id="id_cliente" class="form-select">
                                            <option value="">Seleccione un cliente...</option>
                                        </select>
                                        <small class="text-muted">Seleccione un cliente existente de la base de datos</small>
                                    </div>
                                </div>

                                <!-- Campos para Cliente Nuevo -->
                                <div id="clienteNuevo" class="row" style="display: none;">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i> Nombre del Cliente *
                                        </label>
                                        <input type="text" name="cliente_nombre" id="cliente_nombre" class="form-control" placeholder="Ingrese el nombre completo">
                                        <div class="invalid-feedback">Ingrese el nombre del cliente</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-phone"></i> Teléfono
                                        </label>
                                        <input type="text" name="cliente_telefono" id="cliente_telefono" class="form-control" placeholder="Ej: 61234567">
                                        <small class="text-muted">Opcional - 8 dígitos comenzando con 6 o 7</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Servicio -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2">
                                    <i class="fas fa-scissors"></i> Información del Servicio
                                </h6>
                                
                                <div class="row">
                                    <!-- Servicio -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-cut"></i> Servicio *
                                        </label>
                                        <select name="id_servicio" id="id_servicio" class="form-select" required>
                                            <option value="">Seleccione un servicio...</option>
                                            <?php foreach ($servicios as $servicio): ?>
                                                <option value="<?php echo $servicio['id_servicio']; ?>" 
                                                        data-precio="<?php echo $servicio['precio']; ?>"
                                                        data-duracion="<?php echo $servicio['duracion_minutos']; ?>">
                                                    <?php echo $servicio['nombre']; ?> - 
                                                    <?php echo formatMoney($servicio['precio']); ?> 
                                                    (<?php echo $servicio['duracion_minutos']; ?> min)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Seleccione un servicio</div>
                                    </div>

                                    <!-- Barbero -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user-tie"></i> Barbero *
                                        </label>
                                        <select name="id_barbero" id="id_barbero" class="form-select" required>
                                            <option value="">Seleccione un barbero...</option>
                                            <?php foreach ($barberos as $barbero): ?>
                                                <option value="<?php echo $barbero['id_usuario']; ?>">
                                                    <?php echo $barbero['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Seleccione un barbero</div>
                                    </div>
                                </div>

                                <!-- Información del Servicio Seleccionado -->
                                <div class="row" id="infoServicio" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>Servicio:</strong> <span id="info_nombre_servicio"></span></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>Duración:</strong> <span id="info_duracion"></span> minutos</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>Precio:</strong> <span id="info_precio" class="text-success fw-bold"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Pago -->
                            <div class="col-md-12 mb-4">
                                <h6 class="border-bottom pb-2">
                                    <i class="fas fa-money-bill-wave"></i> Información del Pago
                                </h6>
                                
                                <div class="row">
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

                                <!-- Observaciones -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-sticky-note"></i> Observaciones
                                        </label>
                                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales sobre el servicio..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen del Pago -->
                        <div class="card bg-light mb-4" id="resumenPago" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-receipt"></i> Resumen del Pago
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Cliente:</strong> <span id="resumen_cliente">-</span></p>
                                        <p class="mb-1"><strong>Servicio:</strong> <span id="resumen_servicio">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Barbero:</strong> <span id="resumen_barbero">-</span></p>
                                        <p class="mb-0"><strong>Total a Pagar:</strong> <span id="resumen_total" class="text-success fw-bold fs-5">Bs 0.00</span></p>
                                    </div>
                                </div>
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
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Manejar cambio de tipo de cliente
    $('#tipo_cliente').on('change', function() {
        const tipo = $(this).val();
        
        // Ocultar ambos paneles primero
        $('#clienteExistente').hide();
        $('#clienteNuevo').hide();
        
        // Mostrar el panel correspondiente
        if (tipo === 'existente') {
            $('#clienteExistente').show();
            cargarClientes();
        } else if (tipo === 'nuevo') {
            $('#clienteNuevo').show();
        }
        
        actualizarResumen();
    });

    // Cargar clientes existentes
    function cargarClientes() {
        $.ajax({
            url: '../../ajax/get_clientes.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const select = $('#id_cliente');
                select.empty().append('<option value="">Seleccione un cliente...</option>');
                
                data.forEach(function(cliente) {
                    let texto = cliente.nombre;
                    if (cliente.telefono) {
                        texto += ' - ' + cliente.telefono;
                    }
                    select.append($('<option>', {
                        value: cliente.id_cliente,
                        text: texto,
                        'data-nombre': cliente.nombre
                    }));
                });
            },
            error: function() {
                console.error('Error al cargar clientes');
            }
        });
    }

    // Actualizar monto automáticamente cuando se selecciona un servicio
    $('#id_servicio').on('change', function() {
        const selected = $(this).find(':selected');
        const precio = selected.data('precio');
        const duracion = selected.data('duracion');
        
        if (precio) {
            $('#monto').val(parseFloat(precio).toFixed(2));
            
            // Mostrar información del servicio
            $('#info_nombre_servicio').text(selected.text().split(' - ')[0]);
            $('#info_duracion').text(duracion);
            $('#info_precio').text('Bs ' + parseFloat(precio).toFixed(2));
            $('#infoServicio').show();
        } else {
            $('#infoServicio').hide();
        }
        
        actualizarResumen();
    });

    // Actualizar resumen cuando cambian los campos
    $('#id_cliente, #cliente_nombre, #id_barbero').on('change input', function() {
        actualizarResumen();
    });

    // Función para actualizar el resumen del pago
    function actualizarResumen() {
        let cliente = '-';
        let servicio = '-';
        let barbero = '-';
        let total = 'Bs 0.00';
        
        // Obtener información del cliente
        const tipoCliente = $('#tipo_cliente').val();
        if (tipoCliente === 'existente') {
            const selectedCliente = $('#id_cliente').find(':selected');
            cliente = selectedCliente.data('nombre') || '-';
        } else if (tipoCliente === 'nuevo') {
            cliente = $('#cliente_nombre').val() || '-';
        }
        
        // Obtener información del servicio
        const selectedServicio = $('#id_servicio').find(':selected');
        if (selectedServicio.val()) {
            servicio = selectedServicio.text().split(' - ')[0];
            total = 'Bs ' + $('#monto').val();
        }
        
        // Obtener información del barbero
        const selectedBarbero = $('#id_barbero').find(':selected');
        if (selectedBarbero.val()) {
            barbero = selectedBarbero.text();
        }
        
        // Actualizar resumen
        $('#resumen_cliente').text(cliente);
        $('#resumen_servicio').text(servicio);
        $('#resumen_barbero').text(barbero);
        $('#resumen_total').text(total);
        
        // Mostrar/ocultar resumen
        if (cliente !== '-' && servicio !== '-' && barbero !== '-') {
            $('#resumenPago').show();
        } else {
            $('#resumenPago').hide();
        }
    }

    // Validación del formulario
    $('#formPagoOrdinario').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor complete todos los campos obligatorios',
                confirmButtonColor: '#1cc88a'
            });
        } else {
            const monto = parseFloat($('#monto').val());
            
            if (monto <= 0) {
                e.preventDefault();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Monto inválido',
                    text: 'El monto debe ser mayor a cero',
                    confirmButtonColor: '#1cc88a'
                });
            }
        }
        $(this).addClass('was-validated');
    });

    // Validación de teléfono
    $('#cliente_telefono').on('input', function() {
        const telefono = $(this).val();
        if (telefono && !/^[67]\d{0,7}$/.test(telefono)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
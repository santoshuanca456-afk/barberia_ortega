<?php
/**
 * Crear Nueva Reserva
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

// Obtener clientes
$stmtClientes = $db->query("SELECT id_cliente, nombre, telefono FROM clientes ORDER BY nombre");
$clientes = $stmtClientes->fetchAll();

// Obtener barberos
$stmtBarberos = $db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'barbero' AND estado = 'activo' ORDER BY nombre");
$barberos = $stmtBarberos->fetchAll();

// Obtener servicios
$stmtServicios = $db->query("SELECT id_servicio, nombre, duracion_minutos, precio FROM servicios ORDER BY nombre");
$servicios = $stmtServicios->fetchAll();

$pageTitle = "Nueva Reserva";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calendar-plus text-primary"></i> 
                    Nueva Reserva
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Reserva
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formReserva" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <!-- Cliente -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Cliente *
                                </label>
                                <div class="input-group">
                                    <select name="id_cliente" id="id_cliente" class="form-select" required>
                                        <option value="">Seleccione un cliente</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?php echo $cliente['id_cliente']; ?>">
                                                <?php echo $cliente['nombre']; ?> 
                                                <?php if ($cliente['telefono']): ?>
                                                    - <?php echo $cliente['telefono']; ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="../clientes/crear.php?redirect=reservas" class="btn btn-success" data-bs-toggle="tooltip" title="Nuevo cliente">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <div class="invalid-feedback">Seleccione un cliente</div>
                            </div>

                            <!-- Barbero -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-tie"></i> Barbero *
                                </label>
                                <select name="id_usuario" id="id_usuario" class="form-select" required>
                                    <option value="">Seleccione un barbero</option>
                                    <?php foreach ($barberos as $barbero): ?>
                                        <option value="<?php echo $barbero['id_usuario']; ?>">
                                            <?php echo $barbero['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un barbero</div>
                            </div>

                            <!-- Servicio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-scissors"></i> Servicio *
                                </label>
                                <select name="id_servicio" id="id_servicio" class="form-select" required>
                                    <option value="">Seleccione un servicio</option>
                                    <?php foreach ($servicios as $servicio): ?>
                                        <option value="<?php echo $servicio['id_servicio']; ?>" 
                                                data-duracion="<?php echo $servicio['duracion_minutos']; ?>"
                                                data-precio="<?php echo $servicio['precio']; ?>">
                                            <?php echo $servicio['nombre']; ?> 
                                            (<?php echo $servicio['duracion_minutos']; ?> min - <?php echo formatMoney($servicio['precio']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un servicio</div>
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha *
                                </label>
                                <input type="date" name="fecha" id="fecha" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       value="<?php echo date('Y-m-d'); ?>" 
                                       required>
                                <div class="invalid-feedback">Ingrese una fecha</div>
                            </div>

                            <!-- Hora Inicio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Inicio *
                                </label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" 
                                       min="08:00" max="20:00" required>
                                <small class="text-muted">Horario: 08:00 - 20:00</small>
                                <div class="invalid-feedback">Ingrese una hora válida</div>
                            </div>

                            <!-- Hora Fin (calculada automáticamente) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Fin
                                </label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control" readonly>
                                <small class="text-muted">Se calcula automáticamente según el servicio</small>
                            </div>

                            <!-- Notas -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note"></i> Notas / Observaciones
                                </label>
                                <textarea name="notas" class="form-control" rows="3" 
                                          placeholder="Preferencias, alergias, detalles especiales..."></textarea>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag"></i> Estado
                                </label>
                                <select name="estado" class="form-select">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada" selected>Confirmada</option>
                                </select>
                            </div>

                            <!-- Pagado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill"></i> Estado de Pago
                                </label>
                                <select name="pagado" class="form-select">
                                    <option value="0" selected>No Pagado</option>
                                    <option value="1">Pagado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="alert alert-info mt-3" id="resumen" style="display: none;">
                            <h6><i class="fas fa-info-circle"></i> Resumen de la Reserva</h6>
                            <div id="resumenContenido"></div>
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Disponibilidad del día -->
            <div class="card shadow mt-4" id="cardDisponibilidad" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-day"></i> Disponibilidad
                    </h6>
                </div>
                <div class="card-body">
                    <div id="disponibilidadContenido">
                        <p class="text-muted">Seleccione una fecha y un barbero para ver la disponibilidad</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Calcular hora fin automáticamente
    $('#id_servicio, #hora_inicio').on('change', function() {
        calcularHoraFin();
        mostrarResumen();
    });
    
    // Actualizar resumen cuando cambian los campos
    $('#id_cliente, #id_usuario, #fecha').on('change', function() {
        mostrarResumen();
        cargarDisponibilidad();
    });
    
    function calcularHoraFin() {
        const servicioSelect = document.getElementById('id_servicio');
        const horaInicio = document.getElementById('hora_inicio').value;
        
        if (servicioSelect.value && horaInicio) {
            const duracion = parseInt(servicioSelect.options[servicioSelect.selectedIndex].dataset.duracion);
            
            if (duracion) {
                const [horas, minutos] = horaInicio.split(':');
                const fecha = new Date();
                fecha.setHours(parseInt(horas));
                fecha.setMinutes(parseInt(minutos) + duracion);
                
                const horaFin = fecha.getHours().toString().padStart(2, '0') + ':' + 
                               fecha.getMinutes().toString().padStart(2, '0');
                
                document.getElementById('hora_fin').value = horaFin;
            }
        }
    }
    
    function mostrarResumen() {
        const cliente = $('#id_cliente option:selected').text();
        const barbero = $('#id_usuario option:selected').text();
        const servicio = $('#id_servicio option:selected').text();
        const fecha = $('#fecha').val();
        const horaInicio = $('#hora_inicio').val();
        const horaFin = $('#hora_fin').val();
        
        if (cliente && barbero && servicio && fecha && horaInicio) {
            const servicioSelect = document.getElementById('id_servicio');
            const precio = servicioSelect.options[servicioSelect.selectedIndex].dataset.precio;
            
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> ${cliente}<br>
                        <strong>Barbero:</strong> ${barbero}<br>
                        <strong>Servicio:</strong> ${servicio}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> ${formatearFecha(fecha)}<br>
                        <strong>Horario:</strong> ${horaInicio} - ${horaFin}<br>
                        <strong>Precio:</strong> Bs ${parseFloat(precio).toFixed(2)}
                    </div>
                </div>
            `;
            
            $('#resumenContenido').html(html);
            $('#resumen').fadeIn();
        }
    }
    
    function cargarDisponibilidad() {
        const fecha = $('#fecha').val();
        const barbero = $('#id_usuario').val();
        
        if (fecha && barbero) {
            $('#cardDisponibilidad').show();
            $('#disponibilidadContenido').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
            
            $.ajax({
                url: 'disponibilidad.php',
                method: 'GET',
                data: { fecha: fecha, barbero: barbero },
                success: function(response) {
                    $('#disponibilidadContenido').html(response);
                },
                error: function() {
                    $('#disponibilidadContenido').html('<p class="text-danger">Error al cargar disponibilidad</p>');
                }
            });
        }
    }
    
    function formatearFecha(fecha) {
        const [year, month, day] = fecha.split('-');
        return `${day}/${month}/${year}`;
    }
    
    // Validación del formulario
    $('#formReserva').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
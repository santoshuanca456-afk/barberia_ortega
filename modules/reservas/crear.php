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
                    <form id="formReserva" action="procesar.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <!-- Cliente -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Cliente *
                                </label>
                                <div class="input-group">
                                    <select name="id_cliente" id="id_cliente" class="form-select">
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
                            </div>

                            <!-- Barbero -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-tie"></i> Barbero *
                                </label>
                                <select name="id_usuario" id="id_usuario" class="form-select">
                                    <option value="">Seleccione un barbero</option>
                                    <?php foreach ($barberos as $barbero): ?>
                                        <option value="<?php echo $barbero['id_usuario']; ?>">
                                            <?php echo $barbero['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Servicio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-scissors"></i> Servicio *
                                </label>
                                <select name="id_servicio" id="id_servicio" class="form-select">
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
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha *
                                </label>
                                <input type="date" name="fecha" id="fecha" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <!-- Hora Inicio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Inicio *
                                </label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" 
                                       min="08:00" max="20:00" step="900">
                                <small class="text-muted">Horario: 08:00 - 20:00 (intervalos de 15 min)</small>
                            </div>

                            <!-- Hora Fin (calculada automáticamente) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Fin *
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
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
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
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
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
    
    // Validar formulario antes de enviar
    $('#formReserva').on('submit', function(e) {
        e.preventDefault();
        
        if (validarFormulario()) {
            // Deshabilitar botón y mostrar estado de carga
            $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            
            // Mostrar notificación de procesamiento por esquina
            Swal.fire({
                title: 'Procesando reserva...',
                icon: 'info',
                position: 'top-end',
                toast: true,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                customClass: {
                    popup: 'animated bounceInRight'
                }
            });
            
            // Enviar formulario después de un breve delay para que se vea el mensaje
            setTimeout(() => {
                document.getElementById('formReserva').submit();
            }, 1000);
        }
    });
    
    function calcularHoraFin() {
        const servicioSelect = document.getElementById('id_servicio');
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFinInput = document.getElementById('hora_fin');
        
        if (servicioSelect.value && horaInicio) {
            const duracion = parseInt(servicioSelect.options[servicioSelect.selectedIndex].dataset.duracion);
            
            if (duracion && duracion > 0) {
                const [horas, minutos] = horaInicio.split(':');
                const fecha = new Date();
                fecha.setHours(parseInt(horas));
                fecha.setMinutes(parseInt(minutos) + duracion);
                
                const horaFinCalculada = fecha.getHours().toString().padStart(2, '0') + ':' + 
                                       fecha.getMinutes().toString().padStart(2, '0');
                
                // Validar que no exceda el horario de cierre (20:00)
                if (fecha.getHours() < 20 || (fecha.getHours() === 20 && fecha.getMinutes() === 0)) {
                    horaFinInput.value = horaFinCalculada;
                } else {
                    horaFinInput.value = '';
                    // NOTIFICACIÓN POR ESQUINA - SweetAlert2 toast
                    Swal.fire({
                        title: 'Horario no disponible',
                        text: 'El servicio no puede terminar después de las 20:00. Seleccione una hora más temprana.',
                        icon: 'warning',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'animated bounceInRight'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }
            } else {
                horaFinInput.value = '';
            }
        } else {
            horaFinInput.value = '';
        }
    }
    
    function validarFormulario() {
        // SOLO CAMPOS OBLIGATORIOS - EXCLUYENDO ESTADO Y PAGADO
        const campos = {
            'id_cliente': 'Cliente',
            'id_usuario': 'Barbero',
            'id_servicio': 'Servicio',
            'fecha': 'Fecha',
            'hora_inicio': 'Hora de inicio',
            'hora_fin': 'Hora de fin'
            // NOTA: 'estado' y 'pagado' han sido removidos de la validación
        };
        
        let errores = [];
        
        // Validar campos obligatorios (solo los especificados arriba)
        for (const [campoId, campoNombre] of Object.entries(campos)) {
            const valor = $(`#${campoId}`).val();
            if (!valor || valor === '') {
                errores.push(`${campoNombre}`);
                
                // Resaltar campo con error
                $(`#${campoId}`).addClass('is-invalid');
            } else {
                $(`#${campoId}`).removeClass('is-invalid').addClass('is-valid');
            }
        }
        
        // Validaciones específicas
        const fecha = $('#fecha').val();
        const hoy = new Date().toISOString().split('T')[0];
        if (fecha < hoy) {
            errores.push('La fecha no puede ser anterior al día de hoy');
            $('#fecha').addClass('is-invalid');
        }
        
        const horaInicio = $('#hora_inicio').val();
        if (horaInicio) {
            const [horas, minutos] = horaInicio.split(':');
            if (horas < 8 || horas >= 20) {
                errores.push('La hora de inicio debe estar entre las 08:00 y 20:00');
                $('#hora_inicio').addClass('is-invalid');
            }
        }
        
        // Validar que la hora fin esté calculada
        const horaFin = $('#hora_fin').val();
        if (!horaFin || horaFin === '00:00') {
            errores.push('La hora de fin no se ha calculado correctamente');
            $('#hora_fin').addClass('is-invalid');
        }
        
        // Mostrar errores si los hay
        if (errores.length > 0) {
            // NOTIFICACIÓN POR ESQUINA - SweetAlert2 toast para errores
            let errorMessage = 'Por favor, complete los siguientes campos: ' + errores.join(', ');
            
            Swal.fire({
                title: 'Campos incompletos',
                text: errorMessage,
                icon: 'error',
                position: 'top-end',
                toast: true,
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                customClass: {
                    popup: 'animated bounceInRight'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            // Scroll al primer campo con error
            const primerError = $('.is-invalid').first();
            if (primerError.length) {
                $('html, body').animate({
                    scrollTop: primerError.offset().top - 100
                }, 500);
            }
            
            return false;
        }
        
        return true;
    }
    
    function mostrarResumen() {
        const cliente = $('#id_cliente option:selected').text();
        const barbero = $('#id_usuario option:selected').text();
        const servicio = $('#id_servicio option:selected').text();
        const fecha = $('#fecha').val();
        const horaInicio = $('#hora_inicio').val();
        const horaFin = $('#hora_fin').val();
        const estado = $('select[name="estado"]').val();
        const pagado = $('select[name="pagado"]').val();
        
        if (cliente && barbero && servicio && fecha && horaInicio && horaFin) {
            const servicioSelect = document.getElementById('id_servicio');
            const precio = servicioSelect.options[servicioSelect.selectedIndex].dataset.precio;
            
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> ${cliente}<br>
                        <strong>Barbero:</strong> ${barbero}<br>
                        <strong>Servicio:</strong> ${servicio}<br>
                        <strong>Estado:</strong> ${estado}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> ${formatearFecha(fecha)}<br>
                        <strong>Horario:</strong> ${horaInicio} - ${horaFin}<br>
                        <strong>Precio:</strong> Bs ${parseFloat(precio).toFixed(2)}<br>
                        <strong>Pago:</strong> ${pagado == 1 ? 'Pagado' : 'No Pagado'}
                    </div>
                </div>
            `;
            
            $('#resumenContenido').html(html);
            $('#resumen').fadeIn();
        } else {
            $('#resumen').fadeOut();
        }
    }
    
    function cargarDisponibilidad() {
        const fecha = $('#fecha').val();
        const barbero = $('#id_usuario').val();
        
        if (fecha && barbero) {
            $('#cardDisponibilidad').show();
            $('#disponibilidadContenido').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando disponibilidad...</div>');
            
            $.ajax({
                url: 'disponibilidad.php',
                method: 'GET',
                data: { fecha: fecha, barbero: barbero },
                success: function(response) {
                    $('#disponibilidadContenido').html(response);
                },
                error: function() {
                    $('#disponibilidadContenido').html('<p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Error al cargar la disponibilidad</p>');
                }
            });
        } else {
            $('#cardDisponibilidad').hide();
        }
    }
    
    function formatearFecha(fecha) {
        const [year, month, day] = fecha.split('-');
        return `${day}/${month}/${year}`;
    }
    
    // Validación en tiempo real para mejor UX
    $('select, input').on('change', function() {
        const campoId = $(this).attr('id');
        const valor = $(this).val();
        
        // Solo validar los campos obligatorios (excluyendo estado y pagado)
        const camposObligatorios = ['id_cliente', 'id_usuario', 'id_servicio', 'fecha', 'hora_inicio', 'hora_fin'];
        
        if (camposObligatorios.includes(campoId) && valor) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if (camposObligatorios.includes(campoId) && !valor) {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
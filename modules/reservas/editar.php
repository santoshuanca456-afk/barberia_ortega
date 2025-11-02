<?php
/**
 * Editar Reserva
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$id_reserva = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_reserva) {
    setAlert('error', 'Error', 'ID de reserva no válido');
    header('Location: index.php');
    exit();
}

// Obtener datos de la reserva
$stmt = $db->prepare("
    SELECT r.*, 
           DATE(r.fecha_inicio) as fecha,
           TIME(r.fecha_inicio) as hora_inicio,
           TIME(r.fecha_fin) as hora_fin
    FROM reservas r
    WHERE r.id_reserva = ?
");
$stmt->execute([$id_reserva]);
$reserva = $stmt->fetch();

if (!$reserva) {
    setAlert('error', 'Error', 'Reserva no encontrada');
    header('Location: index.php');
    exit();
}

// Obtener clientes
$stmtClientes = $db->query("SELECT id_cliente, nombre, telefono FROM clientes ORDER BY nombre");
$clientes = $stmtClientes->fetchAll();

// Obtener barberos
$stmtBarberos = $db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'barbero' AND estado = 'activo' ORDER BY nombre");
$barberos = $stmtBarberos->fetchAll();

// Obtener servicios
$stmtServicios = $db->query("SELECT id_servicio, nombre, duracion_minutos, precio FROM servicios ORDER BY nombre");
$servicios = $stmtServicios->fetchAll();

$pageTitle = "Editar Reserva";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-edit text-warning"></i> 
                    Editar Reserva #<?php echo $id_reserva; ?>
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Edición
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formReserva" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" name="id_reserva" value="<?php echo $id_reserva; ?>">

                        <div class="row">
                            <!-- Cliente -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Cliente *
                                </label>
                                <select name="id_cliente" id="id_cliente" class="form-select" required>
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id_cliente']; ?>"
                                                <?php echo $reserva['id_cliente'] == $cliente['id_cliente'] ? 'selected' : ''; ?>>
                                            <?php echo $cliente['nombre']; ?> 
                                            <?php if ($cliente['telefono']): ?>
                                                - <?php echo $cliente['telefono']; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                                        <option value="<?php echo $barbero['id_usuario']; ?>"
                                                <?php echo $reserva['id_usuario'] == $barbero['id_usuario'] ? 'selected' : ''; ?>>
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
                                                data-precio="<?php echo $servicio['precio']; ?>"
                                                <?php echo $reserva['id_servicio'] == $servicio['id_servicio'] ? 'selected' : ''; ?>>
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
                                       value="<?php echo $reserva['fecha']; ?>" 
                                       required>
                                <div class="invalid-feedback">Ingrese una fecha</div>
                            </div>

                            <!-- Hora Inicio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Inicio *
                                </label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" 
                                       value="<?php echo substr($reserva['hora_inicio'], 0, 5); ?>"
                                       min="08:00" max="20:00" required>
                                <small class="text-muted">Horario: 08:00 - 20:00</small>
                                <div class="invalid-feedback">Ingrese una hora válida</div>
                            </div>

                            <!-- Hora Fin -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Hora de Fin
                                </label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control" 
                                       value="<?php echo substr($reserva['hora_fin'], 0, 5); ?>" 
                                       readonly>
                                <small class="text-muted">Se calcula automáticamente según el servicio</small>
                            </div>

                            <!-- Notas -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note"></i> Notas / Observaciones
                                </label>
                                <textarea name="notas" class="form-control" rows="3"><?php echo htmlspecialchars($reserva['notas']); ?></textarea>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag"></i> Estado
                                </label>
                                <select name="estado" class="form-select">
                                    <option value="pendiente" <?php echo $reserva['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="confirmada" <?php echo $reserva['estado'] == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                    <option value="cancelada" <?php echo $reserva['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                    <option value="finalizada" <?php echo $reserva['estado'] == 'finalizada' ? 'selected' : ''; ?>>Finalizada</option>
                                </select>
                            </div>

                            <!-- Pagado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill"></i> Estado de Pago
                                </label>
                                <select name="pagado" class="form-select">
                                    <option value="0" <?php echo !$reserva['pagado'] ? 'selected' : ''; ?>>No Pagado</option>
                                    <option value="1" <?php echo $reserva['pagado'] ? 'selected' : ''; ?>>Pagado</option>
                                </select>
                            </div>
                        </div>

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
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Calcular hora fin automáticamente
    $('#id_servicio, #hora_inicio').on('change', function() {
        calcularHoraFin();
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
<?php
/**
 * Ver Detalles de Reserva
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

// Obtener datos completos de la reserva
$stmt = $db->prepare("
    SELECT r.*, 
           c.nombre as cliente_nombre, c.telefono as cliente_telefono, c.correo as cliente_correo,
           u.nombre as barbero_nombre,
           s.nombre as servicio_nombre, s.duracion_minutos, s.precio
    FROM reservas r
    LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE r.id_reserva = ?
");
$stmt->execute([$id_reserva]);
$reserva = $stmt->fetch();

if (!$reserva) {
    setAlert('error', 'Error', 'Reserva no encontrada');
    header('Location: index.php');
    exit();
}

// Obtener historial de pagos si existe
$stmtPagos = $db->prepare("
    SELECT p.*, u.nombre as usuario_nombre
    FROM pagos p
    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
    WHERE p.id_reserva = ?
    ORDER BY p.fecha_pago DESC
");
$stmtPagos->execute([$id_reserva]);
$pagos = $stmtPagos->fetchAll();

$pageTitle = "Detalles de Reserva";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-info-circle text-info"></i> 
                    Reserva #<?php echo $id_reserva; ?>
                </h1>
                <div>
                    <a href="editar.php?id=<?php echo $id_reserva; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-alt"></i> Información de la Reserva
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-user text-primary"></i> Cliente:</strong><br>
                                    <h5><?php echo $reserva['cliente_nombre']; ?></h5>
                                    <?php if ($reserva['cliente_telefono']): ?>
                                        <p class="mb-1">
                                            <i class="fas fa-phone text-success"></i> 
                                            <a href="tel:<?php echo $reserva['cliente_telefono']; ?>">
                                                <?php echo $reserva['cliente_telefono']; ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($reserva['cliente_correo']): ?>
                                        <p class="mb-0">
                                            <i class="fas fa-envelope text-info"></i> 
                                            <a href="mailto:<?php echo $reserva['cliente_correo']; ?>">
                                                <?php echo $reserva['cliente_correo']; ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-user-tie text-primary"></i> Barbero:</strong><br>
                                    <h5><?php echo $reserva['barbero_nombre']; ?></h5>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <strong><i class="fas fa-scissors text-primary"></i> Servicio:</strong><br>
                                    <h5><?php echo $reserva['servicio_nombre']; ?></h5>
                                    <div class="d-flex gap-3">
                                        <span class="badge bg-info">
                                            <i class="fas fa-clock"></i> <?php echo $reserva['duracion_minutos']; ?> minutos
                                        </span>
                                        <span class="badge bg-success">
                                            <i class="fas fa-dollar-sign"></i> <?php echo formatMoney($reserva['precio']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-calendar text-primary"></i> Fecha:</strong><br>
                                    <h5><?php echo formatDate($reserva['fecha_inicio']); ?></h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-clock text-primary"></i> Horario:</strong><br>
                                    <h5>
                                        <?php echo date('H:i', strtotime($reserva['fecha_inicio'])); ?> - 
                                        <?php echo date('H:i', strtotime($reserva['fecha_fin'])); ?>
                                    </h5>
                                </div>
                                
                                <?php if ($reserva['notas']): ?>
                                <div class="col-md-12 mb-3">
                                    <strong><i class="fas fa-sticky-note text-primary"></i> Notas:</strong><br>
                                    <div class="alert alert-light mt-2">
                                        <?php echo nl2br(htmlspecialchars($reserva['notas'])); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> 
                                        Registrado: <?php echo formatDateTime($reserva['fecha_registro']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Pagos -->
                    <?php if (count($pagos) > 0): ?>
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-money-bill-wave"></i> Historial de Pagos
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Registrado por</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?php echo formatDateTime($pago['fecha_pago']); ?></td>
                                            <td><strong><?php echo formatMoney($pago['monto']); ?></strong></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo ucfirst($pago['metodo']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $pago['usuario_nombre']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Panel de Acciones -->
                <div class="col-md-4">
                    <!-- Estado -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-flag"></i> Estado
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <h3>
                                <span class="badge status-<?php echo $reserva['estado']; ?> p-3">
                                    <?php echo ucfirst($reserva['estado']); ?>
                                </span>
                            </h3>
                            
                            <div class="mt-3">
                                <h6 class="text-muted">Cambiar Estado:</h6>
                                <div class="btn-group-vertical w-100">
                                    <?php if ($reserva['estado'] != 'confirmada'): ?>
                                    <a href="procesar.php?action=cambiar_estado&id=<?php echo $id_reserva; ?>&estado=confirmada" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Confirmar
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($reserva['estado'] != 'finalizada'): ?>
                                    <a href="procesar.php?action=cambiar_estado&id=<?php echo $id_reserva; ?>&estado=finalizada" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-check-double"></i> Finalizar
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($reserva['estado'] != 'cancelada'): ?>
                                    <a href="procesar.php?action=cambiar_estado&id=<?php echo $id_reserva; ?>&estado=cancelada" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Seguro que desea cancelar esta reserva?')">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pago -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-money-bill"></i> Pago
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($reserva['pagado']): ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle fa-3x mb-2"></i>
                                    <h5>Pagado</h5>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-2"></i>
                                    <h5>Pendiente de Pago</h5>
                                </div>
                                <a href="procesar.php?action=marcar_pagado&id=<?php echo $id_reserva; ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-dollar-sign"></i> Marcar como Pagado
                                </a>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <div class="text-start">
                                <strong>Monto Total:</strong><br>
                                <h3 class="text-success"><?php echo formatMoney($reserva['precio']); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="card shadow">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-bolt"></i> Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                
                                <a href="tel:<?php echo $reserva['cliente_telefono']; ?>" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-phone"></i> Llamar Cliente
                                </a>
                                
                                <a href="https://wa.me/591<?php echo $reserva['cliente_telefono']; ?>?text=Hola%20<?php echo urlencode($reserva['cliente_nombre']); ?>%2C%20confirmamos%20tu%20cita%20para%20el%20<?php echo urlencode(formatDate($reserva['fecha_inicio'])); ?>" 
                                   class="btn btn-success btn-sm" 
                                   target="_blank">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                
                                <a href="editar.php?id=<?php echo $id_reserva; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Editar Reserva
                                </a>
                                
                                <?php if (hasRole(ROLE_ADMIN)): ?>
                                <hr>
                                <a href="#" 
                                   onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $id_reserva; ?>', '¿Eliminar esta reserva permanentemente?')" 
                                   class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .no-print {
        display: none !important;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>
<?php
/**
 * Ver Detalles de Cliente
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$id_cliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_cliente) {
    setAlert('error', 'Error', 'ID de cliente no válido');
    header('Location: index.php');
    exit();
}

// Obtener datos del cliente
$stmt = $db->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();

if (!$cliente) {
    setAlert('error', 'Error', 'Cliente no encontrado');
    header('Location: index.php');
    exit();
}

// Obtener historial de reservas
$stmtReservas = $db->prepare("
    SELECT r.*, 
           u.nombre as barbero_nombre,
           s.nombre as servicio_nombre,
           s.precio
    FROM reservas r
    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE r.id_cliente = ?
    ORDER BY r.fecha_inicio DESC
    LIMIT 10
");
$stmtReservas->execute([$id_cliente]);
$reservas = $stmtReservas->fetchAll();

// Estadísticas del cliente
$stmtStats = $db->prepare("
    SELECT 
        COUNT(*) as total_reservas,
        COUNT(CASE WHEN estado = 'finalizada' THEN 1 END) as reservas_completadas,
        COUNT(CASE WHEN estado = 'cancelada' THEN 1 END) as reservas_canceladas,
        SUM(CASE WHEN estado = 'finalizada' AND pagado = 1 THEN s.precio ELSE 0 END) as total_gastado
    FROM reservas r
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    WHERE r.id_cliente = ?
");
$stmtStats->execute([$id_cliente]);
$stats = $stmtStats->fetch();

$pageTitle = "Detalles de Cliente";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-user-circle text-info"></i> 
                    Cliente #<?php echo $id_cliente; ?>
                </h1>
                <div>
                    <a href="editar.php?id=<?php echo $id_cliente; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información del Cliente -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-id-card"></i> Información Personal
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="icon-circle bg-info mx-auto" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            </div>
                            
                            <h4 class="mb-3"><?php echo $cliente['nombre']; ?></h4>
                            
                            <div class="text-start">
                                <?php if ($cliente['telefono']): ?>
                                <div class="mb-2">
                                    <strong><i class="fas fa-phone text-success"></i> Teléfono:</strong><br>
                                    <a href="tel:<?php echo $cliente['telefono']; ?>" class="text-decoration-none">
                                        <?php echo $cliente['telefono']; ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($cliente['correo']): ?>
                                <div class="mb-2">
                                    <strong><i class="fas fa-envelope text-info"></i> Correo:</strong><br>
                                    <a href="mailto:<?php echo $cliente['correo']; ?>" class="text-decoration-none">
                                        <?php echo $cliente['correo']; ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-2">
                                    <strong><i class="fas fa-calendar text-primary"></i> Registrado:</strong><br>
                                    <?php echo formatDate($cliente['fecha_registro']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <?php if ($cliente['notas']): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-sticky-note"></i> Notas
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($cliente['notas'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Acciones Rápidas -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-bolt"></i> Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="../reservas/crear.php?cliente=<?php echo $id_cliente; ?>" class="btn btn-success">
                                    <i class="fas fa-calendar-plus"></i> Nueva Reserva
                                </a>
                                
                                <?php if ($cliente['telefono']): ?>
                                <a href="tel:<?php echo $cliente['telefono']; ?>" class="btn btn-info">
                                    <i class="fas fa-phone"></i> Llamar
                                </a>
                                
                                <a href="https://wa.me/591<?php echo $cliente['telefono']; ?>" 
                                   class="btn btn-success" 
                                   target="_blank">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <?php endif; ?>
                                
                                <a href="editar.php?id=<?php echo $id_cliente; ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Datos
                                </a>
                                
                                <?php if (hasRole(ROLE_ADMIN)): ?>
                                <hr>
                                <a href="#" 
                                   onclick="return confirmarEliminacion('procesar.php?action=eliminar&id=<?php echo $id_cliente; ?>', '¿Eliminar este cliente permanentemente?')" 
                                   class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas e Historial -->
                <div class="col-md-8">
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body py-3 text-center">
                                    <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-0 text-primary"><?php echo $stats['total_reservas']; ?></h3>
                                    <small class="text-muted">Total Reservas</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body py-3 text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0 text-success"><?php echo $stats['reservas_completadas']; ?></h3>
                                    <small class="text-muted">Completadas</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-danger shadow-sm h-100">
                                <div class="card-body py-3 text-center">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h3 class="mb-0 text-danger"><?php echo $stats['reservas_canceladas']; ?></h3>
                                    <small class="text-muted">Canceladas</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body py-3 text-center">
                                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                    <h3 class="mb-0 text-info"><?php echo formatMoney($stats['total_gastado']); ?></h3>
                                    <small class="text-muted">Total Gastado</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Reservas -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-history"></i> Historial de Reservas (Últimas 10)
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (count($reservas) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Barbero</th>
                                                <th>Servicio</th>
                                                <th>Precio</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservas as $reserva): ?>
                                            <tr>
                                                <td><?php echo formatDate($reserva['fecha_inicio']); ?></td>
                                                <td><?php echo date('H:i', strtotime($reserva['fecha_inicio'])); ?></td>
                                                <td><?php echo $reserva['barbero_nombre']; ?></td>
                                                <td><?php echo $reserva['servicio_nombre']; ?></td>
                                                <td><?php echo formatMoney($reserva['precio']); ?></td>
                                                <td>
                                                    <span class="badge status-<?php echo $reserva['estado']; ?>">
                                                        <?php echo ucfirst($reserva['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="../reservas/ver.php?id=<?php echo $reserva['id_reserva']; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>Este cliente aún no tiene reservas</p>
                                    <a href="../reservas/crear.php?cliente=<?php echo $id_cliente; ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear Primera Reserva
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
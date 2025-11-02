<?php
/**
 * Procesar Acciones de Pagos
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Para depuración - quitar en producción
error_log("=== PROCESAR PAGOS EJECUTADO ===");
error_log("Action: " . $action);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));

try {
    switch ($action) {
        case 'registrar_servicio':
            error_log("Procesando registro de pago de servicio...");
            
            // Obtener datos del formulario
            $id_reserva = isset($_POST['id_reserva']) ? intval($_POST['id_reserva']) : 0;
            $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
            $metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : '';
            
            error_log("Datos recibidos - ID Reserva: $id_reserva, Monto: $monto, Método: $metodo");
            
            // Validaciones básicas
            if (empty($id_reserva) || $id_reserva <= 0) {
                throw new Exception('ID de reserva inválido');
            }
            
            if ($monto <= 0) {
                throw new Exception('El monto debe ser mayor a cero');
            }
            
            if (empty($metodo)) {
                throw new Exception('Debe seleccionar un método de pago');
            }
            
            // Verificar que la reserva existe
            $stmt = $db->prepare("
                SELECT r.*, s.precio, c.nombre as cliente_nombre,
                       r.estado, r.pagado
                FROM reservas r
                LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
                LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
                WHERE r.id_reserva = ?
            ");
            $stmt->execute([$id_reserva]);
            $reserva = $stmt->fetch();
            
            if (!$reserva) {
                throw new Exception('La reserva no existe');
            }
            
            error_log("Reserva encontrada - Estado: {$reserva['estado']}, Pagado: {$reserva['pagado']}");
            
            // Verificar que no esté ya pagada
            if ($reserva['pagado']) {
                throw new Exception('Esta reserva ya está pagada');
            }
            
            // Verificar que la reserva esté en estado válido para pago
            if (!in_array($reserva['estado'], ['confirmada', 'finalizada'])) {
                throw new Exception('Solo se pueden pagar reservas confirmadas o finalizadas. Estado actual: ' . $reserva['estado']);
            }
            
            // Iniciar transacción
            $db->beginTransaction();
            
            try {
                // Insertar pago
                $stmt = $db->prepare("
                    INSERT INTO pagos (id_reserva, monto, metodo, id_usuario, fecha_pago)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$id_reserva, $monto, $metodo, $_SESSION['id_usuario']]);
                
                $id_pago = $db->lastInsertId();
                error_log("Pago insertado - ID: $id_pago");
                
                // Marcar reserva como pagada
                $stmt = $db->prepare("UPDATE reservas SET pagado = TRUE WHERE id_reserva = ?");
                $stmt->execute([$id_reserva]);
                error_log("Reserva marcada como pagada");
                
                // Registrar en auditoría
                $accion = "Registró pago de servicio - Reserva #$id_reserva - Cliente: {$reserva['cliente_nombre']} - Monto: " . formatMoney($monto);
                $stmt_auditoria = $db->prepare("INSERT INTO auditoria (id_usuario, accion) VALUES (?, ?)");
                $stmt_auditoria->execute([$_SESSION['id_usuario'], $accion]);
                error_log("Auditoría registrada");
                
                $db->commit();
                error_log("Transacción completada con éxito");
                
                setAlert('success', 'Pago registrado', 'El pago se registró correctamente y la reserva fue marcada como pagada');
                header('Location: index.php');
                exit();
                
            } catch (Exception $e) {
                $db->rollBack();
                error_log("Error en transacción: " . $e->getMessage());
                throw new Exception('Error al registrar el pago: ' . $e->getMessage());
            }
            break;

        case 'registrar_alquiler':
            error_log("Procesando registro de pago de alquiler...");
            
            // Obtener datos
            $id_alquiler = isset($_POST['id_alquiler']) ? intval($_POST['id_alquiler']) : 0;
            $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
            $metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : '';
            $concepto = isset($_POST['concepto']) ? trim($_POST['concepto']) : '';
            
            error_log("Datos recibidos - ID Alquiler: $id_alquiler, Monto: $monto, Método: $metodo");
            
            // Validaciones básicas
            if (empty($id_alquiler) || $id_alquiler <= 0) {
                throw new Exception('ID de alquiler inválido');
            }
            
            if ($monto <= 0) {
                throw new Exception('El monto debe ser mayor a cero');
            }
            
            if (empty($metodo)) {
                throw new Exception('Debe seleccionar un método de pago');
            }
            
            // Verificar que el alquiler existe y está vigente
            $stmt = $db->prepare("
                SELECT a.*, e.nombre as estacion_nombre, u.nombre as usuario_nombre
                FROM alquileres a
                LEFT JOIN estaciones e ON a.id_estacion = e.id_estacion
                LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
                WHERE a.id_alquiler = ? AND a.estado = 'vigente'
            ");
            $stmt->execute([$id_alquiler]);
            $alquiler = $stmt->fetch();
            
            if (!$alquiler) {
                throw new Exception('El alquiler no existe o no está vigente');
            }
            
            // Insertar pago de alquiler
            $stmt = $db->prepare("
                INSERT INTO pagos_alquiler (id_alquiler, monto, metodo, fecha_pago)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$id_alquiler, $monto, $metodo]);
            
            $id_pago_alquiler = $db->lastInsertId();
            error_log("Pago de alquiler insertado - ID: $id_pago_alquiler");
            
            // Registrar en auditoría
            $accion_concepto = $concepto ? " - Concepto: $concepto" : "";
            $accion = "Registró pago de alquiler - Contrato #$id_alquiler - Estación: {$alquiler['estacion_nombre']} - Monto: " . formatMoney($monto) . $accion_concepto;
            $stmt_auditoria = $db->prepare("INSERT INTO auditoria (id_usuario, accion) VALUES (?, ?)");
            $stmt_auditoria->execute([$_SESSION['id_usuario'], $accion]);
            
            setAlert('success', 'Pago registrado', 'El pago de alquiler se registró correctamente');
            header('Location: index.php?tipo=alquileres');
            exit();
            break;

        case 'marcar_pagado_rapido':
            // Solo administradores pueden marcar pagos rápidos
            if (!hasRole(ROLE_ADMIN)) {
                throw new Exception('No tienes permisos para realizar esta acción');
            }
            
            $id_reserva = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if (empty($id_reserva)) {
                throw new Exception('ID de reserva inválido');
            }
            
            // Verificar que la reserva existe y no está pagada
            $stmt = $db->prepare("
                SELECT r.*, s.precio, c.nombre as cliente_nombre
                FROM reservas r
                LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
                LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
                WHERE r.id_reserva = ? AND r.pagado = FALSE
            ");
            $stmt->execute([$id_reserva]);
            $reserva = $stmt->fetch();
            
            if (!$reserva) {
                throw new Exception('La reserva no existe o ya está pagada');
            }
            
            // Verificar que la reserva esté en estado válido para pago
            if (!in_array($reserva['estado'], ['confirmada', 'finalizada'])) {
                throw new Exception('Solo se pueden pagar reservas confirmadas o finalizadas');
            }
            
            // Iniciar transacción
            $db->beginTransaction();
            
            try {
                // Insertar pago en efectivo por defecto
                $stmt = $db->prepare("
                    INSERT INTO pagos (id_reserva, monto, metodo, id_usuario, fecha_pago)
                    VALUES (?, ?, 'efectivo', ?, NOW())
                ");
                $stmt->execute([$id_reserva, $reserva['precio'], $_SESSION['id_usuario']]);
                
                // Marcar reserva como pagada
                $stmt = $db->prepare("UPDATE reservas SET pagado = TRUE WHERE id_reserva = ?");
                $stmt->execute([$id_reserva]);
                
                // Registrar en auditoría
                $accion = "Marcó como pagada la reserva #$id_reserva - Cliente: {$reserva['cliente_nombre']} - Monto: " . formatMoney($reserva['precio']);
                $stmt_auditoria = $db->prepare("INSERT INTO auditoria (id_usuario, accion) VALUES (?, ?)");
                $stmt_auditoria->execute([$_SESSION['id_usuario'], $accion]);
                
                $db->commit();
                
                setAlert('success', 'Pago registrado', 'La reserva se marcó como pagada correctamente');
                header('Location: index.php');
                exit();
                
            } catch (Exception $e) {
                $db->rollBack();
                throw new Exception('Error al registrar el pago: ' . $e->getMessage());
            }
            break;

        default:
            throw new Exception('Acción no válida: ' . $action);
    }
    
} catch (Exception $e) {
    error_log("Error en pagos/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir según el contexto
    if ($action == 'registrar_servicio') {
        header('Location: registrar_servicio.php');
    } else if ($action == 'registrar_alquiler') {
        header('Location: registrar_alquiler.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
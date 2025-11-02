<?php
/**
 * Procesar Acciones de Reservas
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

try {
    switch ($action) {
        case 'crear':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            // Obtener datos
            $id_cliente = sanitize($_POST['id_cliente']);
            $id_usuario = sanitize($_POST['id_usuario']);
            $id_servicio = sanitize($_POST['id_servicio']);
            $fecha = sanitize($_POST['fecha']);
            $hora_inicio = sanitize($_POST['hora_inicio']);
            $hora_fin = sanitize($_POST['hora_fin']);
            $estado = sanitize($_POST['estado']);
            $pagado = isset($_POST['pagado']) ? (int)$_POST['pagado'] : 0;
            $notas = isset($_POST['notas']) ? sanitize($_POST['notas']) : '';
            
            // Construir fechas completas
            $fecha_inicio = $fecha . ' ' . $hora_inicio . ':00';
            $fecha_fin = $fecha . ' ' . $hora_fin . ':00';
            
            // Validar que no exista conflicto de horarios
            $stmtConflicto = $db->prepare("
                SELECT COUNT(*) 
                FROM reservas 
                WHERE id_usuario = ? 
                AND estado NOT IN ('cancelada')
                AND (
                    (fecha_inicio BETWEEN ? AND ?) 
                    OR (fecha_fin BETWEEN ? AND ?)
                    OR (? BETWEEN fecha_inicio AND fecha_fin)
                )
            ");
            $stmtConflicto->execute([
                $id_usuario, 
                $fecha_inicio, $fecha_fin,
                $fecha_inicio, $fecha_fin,
                $fecha_inicio
            ]);
            
            if ($stmtConflicto->fetchColumn() > 0) {
                throw new Exception('El barbero ya tiene una reserva en este horario');
            }
            
            // Insertar reserva
            $stmt = $db->prepare("
                INSERT INTO reservas (
                    id_cliente, id_usuario, id_servicio, 
                    fecha_inicio, fecha_fin, estado, pagado, notas
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $id_cliente, $id_usuario, $id_servicio,
                $fecha_inicio, $fecha_fin, $estado, $pagado, $notas
            ]);
            
            $id_reserva = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó reserva #$id_reserva para cliente #$id_cliente");
            
            // CORREGIDO: Usar sistema de sesiones en lugar de setAlert()
            $_SESSION['success'] = 'Reserva creada correctamente';
            header('Location: index.php');
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_reserva = (int)$_POST['id_reserva'];
            $id_cliente = sanitize($_POST['id_cliente']);
            $id_usuario = sanitize($_POST['id_usuario']);
            $id_servicio = sanitize($_POST['id_servicio']);
            $fecha = sanitize($_POST['fecha']);
            $hora_inicio = sanitize($_POST['hora_inicio']);
            $hora_fin = sanitize($_POST['hora_fin']);
            $estado = sanitize($_POST['estado']);
            $pagado = isset($_POST['pagado']) ? (int)$_POST['pagado'] : 0;
            $notas = isset($_POST['notas']) ? sanitize($_POST['notas']) : '';
            
            $fecha_inicio = $fecha . ' ' . $hora_inicio . ':00';
            $fecha_fin = $fecha . ' ' . $hora_fin . ':00';
            
            // Validar conflictos (excluyendo la reserva actual)
            $stmtConflicto = $db->prepare("
                SELECT COUNT(*) 
                FROM reservas 
                WHERE id_usuario = ? 
                AND id_reserva != ?
                AND estado NOT IN ('cancelada')
                AND (
                    (fecha_inicio BETWEEN ? AND ?) 
                    OR (fecha_fin BETWEEN ? AND ?)
                    OR (? BETWEEN fecha_inicio AND fecha_fin)
                )
            ");
            $stmtConflicto->execute([
                $id_usuario, $id_reserva,
                $fecha_inicio, $fecha_fin,
                $fecha_inicio, $fecha_fin,
                $fecha_inicio
            ]);
            
            if ($stmtConflicto->fetchColumn() > 0) {
                throw new Exception('El barbero ya tiene una reserva en este horario');
            }
            
            // Actualizar reserva
            $stmt = $db->prepare("
                UPDATE reservas SET
                    id_cliente = ?, 
                    id_usuario = ?, 
                    id_servicio = ?,
                    fecha_inicio = ?, 
                    fecha_fin = ?, 
                    estado = ?, 
                    pagado = ?, 
                    notas = ?
                WHERE id_reserva = ?
            ");
            
            $stmt->execute([
                $id_cliente, $id_usuario, $id_servicio,
                $fecha_inicio, $fecha_fin, $estado, $pagado, $notas,
                $id_reserva
            ]);
            
            logAudit($_SESSION['user_id'], "Editó reserva #$id_reserva");
            
            // CORREGIDO: Usar sistema de sesiones
            $_SESSION['success'] = 'Reserva actualizada correctamente';
            header('Location: index.php');
            exit();
            
        case 'cambiar_estado':
            $id_reserva = (int)$_GET['id'];
            $nuevo_estado = sanitize($_GET['estado']);
            
            // Validar estado
            $estados_validos = ['pendiente', 'confirmada', 'cancelada', 'finalizada'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                throw new Exception('Estado inválido');
            }
            
            $stmt = $db->prepare("UPDATE reservas SET estado = ? WHERE id_reserva = ?");
            $stmt->execute([$nuevo_estado, $id_reserva]);
            
            logAudit($_SESSION['user_id'], "Cambió estado de reserva #$id_reserva a '$nuevo_estado'");
            
            // CORREGIDO: Usar sistema de sesiones
            $_SESSION['success'] = "Estado actualizado: $nuevo_estado";
            header('Location: index.php');
            exit();
            
        case 'marcar_pagado':
            $id_reserva = (int)$_GET['id'];
            
            $stmt = $db->prepare("UPDATE reservas SET pagado = 1 WHERE id_reserva = ?");
            $stmt->execute([$id_reserva]);
            
            logAudit($_SESSION['user_id'], "Marcó como pagada la reserva #$id_reserva");
            
            // CORREGIDO: Usar sistema de sesiones
            $_SESSION['success'] = 'Reserva marcada como pagada';
            header('Location: ver.php?id=' . $id_reserva);
            exit();
            
        case 'eliminar':
            // Solo administradores pueden eliminar
            requireRole(ROLE_ADMIN);
            
            $id_reserva = (int)$_GET['id'];
            
            // Obtener información antes de eliminar
            $stmt = $db->prepare("SELECT id_cliente, fecha_inicio FROM reservas WHERE id_reserva = ?");
            $stmt->execute([$id_reserva]);
            $reserva = $stmt->fetch();
            
            // Eliminar reserva
            $stmt = $db->prepare("DELETE FROM reservas WHERE id_reserva = ?");
            $stmt->execute([$id_reserva]);
            
            logAudit($_SESSION['user_id'], "Eliminó reserva #$id_reserva (Cliente: {$reserva['id_cliente']}, Fecha: {$reserva['fecha_inicio']})");
            
            // CORREGIDO: Usar sistema de sesiones
            $_SESSION['success'] = 'Reserva eliminada correctamente';
            header('Location: index.php');
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en reservas/procesar.php: " . $e->getMessage());
    
    // CORREGIDO: Usar sistema de sesiones para errores
    $_SESSION['error'] = $e->getMessage();
    
    // Redirigir según el contexto
    if (isset($_POST['id_reserva'])) {
        header('Location: editar.php?id=' . $_POST['id_reserva']);
    } else if (isset($_GET['id'])) {
        header('Location: ver.php?id=' . $_GET['id']);
    } else {
        header('Location: crear.php');
    }
    exit();
}
<?php
/**
 * Procesar Acciones de Turnos
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
            
            // Obtener y sanitizar datos
            $id_usuario = (int)$_POST['id_usuario'];
            $fecha = sanitize($_POST['fecha']);
            $tipo = sanitize($_POST['tipo']);
            $observaciones = isset($_POST['observaciones']) ? sanitize($_POST['observaciones']) : null;
            
            // Validaciones
            if (empty($id_usuario) || empty($fecha) || empty($tipo)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            
            if (!in_array($tipo, ['apertura', 'cierre', 'limpieza'])) {
                throw new Exception('Tipo de turno inválido');
            }
            
            // Verificar que no exista ya un turno del mismo tipo para la misma fecha
            $stmtCheck = $db->prepare("
                SELECT id_turno 
                FROM turnos 
                WHERE fecha = ? AND tipo = ?
            ");
            $stmtCheck->execute([$fecha, $tipo]);
            if ($stmtCheck->fetch()) {
                throw new Exception("Ya existe un turno de $tipo asignado para esta fecha");
            }
            
            // Insertar turno
            $stmt = $db->prepare("
                INSERT INTO turnos (id_usuario, fecha, tipo, observaciones, cumplido) 
                VALUES (?, ?, ?, ?, FALSE)
            ");
            
            $stmt->execute([$id_usuario, $fecha, $tipo, $observaciones]);
            $id_turno = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Asignó turno de $tipo #$id_turno para $fecha al usuario #$id_usuario");
            
            setAlert('success', 'Turno asignado', 'El turno se asignó correctamente');
            header('Location: index.php?fecha=' . $fecha);
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_turno = (int)$_POST['id_turno'];
            $id_usuario = (int)$_POST['id_usuario'];
            $fecha = sanitize($_POST['fecha']);
            $tipo = sanitize($_POST['tipo']);
            $observaciones = isset($_POST['observaciones']) ? sanitize($_POST['observaciones']) : null;
            
            // Validaciones
            if (empty($id_usuario) || empty($fecha) || empty($tipo)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            
            if (!in_array($tipo, ['apertura', 'cierre', 'limpieza'])) {
                throw new Exception('Tipo de turno inválido');
            }
            
            // Verificar que no exista conflicto con otro turno
            $stmtCheck = $db->prepare("
                SELECT id_turno 
                FROM turnos 
                WHERE fecha = ? AND tipo = ? AND id_turno != ?
            ");
            $stmtCheck->execute([$fecha, $tipo, $id_turno]);
            if ($stmtCheck->fetch()) {
                throw new Exception("Ya existe otro turno de $tipo asignado para esta fecha");
            }
            
            // Actualizar turno
            $stmt = $db->prepare("
                UPDATE turnos SET
                    id_usuario = ?,
                    fecha = ?,
                    tipo = ?,
                    observaciones = ?
                WHERE id_turno = ?
            ");
            
            $stmt->execute([$id_usuario, $fecha, $tipo, $observaciones, $id_turno]);
            
            logAudit($_SESSION['user_id'], "Editó turno #$id_turno");
            
            setAlert('success', 'Turno actualizado', 'Los cambios se guardaron correctamente');
            header('Location: index.php?fecha=' . $fecha);
            exit();
            
        case 'marcar_cumplido':
            $id_turno = (int)$_GET['id'];
            
            // Obtener información del turno
            $stmt = $db->prepare("SELECT fecha FROM turnos WHERE id_turno = ?");
            $stmt->execute([$id_turno]);
            $turno = $stmt->fetch();
            
            if (!$turno) {
                throw new Exception('Turno no encontrado');
            }
            
            // Marcar como cumplido
            $stmt = $db->prepare("UPDATE turnos SET cumplido = TRUE WHERE id_turno = ?");
            $stmt->execute([$id_turno]);
            
            logAudit($_SESSION['user_id'], "Marcó como cumplido el turno #$id_turno");
            
            setAlert('success', 'Turno cumplido', 'El turno se marcó como cumplido');
            header('Location: index.php?fecha=' . $turno['fecha']);
            exit();
            
        case 'eliminar':
            // Solo administradores pueden eliminar
            requireRole(ROLE_ADMIN);
            
            $id_turno = (int)$_GET['id'];
            
            // Obtener información del turno antes de eliminar
            $stmt = $db->prepare("SELECT tipo, fecha FROM turnos WHERE id_turno = ?");
            $stmt->execute([$id_turno]);
            $turno = $stmt->fetch();
            
            if (!$turno) {
                throw new Exception('Turno no encontrado');
            }
            
            // Eliminar turno
            $stmt = $db->prepare("DELETE FROM turnos WHERE id_turno = ?");
            $stmt->execute([$id_turno]);
            
            logAudit($_SESSION['user_id'], "Eliminó turno de {$turno['tipo']} #$id_turno (Fecha: {$turno['fecha']})");
            
            setAlert('success', 'Turno eliminado', 'El turno se eliminó correctamente');
            header('Location: index.php?fecha=' . $turno['fecha']);
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en turnos/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir
    if (isset($_POST['fecha'])) {
        header('Location: index.php?fecha=' . $_POST['fecha']);
    } else if (isset($turno['fecha'])) {
        header('Location: index.php?fecha=' . $turno['fecha']);
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
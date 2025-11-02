<?php
/**
 * Procesar Acciones de Servicios
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
            $nombre = sanitize($_POST['nombre']);
            $duracion_minutos = (int)$_POST['duracion_minutos'];
            $precio = (float)$_POST['precio'];
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre del servicio es obligatorio');
            }
            
            if ($duracion_minutos < 5 || $duracion_minutos > 300) {
                throw new Exception('La duración debe estar entre 5 y 300 minutos');
            }
            
            if ($precio <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }
            
            // Verificar si ya existe un servicio con el mismo nombre
            $stmtCheck = $db->prepare("SELECT id_servicio FROM servicios WHERE nombre = ?");
            $stmtCheck->execute([$nombre]);
            if ($stmtCheck->fetch()) {
                throw new Exception('Ya existe un servicio con este nombre');
            }
            
            // Insertar servicio
            $stmt = $db->prepare("
                INSERT INTO servicios (nombre, duracion_minutos, precio) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$nombre, $duracion_minutos, $precio]);
            $id_servicio = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó servicio #$id_servicio: $nombre");
            
            setAlert('success', 'Servicio creado', 'El servicio se registró correctamente');
            header('Location: index.php');
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_servicio = (int)$_POST['id_servicio'];
            $nombre = sanitize($_POST['nombre']);
            $duracion_minutos = (int)$_POST['duracion_minutos'];
            $precio = (float)$_POST['precio'];
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre del servicio es obligatorio');
            }
            
            if ($duracion_minutos < 5 || $duracion_minutos > 300) {
                throw new Exception('La duración debe estar entre 5 y 300 minutos');
            }
            
            if ($precio <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }
            
            // Verificar si el nombre ya está en uso por otro servicio
            $stmtCheck = $db->prepare("SELECT id_servicio FROM servicios WHERE nombre = ? AND id_servicio != ?");
            $stmtCheck->execute([$nombre, $id_servicio]);
            if ($stmtCheck->fetch()) {
                throw new Exception('Ya existe otro servicio con este nombre');
            }
            
            // Actualizar servicio
            $stmt = $db->prepare("
                UPDATE servicios SET
                    nombre = ?,
                    duracion_minutos = ?,
                    precio = ?
                WHERE id_servicio = ?
            ");
            
            $stmt->execute([$nombre, $duracion_minutos, $precio, $id_servicio]);
            
            logAudit($_SESSION['user_id'], "Editó servicio #$id_servicio: $nombre");
            
            setAlert('success', 'Servicio actualizado', 'Los cambios se guardaron correctamente');
            header('Location: index.php');
            exit();
            
        case 'eliminar':
            // Solo administradores pueden eliminar
            requireRole(ROLE_ADMIN);
            
            $id_servicio = (int)$_GET['id'];
            
            // Verificar si tiene reservas asociadas
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM reservas WHERE id_servicio = ?");
            $stmtCheck->execute([$id_servicio]);
            $tieneReservas = $stmtCheck->fetchColumn();
            
            if ($tieneReservas > 0) {
                throw new Exception("No se puede eliminar el servicio porque tiene $tieneReservas reserva(s) asociada(s)");
            }
            
            // Obtener información del servicio antes de eliminar
            $stmt = $db->prepare("SELECT nombre FROM servicios WHERE id_servicio = ?");
            $stmt->execute([$id_servicio]);
            $servicio = $stmt->fetch();
            
            // Eliminar servicio
            $stmt = $db->prepare("DELETE FROM servicios WHERE id_servicio = ?");
            $stmt->execute([$id_servicio]);
            
            logAudit($_SESSION['user_id'], "Eliminó servicio #$id_servicio: {$servicio['nombre']}");
            
            setAlert('success', 'Servicio eliminado', 'El servicio se eliminó correctamente');
            header('Location: index.php');
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en servicios/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir según el contexto
    if (isset($_POST['id_servicio'])) {
        header('Location: editar.php?id=' . $_POST['id_servicio']);
    } else if (isset($_GET['id'])) {
        header('Location: index.php');
    } else {
        header('Location: crear.php');
    }
    exit();
}
?>
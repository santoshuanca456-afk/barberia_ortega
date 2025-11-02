<?php
/**
 * Procesar Acciones de Clientes
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
            $telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : null;
            $correo = isset($_POST['correo']) ? sanitize($_POST['correo']) : null;
            $notas = isset($_POST['notas']) ? sanitize($_POST['notas']) : null;
            $redirect = isset($_POST['redirect']) ? sanitize($_POST['redirect']) : '';
            
            // Validar nombre obligatorio
            if (empty($nombre)) {
                throw new Exception('El nombre del cliente es obligatorio');
            }
            
            // Validar teléfono si se proporciona
            if (!empty($telefono) && !preg_match('/^[67]\d{7}$/', $telefono)) {
                throw new Exception('El teléfono debe tener 8 dígitos y comenzar con 6 o 7');
            }
            
            // Validar correo si se proporciona
            if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }
            
            // Verificar si ya existe un cliente con el mismo teléfono
            if (!empty($telefono)) {
                $stmtCheck = $db->prepare("SELECT id_cliente FROM clientes WHERE telefono = ?");
                $stmtCheck->execute([$telefono]);
                if ($stmtCheck->fetch()) {
                    throw new Exception('Ya existe un cliente registrado con este número de teléfono');
                }
            }
            
            // Insertar cliente
            $stmt = $db->prepare("
                INSERT INTO clientes (nombre, telefono, correo, notas) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([$nombre, $telefono, $correo, $notas]);
            $id_cliente = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó cliente #$id_cliente: $nombre");
            
            setAlert('success', 'Cliente registrado', 'El cliente se registró correctamente');
            
            // Redirigir según corresponda
            if ($redirect == 'reservas') {
                header('Location: ../reservas/crear.php');
            } else {
                header('Location: ver.php?id=' . $id_cliente);
            }
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_cliente = (int)$_POST['id_cliente'];
            $nombre = sanitize($_POST['nombre']);
            $telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : null;
            $correo = isset($_POST['correo']) ? sanitize($_POST['correo']) : null;
            $notas = isset($_POST['notas']) ? sanitize($_POST['notas']) : null;
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre del cliente es obligatorio');
            }
            
            if (!empty($telefono) && !preg_match('/^[67]\d{7}$/', $telefono)) {
                throw new Exception('El teléfono debe tener 8 dígitos y comenzar con 6 o 7');
            }
            
            if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }
            
            // Verificar si el teléfono ya está en uso por otro cliente
            if (!empty($telefono)) {
                $stmtCheck = $db->prepare("SELECT id_cliente FROM clientes WHERE telefono = ? AND id_cliente != ?");
                $stmtCheck->execute([$telefono, $id_cliente]);
                if ($stmtCheck->fetch()) {
                    throw new Exception('Ya existe otro cliente registrado con este número de teléfono');
                }
            }
            
            // Actualizar cliente
            $stmt = $db->prepare("
                UPDATE clientes SET
                    nombre = ?,
                    telefono = ?,
                    correo = ?,
                    notas = ?
                WHERE id_cliente = ?
            ");
            
            $stmt->execute([$nombre, $telefono, $correo, $notas, $id_cliente]);
            
            logAudit($_SESSION['user_id'], "Editó cliente #$id_cliente: $nombre");
            
            setAlert('success', 'Cliente actualizado', 'Los cambios se guardaron correctamente');
            header('Location: ver.php?id=' . $id_cliente);
            exit();
            
        case 'eliminar':
            // Solo administradores pueden eliminar
            requireRole(ROLE_ADMIN);
            
            $id_cliente = (int)$_GET['id'];
            
            // Verificar si tiene reservas
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM reservas WHERE id_cliente = ?");
            $stmtCheck->execute([$id_cliente]);
            $tieneReservas = $stmtCheck->fetchColumn();
            
            if ($tieneReservas > 0) {
                throw new Exception("No se puede eliminar el cliente porque tiene $tieneReservas reserva(s) registrada(s)");
            }
            
            // Obtener información del cliente antes de eliminar
            $stmt = $db->prepare("SELECT nombre FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            $cliente = $stmt->fetch();
            
            // Eliminar cliente
            $stmt = $db->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            
            logAudit($_SESSION['user_id'], "Eliminó cliente #$id_cliente: {$cliente['nombre']}");
            
            setAlert('success', 'Cliente eliminado', 'El cliente se eliminó correctamente');
            header('Location: index.php');
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en clientes/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir según el contexto
    if (isset($_POST['id_cliente'])) {
        header('Location: editar.php?id=' . $_POST['id_cliente']);
    } else if (isset($_GET['id'])) {
        header('Location: ver.php?id=' . $_GET['id']);
    } else {
        header('Location: crear.php');
    }
    exit();
}
?>
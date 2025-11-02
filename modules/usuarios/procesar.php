<?php
/**
 * Procesar Acciones de Usuarios
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();
requireRole(ROLE_ADMIN); // Solo administradores

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
            $usuario = sanitize($_POST['usuario']);
            $contrasena = $_POST['contrasena'];
            $telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : null;
            $correo = isset($_POST['correo']) ? sanitize($_POST['correo']) : null;
            $rol = sanitize($_POST['rol']);
            $estado = sanitize($_POST['estado']);
            
            // Validaciones
            if (empty($nombre) || empty($usuario) || empty($contrasena) || empty($rol)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            
            if (strlen($usuario) < 3) {
                throw new Exception('El nombre de usuario debe tener al menos 3 caracteres');
            }
            
            if (strlen($contrasena) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            if (!in_array($rol, ['administrador', 'barbero', 'apoyo'])) {
                throw new Exception('Rol inválido');
            }
            
            // Validar teléfono si se proporciona
            if (!empty($telefono) && !preg_match('/^[67]\d{7}$/', $telefono)) {
                throw new Exception('El teléfono debe tener 8 dígitos y comenzar con 6 o 7');
            }
            
            // Validar correo si se proporciona
            if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }
            
            // Verificar si el usuario ya existe
            $stmtCheck = $db->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
            $stmtCheck->execute([$usuario]);
            if ($stmtCheck->fetch()) {
                throw new Exception('El nombre de usuario ya está en uso');
            }
            
            // Hashear contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            // Insertar usuario
            $stmt = $db->prepare("
                INSERT INTO usuarios (nombre, usuario, correo, telefono, contrasena_hash, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$nombre, $usuario, $correo, $telefono, $contrasena_hash, $rol, $estado]);
            $id_usuario = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó usuario #$id_usuario: $usuario (Rol: $rol)");
            
            setAlert('success', 'Usuario creado', 'El usuario se registró correctamente');
            header('Location: ver.php?id=' . $id_usuario);
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_usuario = (int)$_POST['id_usuario'];
            $nombre = sanitize($_POST['nombre']);
            $usuario = sanitize($_POST['usuario']);
            $telefono = isset($_POST['telefono']) ? sanitize($_POST['telefono']) : null;
            $correo = isset($_POST['correo']) ? sanitize($_POST['correo']) : null;
            $rol = sanitize($_POST['rol']);
            $estado = sanitize($_POST['estado']);
            
            // Validaciones
            if (empty($nombre) || empty($usuario) || empty($rol)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            
            if (strlen($usuario) < 3) {
                throw new Exception('El nombre de usuario debe tener al menos 3 caracteres');
            }
            
            if (!in_array($rol, ['administrador', 'barbero', 'apoyo'])) {
                throw new Exception('Rol inválido');
            }
            
            if (!empty($telefono) && !preg_match('/^[67]\d{7}$/', $telefono)) {
                throw new Exception('El teléfono debe tener 8 dígitos y comenzar con 6 o 7');
            }
            
            if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }
            
            // Verificar si el usuario ya está en uso por otro usuario
            $stmtCheck = $db->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ? AND id_usuario != ?");
            $stmtCheck->execute([$usuario, $id_usuario]);
            if ($stmtCheck->fetch()) {
                throw new Exception('El nombre de usuario ya está en uso por otro usuario');
            }
            
            // Actualizar usuario
            $stmt = $db->prepare("
                UPDATE usuarios SET
                    nombre = ?,
                    usuario = ?,
                    correo = ?,
                    telefono = ?,
                    rol = ?,
                    estado = ?
                WHERE id_usuario = ?
            ");
            
            $stmt->execute([$nombre, $usuario, $correo, $telefono, $rol, $estado, $id_usuario]);
            
            // Si hay nueva contraseña
            if (!empty($_POST['nueva_contrasena'])) {
                $nueva_contrasena = $_POST['nueva_contrasena'];
                
                if (strlen($nueva_contrasena) < 6) {
                    throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                }
                
                $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                $stmtPass = $db->prepare("UPDATE usuarios SET contrasena_hash = ? WHERE id_usuario = ?");
                $stmtPass->execute([$contrasena_hash, $id_usuario]);
                
                logAudit($_SESSION['user_id'], "Cambió contraseña del usuario #$id_usuario");
            }
            
            logAudit($_SESSION['user_id'], "Editó usuario #$id_usuario: $usuario");
            
            setAlert('success', 'Usuario actualizado', 'Los cambios se guardaron correctamente');
            header('Location: ver.php?id=' . $id_usuario);
            exit();
            
        case 'cambiar_estado':
            $id_usuario = (int)$_GET['id'];
            $nuevo_estado = sanitize($_GET['estado']);
            
            // Validar estado
            if (!in_array($nuevo_estado, ['activo', 'inactivo'])) {
                throw new Exception('Estado inválido');
            }
            
            // No permitir desactivar el propio usuario
            if ($id_usuario == $_SESSION['user_id'] && $nuevo_estado == 'inactivo') {
                throw new Exception('No puedes desactivar tu propio usuario');
            }
            
            $stmt = $db->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?");
            $stmt->execute([$nuevo_estado, $id_usuario]);
            
            logAudit($_SESSION['user_id'], "Cambió estado del usuario #$id_usuario a '$nuevo_estado'");
            
            setAlert('success', 'Estado actualizado', "El usuario ahora está $nuevo_estado");
            header('Location: index.php');
            exit();
            
        case 'cambiar_contrasena':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_usuario = (int)$_POST['id_usuario'];
            $contrasena_actual = $_POST['contrasena_actual'];
            $nueva_contrasena = $_POST['nueva_contrasena'];
            $confirmar_contrasena = $_POST['confirmar_contrasena'];
            
            // Validar que las contraseñas coincidan
            if ($nueva_contrasena !== $confirmar_contrasena) {
                throw new Exception('Las contraseñas no coinciden');
            }
            
            if (strlen($nueva_contrasena) < 6) {
                throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
            }
            
            // Si no es admin, verificar contraseña actual
            if ($id_usuario == $_SESSION['user_id']) {
                $stmt = $db->prepare("SELECT contrasena_hash FROM usuarios WHERE id_usuario = ?");
                $stmt->execute([$id_usuario]);
                $usuario = $stmt->fetch();
                
                if (!password_verify($contrasena_actual, $usuario['contrasena_hash'])) {
                    throw new Exception('La contraseña actual es incorrecta');
                }
            }
            
            // Actualizar contraseña
            $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET contrasena_hash = ? WHERE id_usuario = ?");
            $stmt->execute([$contrasena_hash, $id_usuario]);
            
            logAudit($_SESSION['user_id'], "Cambió contraseña del usuario #$id_usuario");
            
            setAlert('success', 'Contraseña actualizada', 'La contraseña se cambió correctamente');
            header('Location: ver.php?id=' . $id_usuario);
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en usuarios/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir según el contexto
    if (isset($_POST['id_usuario'])) {
        header('Location: editar.php?id=' . $_POST['id_usuario']);
    } else if (isset($_GET['id'])) {
        header('Location: ver.php?id=' . $_GET['id']);
    } else {
        header('Location: crear.php');
    }
    exit();
}
?>
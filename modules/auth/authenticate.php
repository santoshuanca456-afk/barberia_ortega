<?php
/**
 * Autenticación de Usuario
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setAlert('error', 'Error de seguridad', 'Token CSRF inválido');
    header('Location: login.php');
    exit();
}

// Obtener y sanitizar datos
$usuario = sanitize($_POST['usuario']);
$contrasena = $_POST['contrasena'];
$recordar = isset($_POST['recordar']);

// Validar campos vacíos
if (empty($usuario) || empty($contrasena)) {
    setAlert('warning', 'Campos vacíos', 'Por favor complete todos los campos');
    header('Location: login.php');
    exit();
}

try {
    $db = getDB();
    
    // Buscar usuario en la base de datos
    $stmt = $db->prepare("
        SELECT id_usuario, nombre, usuario, contrasena_hash, rol, estado 
        FROM usuarios 
        WHERE usuario = ? AND estado = 'activo'
    ");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();
    
    // Verificar si el usuario existe y la contraseña es correcta
    if ($user && password_verify($contrasena, $user['contrasena_hash'])) {
        
        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);
        
        // Guardar datos en sesión
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_username'] = $user['usuario'];
        $_SESSION['user_role'] = $user['rol'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Registrar en auditoría
        logAudit($user['id_usuario'], "Inicio de sesión exitoso - IP: " . $_SERVER['REMOTE_ADDR']);
        
        // Si marcó "recordar sesión", extender cookie de sesión
        if ($recordar) {
            ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 días
        }
        
        // Redirigir al dashboard
        header('Location: ' . SITE_URL . 'modules/dashboard/');
        exit();
        
    } else {
        // Usuario no existe o contraseña incorrecta
        
        // Registrar intento fallido (si el usuario existe)
        if ($user) {
            logAudit($user['id_usuario'], "Intento de inicio de sesión fallido - IP: " . $_SERVER['REMOTE_ADDR']);
        }
        
        setAlert('error', 'Error de autenticación', 'Usuario o contraseña incorrectos');
        header('Location: login.php');
        exit();
    }
    
} catch(PDOException $e) {
    error_log("Error en autenticación: " . $e->getMessage());
    setAlert('error', 'Error del sistema', 'Ocurrió un error al intentar iniciar sesión');
    header('Location: login.php');
    exit();
}
?>
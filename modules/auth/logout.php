<?php
/**
 * Cerrar Sesión
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';

// Verificar que el usuario esté logueado
if (isLoggedIn()) {
    // Registrar en auditoría
    logAudit($_SESSION['user_id'], "Cierre de sesión - IP: " . $_SERVER['REMOTE_ADDR']);
    
    // Guardar mensaje antes de destruir sesión
    $userName = $_SESSION['user_name'];
    
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión si existe
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Destruir la sesión
    session_destroy();
    
    // Iniciar nueva sesión para el mensaje
    session_start();
    setAlert('success', 'Sesión cerrada', '¡Hasta pronto, ' . $userName . '!');
}

// Redirigir al login
header('Location: login.php');
exit();
?>
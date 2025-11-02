<?php
/**
 * Configuración General del Sistema
 * Sistema de Gestión - Barbería Ortega
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/La_Paz');

// Configuración del sistema
define('SITE_NAME', 'Barbería Ortega');
define('SITE_URL', 'http://localhost/barberia_ortega/');
define('BASE_PATH', __DIR__ . '/../');

// Configuración de rutas
define('MODULES_PATH', BASE_PATH . 'modules/');
define('INCLUDES_PATH', BASE_PATH . 'includes/');
define('ASSETS_PATH', SITE_URL . 'assets/');
define('UPLOADS_PATH', BASE_PATH . 'uploads/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Configuración de uploads
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Roles de usuario
define('ROLE_ADMIN', 'administrador');
define('ROLE_BARBERO', 'barbero');
define('ROLE_APOYO', 'apoyo');

// Estados de reservas
define('RESERVA_PENDIENTE', 'pendiente');
define('RESERVA_CONFIRMADA', 'confirmada');
define('RESERVA_CANCELADA', 'cancelada');
define('RESERVA_FINALIZADA', 'finalizada');

// Estados de alquileres
define('ALQUILER_VIGENTE', 'vigente');
define('ALQUILER_VENCIDO', 'vencido');
define('ALQUILER_CANCELADO', 'cancelado');

// Métodos de pago
define('PAGO_EFECTIVO', 'efectivo');
define('PAGO_TARJETA', 'tarjeta');
define('PAGO_QR', 'qr');
define('PAGO_OTRO', 'otro');

// Mostrar errores solo en desarrollo
define('DEV_MODE', true);
if (DEV_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Incluir la configuración de base de datos
require_once __DIR__ . '/database.php';

// Función para verificar si el usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Función para verificar el rol del usuario
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Función para requerir autenticación
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'modules/auth/login.php');
        exit();
    }
}

// Función para requerir un rol específico
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: ' . SITE_URL . 'modules/dashboard/');
        exit();
    }
}

// Función para sanitizar entrada
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Función para formatear fecha
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Función para formatear fecha y hora
function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

// Función para formatear moneda boliviana
function formatMoney($amount) {
    return 'Bs ' . number_format($amount, 2, ',', '.');
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para registrar en auditoría
function logAudit($userId, $action) {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO auditoria (id_usuario, accion) VALUES (?, ?)");
        $stmt->execute([$userId, $action]);
    } catch(PDOException $e) {
        error_log("Error en auditoría: " . $e->getMessage());
    }
}

// Función para generar alertas (usando SweetAlert2)
function setAlert($type, $title, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}

// Función para mostrar alertas
function showAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<script>
            Swal.fire({
                icon: '{$alert['type']}',
                title: '{$alert['title']}',
                text: '{$alert['message']}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>";
        unset($_SESSION['alert']);
    }
}
?>
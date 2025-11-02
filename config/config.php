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

// =============================================
// SISTEMA DE ALERTAS Y NOTIFICACIONES MEJORADO
// =============================================

// Función para establecer mensaje de éxito
function setSuccessMessage($message) {
    $_SESSION['success'] = $message;
}

// Función para establecer mensaje de error
function setErrorMessage($message) {
    $_SESSION['error'] = $message;
}

// Función para establecer mensaje de advertencia
function setWarningMessage($message) {
    $_SESSION['warning'] = $message;
}

// Función para establecer mensaje informativo
function setInfoMessage($message) {
    $_SESSION['info'] = $message;
}

// Función para establecer mensaje de login
function setLoginMessage($message) {
    $_SESSION['login'] = $message;
}

// Función para mostrar alertas (compatible con SweetAlert2)
function showAlert() {
    // Alertas de éxito
    if (isset($_SESSION['success'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . addslashes($_SESSION['success']) . "', 'success', 3000);
            });
        </script>";
        unset($_SESSION['success']);
    }
    
    // Alertas de error
    if (isset($_SESSION['error'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . addslashes($_SESSION['error']) . "', 'error', 4000);
            });
        </script>";
        unset($_SESSION['error']);
    }
    
    // Alertas de advertencia
    if (isset($_SESSION['warning'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . addslashes($_SESSION['warning']) . "', 'warning', 3500);
            });
        </script>";
        unset($_SESSION['warning']);
    }
    
    // Alertas informativas
    if (isset($_SESSION['info'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . addslashes($_SESSION['info']) . "', 'info', 3000);
            });
        </script>";
        unset($_SESSION['info']);
    }
    
    // Alertas especiales de login
    if (isset($_SESSION['login'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Bienvenido!',
                    text: '" . addslashes($_SESSION['login']) . "',
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    customClass: {
                        popup: 'animated bounceIn'
                    }
                });
            });
        </script>";
        unset($_SESSION['login']);
    }
}

// =============================================
// SISTEMA DE NOTIFICACIONES
// =============================================

/**
 * Crear una nueva notificación
 */
function createNotification($userId, $titulo, $mensaje, $url = null, $tipo = 'info') {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO notificaciones (id_usuario, titulo, mensaje, url, tipo, fecha_creacion) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $titulo, $mensaje, $url, $tipo]);
    } catch(PDOException $e) {
        error_log("Error al crear notificación: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener notificaciones no leídas de un usuario
 */
function getUnreadNotifications($userId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM notificaciones 
            WHERE id_usuario = ? AND leido = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    } catch(PDOException $e) {
        error_log("Error al obtener notificaciones: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener últimas notificaciones de un usuario
 */
function getRecentNotifications($userId, $limit = 5) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT * FROM notificaciones 
            WHERE id_usuario = ? 
            ORDER BY fecha_creacion DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error al obtener notificaciones recientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Marcar notificaciones como leídas
 */
function markNotificationsAsRead($userId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            UPDATE notificaciones 
            SET leido = 1, fecha_leido = NOW() 
            WHERE id_usuario = ? AND leido = 0
        ");
        return $stmt->execute([$userId]);
    } catch(PDOException $e) {
        error_log("Error al marcar notificaciones como leídas: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para formatear fecha relativa (para notificaciones)
 */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'hace ' . implode(', ', $string) : 'justo ahora';
}

// =============================================
// FUNCIONES DE NOTIFICACIONES AUTOMÁTICAS
// =============================================

/**
 * Notificar nueva reserva
 */
function notifyNewReservation($reservaId, $clienteNombre, $barberoNombre, $fecha, $hora) {
    try {
        $db = getDB();
        
        // Notificar al barbero
        $stmt = $db->prepare("SELECT id_usuario FROM reservas WHERE id_reserva = ?");
        $stmt->execute([$reservaId]);
        $reserva = $stmt->fetch();
        
        if ($reserva) {
            $titulo = "Nueva Reserva";
            $mensaje = "Tienes una nueva reserva con $clienteNombre para el $fecha a las $hora";
            $url = SITE_URL . "modules/reservas/ver.php?id=" . $reservaId;
            
            createNotification($reserva['id_usuario'], $titulo, $mensaje, $url, 'info');
        }
        
        // Notificar a administradores
        $stmt = $db->prepare("SELECT id_usuario FROM usuarios WHERE rol = ? AND estado = 'activo'");
        $stmt->execute([ROLE_ADMIN]);
        $admins = $stmt->fetchAll();
        
        foreach ($admins as $admin) {
            $titulo = "Nueva Reserva Creada";
            $mensaje = "Se creó una reserva para $clienteNombre con $barberoNombre - $fecha $hora";
            $url = SITE_URL . "modules/reservas/ver.php?id=" . $reservaId;
            
            createNotification($admin['id_usuario'], $titulo, $mensaje, $url, 'info');
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Error en notificación de reserva: " . $e->getMessage());
        return false;
    }
}

/**
 * Notificar reserva cancelada
 */
function notifyCancelledReservation($reservaId, $clienteNombre, $motivo = '') {
    try {
        $db = getDB();
        
        // Obtener información de la reserva
        $stmt = $db->prepare("
            SELECT r.id_usuario, u.nombre as barbero_nombre, r.fecha, r.hora_inicio 
            FROM reservas r 
            JOIN usuarios u ON r.id_usuario = u.id_usuario 
            WHERE r.id_reserva = ?
        ");
        $stmt->execute([$reservaId]);
        $reserva = $stmt->fetch();
        
        if ($reserva) {
            $titulo = "Reserva Cancelada";
            $mensaje = "La reserva con $clienteNombre para el " . formatDate($reserva['fecha']) . 
                      " a las " . substr($reserva['hora_inicio'], 0, 5) . " ha sido cancelada" . 
                      ($motivo ? ". Motivo: $motivo" : "");
            $url = SITE_URL . "modules/reservas/";
            
            createNotification($reserva['id_usuario'], $titulo, $mensaje, $url, 'warning');
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Error en notificación de cancelación: " . $e->getMessage());
        return false;
    }
}

/**
 * Notificar pago realizado
 */
function notifyPayment($reservaId, $clienteNombre, $monto, $metodoPago) {
    try {
        $db = getDB();
        
        // Obtener información de la reserva
        $stmt = $db->prepare("
            SELECT r.id_usuario, u.nombre as barbero_nombre 
            FROM reservas r 
            JOIN usuarios u ON r.id_usuario = u.id_usuario 
            WHERE r.id_reserva = ?
        ");
        $stmt->execute([$reservaId]);
        $reserva = $stmt->fetch();
        
        if ($reserva) {
            $titulo = "Pago Recibido";
            $mensaje = "Se recibió pago de " . formatMoney($monto) . " ($metodoPago) de $clienteNombre";
            $url = SITE_URL . "modules/pagos/ver.php?id=" . $reservaId;
            
            createNotification($reserva['id_usuario'], $titulo, $mensaje, $url, 'success');
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Error en notificación de pago: " . $e->getMessage());
        return false;
    }
}
?>
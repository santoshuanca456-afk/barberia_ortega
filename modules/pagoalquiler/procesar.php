<?php
/**
 * Procesar Acciones de Pagos de Alquiler
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

try {
    switch ($action) {
        case 'registrar_alquiler':
            error_log("Procesando registro de pago de alquiler...");
            
            // Verificar token CSRF si está configurado
            if (function_exists('verifyCSRFToken') && (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token']))) {
                throw new Exception('Token de seguridad inválido');
            }
            
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
            header('Location: index.php');
            exit();
            break;

        case 'eliminar_pago_alquiler':
            // Solo administradores pueden eliminar pagos
            if (!hasRole(ROLE_ADMIN)) {
                throw new Exception('No tienes permisos para realizar esta acción');
            }
            
            $id_pago = intval($_GET['id']);
            
            // Verificar que el pago existe
            $stmt = $db->prepare("
                SELECT pa.*, a.id_alquiler, e.nombre as estacion_nombre
                FROM pagos_alquiler pa
                LEFT JOIN alquileres a ON pa.id_alquiler = a.id_alquiler
                LEFT JOIN estaciones e ON a.id_estacion = e.id_estacion
                WHERE pa.id_pago_alquiler = ?
            ");
            $stmt->execute([$id_pago]);
            $pago = $stmt->fetch();
            
            if (!$pago) {
                throw new Exception('El pago no existe');
            }
            
            // Eliminar pago de alquiler
            $stmt = $db->prepare("DELETE FROM pagos_alquiler WHERE id_pago_alquiler = ?");
            $stmt->execute([$id_pago]);
            
            // Registrar en auditoría
            $accion = "Eliminó pago de alquiler #$id_pago - Contrato #{$pago['id_alquiler']} - Estación: {$pago['estacion_nombre']}";
            $stmt_auditoria = $db->prepare("INSERT INTO auditoria (id_usuario, accion) VALUES (?, ?)");
            $stmt_auditoria->execute([$_SESSION['id_usuario'], $accion]);
            
            setAlert('success', 'Pago eliminado', 'El pago de alquiler fue eliminado correctamente');
            header('Location: index.php');
            exit();
            break;

        default:
            throw new Exception('Acción no válida: ' . $action);
    }
    
} catch (Exception $e) {
    error_log("Error en pagoalquiler/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    
    // Redirigir según el contexto
    if ($action == 'registrar_alquiler') {
        header('Location: registrar_alquiler.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
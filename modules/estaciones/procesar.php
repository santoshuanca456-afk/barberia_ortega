<?php
/**
 * Procesar Acciones de Estaciones
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
            $descripcion = isset($_POST['descripcion']) ? sanitize($_POST['descripcion']) : null;
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre de la estación es obligatorio');
            }
            
            // Verificar que no exista una estación con el mismo nombre
            $stmtCheck = $db->prepare("SELECT id_estacion FROM estaciones WHERE nombre = ?");
            $stmtCheck->execute([$nombre]);
            if ($stmtCheck->fetch()) {
                throw new Exception('Ya existe una estación con este nombre');
            }
            
            // Insertar estación
            $stmt = $db->prepare("
                INSERT INTO estaciones (nombre, descripcion, disponible) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$nombre, $descripcion, $disponible]);
            $id_estacion = $db->lastInsertId();
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó estación #$id_estacion: $nombre");
            
            setAlert('success', 'Estación creada', 'La estación se registró correctamente');
            header('Location: index.php');
            exit();
            
        case 'editar':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_estacion = (int)$_POST['id_estacion'];
            $nombre = sanitize($_POST['nombre']);
            $descripcion = isset($_POST['descripcion']) ? sanitize($_POST['descripcion']) : null;
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre de la estación es obligatorio');
            }
            
            // Verificar que el nombre no esté en uso por otra estación
            $stmtCheck = $db->prepare("SELECT id_estacion FROM estaciones WHERE nombre = ? AND id_estacion != ?");
            $stmtCheck->execute([$nombre, $id_estacion]);
            if ($stmtCheck->fetch()) {
                throw new Exception('Ya existe otra estación con este nombre');
            }
            
            // Actualizar estación
            $stmt = $db->prepare("
                UPDATE estaciones SET
                    nombre = ?,
                    descripcion = ?,
                    disponible = ?
                WHERE id_estacion = ?
            ");
            
            $stmt->execute([$nombre, $descripcion, $disponible, $id_estacion]);
            
            logAudit($_SESSION['user_id'], "Editó estación #$id_estacion: $nombre");
            
            setAlert('success', 'Estación actualizada', 'Los cambios se guardaron correctamente');
            header('Location: index.php');
            exit();
            
        case 'toggle_disponibilidad':
            $id_estacion = (int)$_GET['id'];
            $disponible = (int)$_GET['disponible'];
            
            $stmt = $db->prepare("UPDATE estaciones SET disponible = ? WHERE id_estacion = ?");
            $stmt->execute([$disponible, $id_estacion]);
            
            $estado = $disponible ? 'disponible' : 'no disponible';
            logAudit($_SESSION['user_id'], "Marcó estación #$id_estacion como $estado");
            
            setAlert('success', 'Estado actualizado', "La estación ahora está marcada como $estado");
            header('Location: index.php');
            exit();
            
        case 'crear_alquiler':
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                throw new Exception('Token CSRF inválido');
            }
            
            $id_estacion = (int)$_POST['id_estacion'];
            $id_usuario = (int)$_POST['id_usuario'];
            $fecha_inicio = sanitize($_POST['fecha_inicio']);
            $fecha_fin = sanitize($_POST['fecha_fin']);
            $monto = (float)$_POST['monto'];
            
            // Validaciones
            if (empty($id_estacion) || empty($id_usuario) || empty($fecha_inicio) || empty($fecha_fin)) {
                throw new Exception('Todos los campos obligatorios deben ser completados');
            }
            
            if ($monto <= 0) {
                throw new Exception('El monto debe ser mayor a 0');
            }
            
            if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
                throw new Exception('La fecha de fin debe ser posterior a la fecha de inicio');
            }
            
            // Verificar que no exista un alquiler vigente para esta estación
            $stmtCheck = $db->prepare("
                SELECT id_alquiler 
                FROM alquileres 
                WHERE id_estacion = ? AND estado = 'vigente'
            ");
            $stmtCheck->execute([$id_estacion]);
            if ($stmtCheck->fetch()) {
                throw new Exception('Esta estación ya tiene un alquiler vigente');
            }
            
            // Procesar archivo PDF si existe
            $contrato_pdf = null;
            if (isset($_FILES['contrato_pdf']) && $_FILES['contrato_pdf']['error'] == 0) {
                $file = $_FILES['contrato_pdf'];
                
                // Validar tipo de archivo
                if ($file['type'] != 'application/pdf') {
                    throw new Exception('Solo se permiten archivos PDF');
                }
                
                // Validar tamaño (5MB)
                if ($file['size'] > MAX_FILE_SIZE) {
                    throw new Exception('El archivo excede el tamaño máximo permitido (5MB)');
                }
                
                // Crear directorio si no existe
                $uploadDir = UPLOADS_PATH . 'contratos/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generar nombre único
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'contrato_' . $id_estacion . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                // Mover archivo
                if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                    throw new Exception('Error al subir el archivo');
                }
                
                $contrato_pdf = 'contratos/' . $filename;
            }
            
            // Insertar alquiler
            $stmt = $db->prepare("
                INSERT INTO alquileres (id_estacion, id_usuario, fecha_inicio, fecha_fin, monto, contrato_pdf, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 'vigente')
            ");
            
            $stmt->execute([$id_estacion, $id_usuario, $fecha_inicio, $fecha_fin, $monto, $contrato_pdf]);
            $id_alquiler = $db->lastInsertId();
            
            // Marcar estación como no disponible
            $stmtUpdate = $db->prepare("UPDATE estaciones SET disponible = FALSE WHERE id_estacion = ?");
            $stmtUpdate->execute([$id_estacion]);
            
            // Registrar en auditoría
            logAudit($_SESSION['user_id'], "Creó alquiler #$id_alquiler para estación #$id_estacion");
            
            setAlert('success', 'Alquiler creado', 'El alquiler se registró correctamente');
            header('Location: alquileres.php?estacion=' . $id_estacion);
            exit();
            
        case 'finalizar_alquiler':
            requireRole(ROLE_ADMIN);
            
            $id_alquiler = (int)$_GET['id'];
            
            // Obtener información del alquiler
            $stmt = $db->prepare("SELECT id_estacion FROM alquileres WHERE id_alquiler = ?");
            $stmt->execute([$id_alquiler]);
            $alquiler = $stmt->fetch();
            
            if (!$alquiler) {
                throw new Exception('Alquiler no encontrado');
            }
            
            // Cambiar estado a vencido/cancelado
            $stmtUpdate = $db->prepare("UPDATE alquileres SET estado = 'cancelado' WHERE id_alquiler = ?");
            $stmtUpdate->execute([$id_alquiler]);
            
            // Liberar estación
            $stmtEstacion = $db->prepare("UPDATE estaciones SET disponible = TRUE WHERE id_estacion = ?");
            $stmtEstacion->execute([$alquiler['id_estacion']]);
            
            logAudit($_SESSION['user_id'], "Finalizó alquiler #$id_alquiler");
            
            setAlert('success', 'Alquiler finalizado', 'El alquiler se finalizó y la estación quedó disponible');
            header('Location: index.php');
            exit();
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en estaciones/procesar.php: " . $e->getMessage());
    setAlert('error', 'Error', $e->getMessage());
    header('Location: index.php');
    exit();
}
?>
<?php
/**
 * Obtener lista de clientes para select (AJAX)
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$db = getDB();

try {
    // Obtener clientes activos
    $stmt = $db->query("
        SELECT id_cliente, nombre, telefono 
        FROM clientes 
        WHERE estado = 'activo' OR estado IS NULL
        ORDER BY nombre
    ");

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Configurar headers para respuesta JSON
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    echo json_encode([
        'success' => true,
        'data' => $clientes
    ]);
    
} catch (Exception $e) {
    // En caso de error, devolver respuesta de error
    header('Content-Type: application/json');
    header('HTTP/1.1 500 Internal Server Error');
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar clientes: ' . $e->getMessage()
    ]);
}
?>
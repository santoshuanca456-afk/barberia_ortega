<?php
/**
 * Configuración de Conexión a Base de Datos
 * Sistema de Gestión - Barbería Ortega
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barberia_ortega');
define('DB_CHARSET', 'utf8mb4');

// Clase de conexión a la base de datos
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    // Patrón Singleton para una única instancia
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener la conexión
    public function getConnection() {
        return $this->conn;
    }
    
    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Función helper para obtener la conexión fácilmente
function getDB() {
    return Database::getInstance()->getConnection();
}

// Probar la conexión (comentar después de verificar)
/*
try {
    $db = getDB();
    echo "✅ Conexión exitosa a la base de datos!";
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
*/
?>
<?php
/**
 * EasyBuy Uganda - Database Configuration
 * Version: 2.0.0
 */

// Load main config if not already loaded
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Database connection class with singleton pattern
 */
class DatabaseConfig {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get database instance (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Log successful connection in debug mode
            if (isDebug()) {
                logMessage('Database connection established successfully', 'info');
            }
        } catch (PDOException $e) {
            logMessage('Database connection failed: ' . $e->getMessage(), 'critical');
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get the PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a query with parameters
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            logMessage('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql, 'error');
            throw $e;
        }
    }
    
    /**
     * Insert data into table
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES ($placeholders)";
        $stmt = $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update data in table
     */
    public function update($table, $data, $where, $whereParams = []) {
        $fields = array_map(function($field) {
            return "$field = :$field";
        }, array_keys($data));
        $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $where";
        $params = array_merge($data, $whereParams);
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * Delete data from table
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Check if table exists
     */
    public function tableExists($table) {
        try {
            $result = $this->query("SHOW TABLES LIKE ?", [$table]);
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get table columns
     */
    public function getColumns($table) {
        $result = $this->query("DESCRIBE $table");
        return $result->fetchAll();
    }
}

// Create and return database instance
function db() {
    return DatabaseConfig::getInstance();
}

// For backward compatibility
class Database extends DatabaseConfig {
    public static function getInstance() {
        return parent::getInstance();
    }
}
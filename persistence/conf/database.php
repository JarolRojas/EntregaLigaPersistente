<?php
class Database {
    private static ?self $instance = null;
    private \mysqli $connection;
    
    private const DB_HOST = 'localhost';
    private const DB_USER = 'root';
    private const DB_PASSWORD = '';
    private const DB_NAME = 'futbol_persistencia';
    
    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Obtiene la instancia única de Database
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establece la conexión con la base de datos
     */
    private function connect(): void {
        $this->connection = new \mysqli(
            self::DB_HOST,
            self::DB_USER,
            self::DB_PASSWORD,
            self::DB_NAME
        );
        
        if ($this->connection->connect_error) {
            throw new \Exception('Error de conexión: ' . $this->connection->connect_error);
        }
        
        $this->connection->set_charset('utf8mb4');
    }
    
    /**
     * Obtiene la conexión de mysqli
     */
    public function getConnection(): \mysqli {
        return $this->connection;
    }
    
    /**
     * Ejecuta una consulta preparada
     * 
     * @param string $query Consulta SQL con placeholders (?)
     * @param array $params Parámetros a vincular
     * @param string $types Tipos de datos de los parámetros
     * @return \mysqli_result|bool
     */
    public function executeQuery(string $query, array $params = [], string $types = ''): \mysqli_result|bool {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            throw new \Exception('Error en preparación: ' . $this->connection->error);
        }
        
        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new \Exception('Error en ejecución: ' . $stmt->error);
        }
        
        return $stmt->get_result();
    }
    
    /**
     * Ejecuta una consulta y obtiene una única fila como array asociativo
     */
    public function fetchOne(string $query, array $params = [], string $types = ''): ?array {
        $result = $this->executeQuery($query, $params, $types);
        return $result ? $result->fetch_assoc() : null;
    }
    
    /**
     * Ejecuta una consulta y obtiene todas las filas como array de arrays
     */
    public function fetchAll(string $query, array $params = [], string $types = ''): array {
        $result = $this->executeQuery($query, $params, $types);
        $rows = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }
    
    /**
     * Obtiene el ID de la última fila insertada
     */
    public function getLastInsertId(): int {
        return $this->connection->insert_id;
    }
    
    /**
     * Obtiene el número de filas afectadas por la última consulta
     */
    public function getAffectedRows(): int {
        return $this->connection->affected_rows;
    }
    
    /**
     * Inicia una transacción
     */
    public function beginTransaction(): void {
        $this->connection->begin_transaction();
    }
    
    /**
     * Confirma una transacción
     */
    public function commit(): void {
        $this->connection->commit();
    }
    
    /**
     * Revierte una transacción
     */
    public function rollback(): void {
        $this->connection->rollback();
    }
    
    /**
     * Cierra la conexión
     */
    public function closeConnection(): void {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
    
    /**
     * Previene la clonación
     */
    private function __clone() {}
    
}
?>
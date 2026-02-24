<?php
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            die();
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function select($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }
    
    public function selectOne($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch(PDOException $e) {
            echo "Query Error: " . $e->getMessage();
            return null;
        }
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            echo "Insert Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach(array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);
        
        $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute(array_merge($data, $whereParams));
        } catch(PDOException $e) {
            echo "Update Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function delete($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            echo "Delete Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function count($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch(PDOException $e) {
            echo "Count Error: " . $e->getMessage();
            return 0;
        }
    }
}

$db = new Database();

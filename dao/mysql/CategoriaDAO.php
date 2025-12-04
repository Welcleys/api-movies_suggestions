<?php
namespace dao\mysql;

use generic\MysqlSingleton;

class CategoriaDAO {
    private $conn;
    private $table_name = "categorias";

    public function __construct(MysqlSingleton $conn) {
        $this->conn = $conn;
    }

    public function create(string $nome): bool {
        $query = "INSERT INTO " . $this->table_name . " (nome) VALUES (?)";
        $params = [$nome];
        
        return $this->conn->executeNonQuery($query, $params) > 0;
    }

    public function findAll(): array {
        $query = "SELECT id, nome FROM " . $this->table_name . " ORDER BY nome ASC";
        $stmt = $this->conn->prepared($query); 
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findById(int $id): ?array {
        $query = "SELECT id, nome FROM " . $this->table_name . " WHERE id = ?";
        $params = [$id];
        $stmt = $this->conn->prepared($query, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
    
    public function update(int $id, string $nome): bool {
        $query = "UPDATE " . $this->table_name . " SET nome = ? WHERE id = ?";
        $params = [$nome, $id];
        
        return $this->conn->executeNonQuery($query, $params) > 0;
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $params = [$id];
        
        return $this->conn->executeNonQuery($query, $params) > 0;
    }
}
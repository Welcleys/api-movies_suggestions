<?php
namespace dao\mysql;

use generic\MysqlSingleton; // Usa a nova classe de conexão

class CategoriaDAO {
    private $conn;
    private $table_name = "categorias";

    // Recebe a conexão via injeção de dependência (do MysqlFactory)
    public function __construct(MysqlSingleton $conn) {
        $this->conn = $conn;
    }

    // CREATE (Agora usando prepared statements do MysqlSingleton)
    public function create(string $nome): bool {
        $query = "INSERT INTO " . $this->table_name . " (nome) VALUES (?)";
        $params = [$nome];
        
        // Retorna true se a execução foi bem-sucedida (rowCount > 0)
        return $this->conn->executeNonQuery($query, $params) > 0;
    }

    // READ ALL
    public function findAll(): array {
        $query = "SELECT id, nome FROM " . $this->table_name . " ORDER BY nome ASC";
        // Usa o método prepared para evitar injeção, mesmo sem parâmetros de entrada
        $stmt = $this->conn->prepared($query); 
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // READ ONE
    public function findById(int $id): ?array {
        $query = "SELECT id, nome FROM " . $this->table_name . " WHERE id = ?";
        $params = [$id];
        $stmt = $this->conn->prepared($query, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
    
    // UPDATE
    public function update(int $id, string $nome): bool {
        $query = "UPDATE " . $this->table_name . " SET nome = ? WHERE id = ?";
        $params = [$nome, $id];
        
        return $this->conn->executeNonQuery($query, $params) > 0;
    }

    // DELETE
    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $params = [$id];
        
        return $this->conn->executeNonQuery($query, $params) > 0;
    }
}
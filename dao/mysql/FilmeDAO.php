<?php
namespace dao\mysql;

use generic\MysqlSingleton;

class FilmeDAO {
    private $conn;
    private $table_name = "filmes";

    public function __construct(MysqlSingleton $conn) {
        $this->conn = $conn;
    }

    public function create(string $titulo, int $ano_lancamento): bool {
        $query = "INSERT INTO " . $this->table_name . " (titulo, ano_lancamento) VALUES (?, ?)";
        return $this->conn->executeNonQuery($query, [$titulo, $ano_lancamento]) > 0;
    }

    public function findAll(): array {
        $query = "SELECT id, titulo, ano_lancamento FROM " . $this->table_name . " ORDER BY titulo ASC";
        $stmt = $this->conn->prepared($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $query = "SELECT id, titulo, ano_lancamento FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepared($query, [$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    public function update(int $id, string $titulo, int $ano_lancamento): bool {
        $query = "UPDATE " . $this->table_name . " SET titulo = ?, ano_lancamento = ? WHERE id = ?";
        return $this->conn->executeNonQuery($query, [$titulo, $ano_lancamento, $id]) > 0;
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        return $this->conn->executeNonQuery($query, [$id]) > 0;
    }
}
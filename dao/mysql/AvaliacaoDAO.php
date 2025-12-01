<?php
namespace dao\mysql;

use generic\MysqlSingleton;

class AvaliacaoDAO {
    private $conn;
    private $table_name = "avaliacoes";

    public function __construct(MysqlSingleton $conn) {
        $this->conn = $conn;
    }

    public function create(int $filme_id, int $categoria_id, int $nota): bool {
        $query = "INSERT INTO " . $this->table_name . " (filme_id, categoria_id, nota) VALUES (?, ?, ?)";
        return $this->conn->executeNonQuery($query, [$filme_id, $categoria_id, $nota]) > 0;
    }

    public function findAll(): array {
        $query = "SELECT a.id, a.nota, f.titulo AS filme_titulo, c.nome AS categoria_nome 
                  FROM " . $this->table_name . " a
                  JOIN filmes f ON a.filme_id = f.id
                  JOIN categorias c ON a.categoria_id = c.id
                  ORDER BY a.id DESC";
        $stmt = $this->conn->prepared($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $query = "SELECT id, filme_id, categoria_id, nota FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepared($query, [$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    public function update(int $id, int $filme_id, int $categoria_id, int $nota): bool {
        $query = "UPDATE " . $this->table_name . " SET filme_id = ?, categoria_id = ?, nota = ? WHERE id = ?";
        return $this->conn->executeNonQuery($query, [$filme_id, $categoria_id, $nota, $id]) > 0;
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        return $this->conn->executeNonQuery($query, [$id]) > 0;
    }
}
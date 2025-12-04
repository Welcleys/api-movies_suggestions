<?php
namespace service;

use generic\MysqlFactory;

class CategoriaService {
    private $dao;

    public function __construct() {
        $this->dao = MysqlFactory::createCategoriaDAO();
    }
    
    // Mapeado para GET /categoria
    // Retorna os dados; o código HTTP 200 será o padrão no ponto de saída
    public function getAll(): array {
        return $this->dao->findAll();
    }
    
    public function getById(array $data): ?array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID inválido: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID inválido para busca.'];
        }
        
        $resultado = $this->dao->findById($id);

        if (!$resultado) {
            // Não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria não encontrada.'];
        }

        // Retorna o resultado encontrado (o status 200 será o padrão)
        return $resultado;
    }

    // Mapeado para POST /categoria
    public function create(array $data): array {
        $nome = trim($data['nome'] ?? '');
        
        if (empty($nome)) {
            // Validação de campos: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'O nome da categoria não pode ser vazio.'];
        }
        
        if ($this->dao->create($nome)) {
            // Sucesso na criação: 201 Created
            return ['http_code' => 201, 'status' => 'success', 'message' => 'Categoria criada com sucesso.'];
        } else {
            // Falha interna: 500 Internal Server Error
            return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar categoria.'];
        }
    }
    
    // Mapeado para PUT/PATCH /categoria
    public function update(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        $nome = trim($data['nome'] ?? '');

        if ($id <= 0 || empty($nome)) {
            // Validação de campos: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID e nome são necessários para atualização.'];
        }

        if (!$this->dao->findById($id)) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria a ser atualizada não encontrada.'];
        }

        if ($this->dao->update($id, $nome)) {
            // Sucesso na atualização: 200 OK
            return ['http_code' => 200, 'status' => 'success', 'message' => 'Categoria atualizada com sucesso.'];
        }

        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar categoria.'];
    }

    // Mapeado para DELETE /categoria
    public function delete(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID ausente: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria não encontrada.'];
        }
        
        try {
            if ($this->dao->delete($id)) {
                // Sucesso na exclusão: 200 OK
                return ['http_code' => 200, 'status' => 'success', 'message' => 'Categoria excluída com sucesso.'];
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {
                // Conflito de Integridade (Chave Estrangeira): 409 Conflict
                return ['http_code' => 409, 'status' => 'error', 'message' => 'Não é possível excluir a categoria, pois ela possui registros associados.'];
            }
        }
        // Falha interna (catch geral): 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir categoria.'];
    }
}
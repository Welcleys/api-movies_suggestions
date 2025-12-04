<?php
namespace service;

use generic\MysqlFactory;

class CategoriaService {
    private $dao;

    public function __construct() {
        $this->dao = MysqlFactory::createCategoriaDAO();
    }
    
    public function getAll(): array {
        return $this->dao->findAll();
    }
    
    public function getById(array $data): ?array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID inválido para busca.'];
        }
        
        $resultado = $this->dao->findById($id);

        if (!$resultado) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria não encontrada.'];
        }

        return $resultado;
    }

    public function create(array $data): array {
        $nome = trim($data['nome'] ?? '');
        
        if (empty($nome)) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'O nome da categoria não pode ser vazio.'];
        }
        
        if ($this->dao->create($nome)) {

            return ['http_code' => 201, 'status' => 'success', 'message' => 'Categoria criada com sucesso.'];
        } else {

            return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar categoria.'];
        }
    }

    public function update(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        $nome = trim($data['nome'] ?? '');

        if ($id <= 0 || empty($nome)) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID e nome são necessários para atualização.'];
        }

        if (!$this->dao->findById($id)) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria a ser atualizada não encontrada.'];
        }

        if ($this->dao->update($id, $nome)) {

            return ['http_code' => 200, 'status' => 'success', 'message' => 'Categoria atualizada com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar categoria.'];
    }

    public function delete(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Categoria não encontrada.'];
        }
        
        try {
            if ($this->dao->delete($id)) {

                return ['http_code' => 200, 'status' => 'success', 'message' => 'Categoria excluída com sucesso.'];
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {

                return ['http_code' => 409, 'status' => 'error', 'message' => 'Não é possível excluir a categoria, pois ela possui registros associados.'];
            }
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir categoria.'];
    }
}
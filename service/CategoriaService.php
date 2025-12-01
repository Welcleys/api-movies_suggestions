<?php
namespace service;

use generic\MysqlFactory; // Usa a nova Factory para criar o DAO

class CategoriaService {
    private $dao;

    public function __construct() {
        // Obter o DAO via Factory (injeção indireta de dependência)
        $this->dao = MysqlFactory::createCategoriaDAO();
    }
    
    // ... (Métodos getAll, getById, create, update, delete permanecem iguais na lógica)
    
    public function getAll(): array {
        return $this->dao->findAll();
    }
    
    // NOVO MÉTODO: Lógica para buscar por ID (sem validações complexas, apenas chama o DAO)
    public function getById(array $data): ?array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // Regra de Negócio: ID inválido
            return ['success' => false, 'message' => 'ID inválido para busca.'];
        }
        
        // Chama o método findById da camada DAO
        $resultado = $this->dao->findById($id);

        if (!$resultado) {
            return ['success' => false, 'message' => 'Categoria não encontrada.'];
        }

        // Retorna o array de dados (ou null, se preferir que o Controller trate isso)
        return $resultado;
    }
    
    // ... (implemente o restante do CRUD Service aqui)
    
    // Lógica para Criação com Regras de Negócio
    public function create(array $data): array {
        $nome = trim($data['nome'] ?? '');
        
        if (empty($nome)) {
            return ['success' => false, 'message' => 'O nome da categoria não pode ser vazio.'];
        }
        
        if ($this->dao->create($nome)) {
            return ['success' => true, 'message' => 'Categoria criada com sucesso.'];
        } else {
            return ['success' => false, 'message' => 'Erro interno ao criar categoria.'];
        }
    }
    
    public function update(array $data): array {
         // O Controller deve garantir que o 'id' e 'nome' estão nos dados
         $id = (int) ($data['id'] ?? 0);
         $nome = trim($data['nome'] ?? '');

         if ($id <= 0 || empty($nome)) {
             return ['success' => false, 'message' => 'ID e nome são necessários para atualização.'];
         }

         if (!$this->dao->findById($id)) {
             return ['success' => false, 'message' => 'Categoria a ser atualizada não encontrada.'];
         }

         if ($this->dao->update($id, $nome)) {
             return ['success' => true, 'message' => 'Categoria atualizada com sucesso.'];
         }

         return ['success' => false, 'message' => 'Falha ao atualizar categoria.'];
    }

    public function delete(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            return ['success' => false, 'message' => 'Categoria não encontrada.'];
        }
        
        try {
            if ($this->dao->delete($id)) {
                return ['success' => true, 'message' => 'Categoria excluída com sucesso.'];
            }
        } catch (\PDOException $e) {
            // Captura o erro de chave estrangeira
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {
                 return ['success' => false, 'message' => 'Não é possível excluir a categoria, pois ela possui registros associados.'];
            }
        }
        return ['success' => false, 'message' => 'Falha ao excluir categoria.'];
    }
}
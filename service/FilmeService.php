<?php
namespace service;

use generic\MysqlFactory;

class FilmeService {
    private $dao;

    public function __construct() {
        $this->dao = MysqlFactory::createFilmeDAO();
    }

    public function getAll(): array {
        return $this->dao->findAll();
    }
    
    public function getById(array $data): ?array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID inválido para busca.'];
        }
        
        return $this->dao->findById($id);
    }

    public function create(array $data): array {
        $titulo = trim($data['titulo'] ?? '');
        $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);
        
        if (empty($titulo) || $ano_lancamento <= 0) {
            return ['success' => false, 'message' => 'Título e Ano de Lançamento são obrigatórios e válidos.'];
        }

        if ($this->dao->create($titulo, $ano_lancamento)) {
            return ['success' => true, 'message' => 'Filme criado com sucesso.'];
        }
        return ['success' => false, 'message' => 'Erro interno ao criar filme.'];
    }
    
    public function atualizar(array $data): array {
         $id = (int) ($data['id'] ?? 0);
         $titulo = trim($data['titulo'] ?? '');
         $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);

         if ($id <= 0 || empty($titulo) || $ano_lancamento <= 0) {
             return ['success' => false, 'message' => 'ID, Título e Ano de Lançamento são necessários para atualização.'];
         }

         if (!$this->dao->findById($id)) {
             return ['success' => false, 'message' => 'Filme a ser atualizado não encontrado.'];
         }

         if ($this->dao->update($id, $titulo, $ano_lancamento)) {
             return ['success' => true, 'message' => 'Filme atualizado com sucesso.'];
         }
         return ['success' => false, 'message' => 'Falha ao atualizar filme.'];
    }
    
    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            return ['success' => false, 'message' => 'Filme não encontrado.'];
        }

        try {
            if ($this->dao->delete($id)) {
                return ['success' => true, 'message' => 'Filme excluído com sucesso.'];
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {
                 return ['success' => false, 'message' => 'Não é possível excluir o filme, pois ele possui avaliações associadas.'];
            }
        }
        return ['success' => false, 'message' => 'Falha ao excluir filme.'];
    }
}
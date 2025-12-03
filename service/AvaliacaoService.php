<?php
namespace service;

use generic\MysqlFactory;

class AvaliacaoService {
    private $dao;
    private $filmeDao;
    private $categoriaDao;

    public function __construct() {
        $this->dao = MysqlFactory::createAvaliacaoDAO();
        $this->filmeDao = MysqlFactory::createFilmeDAO();
        $this->categoriaDao = MysqlFactory::createCategoriaDAO();
    }
    
    private function validateData(array $data): array {
        $filme_id = (int) ($data['filme_id'] ?? 0);
        $categoria_id = (int) ($data['categoria_id'] ?? 0);
        $nota = (int) ($data['nota'] ?? 0);
        
        if ($filme_id <= 0 || $categoria_id <= 0 || $nota <= 0) {
            return ['success' => false, 'message' => 'filme_id, categoria_id e nota são obrigatórios.'];
        }

        if ($nota < 1 || $nota > 10) { // Exemplo de regra: nota entre 1 e 10
            return ['success' => false, 'message' => 'A nota deve ser um valor entre 1 e 10.'];
        }
        
        if (!$this->filmeDao->findById($filme_id)) {
            return ['success' => false, 'message' => 'Filme com ID ' . $filme_id . ' não encontrado.'];
        }

        if (!$this->categoriaDao->findById($categoria_id)) {
            return ['success' => false, 'message' => 'Categoria com ID ' . $categoria_id . ' não encontrada.'];
        }

        return ['success' => true, 'data' => ['filme_id' => $filme_id, 'categoria_id' => $categoria_id, 'nota' => $nota]];
    }

    public function getAll(): array {
        return $this->dao->findAll();
    }

    public function create(array $data): array {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }
        
        $d = $validation['data'];
        if ($this->dao->create($d['filme_id'], $d['categoria_id'], $d['nota'])) {
            return ['success' => true, 'message' => 'Avaliação criada com sucesso.'];
        }

        return ['success' => false, 'message' => 'Erro interno ao criar avaliação.'];
    }
    
    public function atualizar(array $data): array {
    $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID é obrigatório para atualização.'];
        }

        // 1. Busca a avaliação existente (dados atuais)
        $avaliacaoExistente = $this->dao->findById($id);
        if (!$avaliacaoExistente) {
            return ['success' => false, 'message' => 'Avaliação a ser atualizada não encontrada.'];
        }

        // 2. Define os novos valores, mantendo os antigos se não forem enviados
        $filme_id = (int) ($data['filme_id'] ?? $avaliacaoExistente['filme_id']);
        $categoria_id = (int) ($data['categoria_id'] ?? $avaliacaoExistente['categoria_id']);
        $nota = (int) ($data['nota'] ?? $avaliacaoExistente['nota']);

        // 3. Validação Básica dos campos que podem ter sido alterados
        if ($filme_id <= 0 || $categoria_id <= 0 || $nota <= 0) {
            return ['success' => false, 'message' => 'filme_id, categoria_id e nota são obrigatórios e devem ser válidos.'];
        }
        if ($nota < 1 || $nota > 10) {
            return ['success' => false, 'message' => 'A nota deve ser um valor entre 1 e 10.'];
        }

        // 4. Validação de Chaves Estrangeiras (Obrigatório se alterado)
        if (!$this->filmeDao->findById($filme_id)) {
            return ['success' => false, 'message' => 'Filme com ID ' . $filme_id . ' não encontrado.'];
        }
        if (!$this->categoriaDao->findById($categoria_id)) {
            return ['success' => false, 'message' => 'Categoria com ID ' . $categoria_id . ' não encontrada.'];
        }

        // 5. Atualiza o DAO com todos os campos (combinando novos e antigos)
        if ($this->dao->update($id, $filme_id, $categoria_id, $nota)) {
            return ['success' => true, 'message' => 'Avaliação atualizada com sucesso.'];
        }
        return ['success' => false, 'message' => 'Falha ao atualizar avaliação.'];
    }

    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            return ['success' => false, 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            return ['success' => false, 'message' => 'Avaliação não encontrada.'];
        }
        
        if ($this->dao->delete($id)) {
            return ['success' => true, 'message' => 'Avaliação excluída com sucesso.'];
        }
        return ['success' => false, 'message' => 'Falha ao excluir avaliação.'];
    }
}
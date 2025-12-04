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
            return ['status' => 'error', 'http_code' => 400, 'message' => 'filme_id, categoria_id e nota são obrigatórios.'];
        }

        if ($nota < 1 || $nota > 10) { 
            return ['status' => 'error', 'http_code' => 400, 'message' => 'A nota deve ser um valor entre 1 e 10.'];
        }
        
        if (!$this->filmeDao->findById($filme_id)) {
            return ['status' => 'error', 'http_code' => 404, 'message' => 'Filme com ID ' . $filme_id . ' não encontrado.'];
        }

        if (!$this->categoriaDao->findById($categoria_id)) {
            return ['status' => 'error', 'http_code' => 404, 'message' => 'Categoria com ID ' . $categoria_id . ' não encontrada.'];
        }

        return ['status' => 'success', 'data' => ['filme_id' => $filme_id, 'categoria_id' => $categoria_id, 'nota' => $nota]];
    }

    public function getAll(): array {
        return $this->dao->findAll();
    }

    public function create(array $data): array {
        $validation = $this->validateData($data);
        if ($validation['status'] === 'error') {
            return $validation;
        }
        
        $d = $validation['data'];
        if ($this->dao->create($d['filme_id'], $d['categoria_id'], $d['nota'])) {

            return ['http_code' => 201, 'status' => 'success', 'message' => 'Avaliação criada com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar avaliação.'];
    }
    
    public function atualizar(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para atualização.'];
        }

        $avaliacaoExistente = $this->dao->findById($id);
        if (!$avaliacaoExistente) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Avaliação a ser atualizada não encontrada.'];
        }

        $filme_id = (int) ($data['filme_id'] ?? $avaliacaoExistente['filme_id']);
        $categoria_id = (int) ($data['categoria_id'] ?? $avaliacaoExistente['categoria_id']);
        $nota = (int) ($data['nota'] ?? $avaliacaoExistente['nota']);

        $validationData = ['filme_id' => $filme_id, 'categoria_id' => $categoria_id, 'nota' => $nota];
        $validation = $this->validateData($validationData);
        if ($validation['status'] === 'error') {
            return $validation;
        }
        
        if ($this->dao->update($id, $filme_id, $categoria_id, $nota)) {

            return ['http_code' => 200, 'status' => 'success', 'message' => 'Avaliação atualizada com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar avaliação.'];
    }

    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Avaliação não encontrada.'];
        }
        
        if ($this->dao->delete($id)) {

            return ['http_code' => 200, 'status' => 'success', 'message' => 'Avaliação excluída com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir avaliação.'];
    }
}
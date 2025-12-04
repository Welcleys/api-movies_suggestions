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
    
    // Função auxiliar para validação de dados comuns
    private function validateData(array $data): array {
        $filme_id = (int) ($data['filme_id'] ?? 0);
        $categoria_id = (int) ($data['categoria_id'] ?? 0);
        $nota = (int) ($data['nota'] ?? 0);
        
        // Validação de campos obrigatórios
        if ($filme_id <= 0 || $categoria_id <= 0 || $nota <= 0) {
            // Retorno com o status de erro e o código HTTP 400
            return ['status' => 'error', 'http_code' => 400, 'message' => 'filme_id, categoria_id e nota são obrigatórios.'];
        }

        // Validação de intervalo da nota
        if ($nota < 1 || $nota > 10) { 
            return ['status' => 'error', 'http_code' => 400, 'message' => 'A nota deve ser um valor entre 1 e 10.'];
        }
        
        // Validação de chaves estrangeiras (existência)
        if (!$this->filmeDao->findById($filme_id)) {
            return ['status' => 'error', 'http_code' => 404, 'message' => 'Filme com ID ' . $filme_id . ' não encontrado.'];
        }

        if (!$this->categoriaDao->findById($categoria_id)) {
            return ['status' => 'error', 'http_code' => 404, 'message' => 'Categoria com ID ' . $categoria_id . ' não encontrada.'];
        }

        return ['status' => 'success', 'data' => ['filme_id' => $filme_id, 'categoria_id' => $categoria_id, 'nota' => $nota]];
    }

    // Mapeado para GET /avaliacao
    // Retorna os dados, o código HTTP 200 será o padrão no ponto de saída
    public function getAll(): array {
        return $this->dao->findAll();
    }

    // Mapeado para POST /avaliacao
    public function create(array $data): array {
        $validation = $this->validateData($data);
        if ($validation['status'] === 'error') {
            return $validation; // Retorna o erro com http_code 400 ou 404
        }
        
        $d = $validation['data'];
        if ($this->dao->create($d['filme_id'], $d['categoria_id'], $d['nota'])) {
            // Sucesso na criação: 201 Created
            return ['http_code' => 201, 'status' => 'success', 'message' => 'Avaliação criada com sucesso.'];
        }

        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar avaliação.'];
    }
    
    // Mapeado para PUT/PATCH /avaliacao
    public function atualizar(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID ausente: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para atualização.'];
        }

        $avaliacaoExistente = $this->dao->findById($id);
        if (!$avaliacaoExistente) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Avaliação a ser atualizada não encontrada.'];
        }

        // Lógica de mesclagem (PATCH-like)
        $filme_id = (int) ($data['filme_id'] ?? $avaliacaoExistente['filme_id']);
        $categoria_id = (int) ($data['categoria_id'] ?? $avaliacaoExistente['categoria_id']);
        $nota = (int) ($data['nota'] ?? $avaliacaoExistente['nota']);

        // Validação (usando a lógica simplificada da função interna, mas tratando os retornos)
        $validationData = ['filme_id' => $filme_id, 'categoria_id' => $categoria_id, 'nota' => $nota];
        $validation = $this->validateData($validationData);
        if ($validation['status'] === 'error') {
            return $validation; // Retorna o erro com http_code 400 ou 404
        }
        
        if ($this->dao->update($id, $filme_id, $categoria_id, $nota)) {
            // Sucesso na atualização: 200 OK
            return ['http_code' => 200, 'status' => 'success', 'message' => 'Avaliação atualizada com sucesso.'];
        }
        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar avaliação.'];
    }

    // Mapeado para DELETE /avaliacao
    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID ausente: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Avaliação não encontrada.'];
        }
        
        if ($this->dao->delete($id)) {
            // Sucesso na exclusão: 200 OK (Muitas APIs usam 204 No Content, mas 200 é mais simples aqui)
            return ['http_code' => 200, 'status' => 'success', 'message' => 'Avaliação excluída com sucesso.'];
        }
        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir avaliação.'];
    }
}
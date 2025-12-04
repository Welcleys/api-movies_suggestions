<?php
namespace service;

use generic\MysqlFactory;

class FilmeService {
    private $dao;

    public function __construct() {
        $this->dao = MysqlFactory::createFilmeDAO();
    }

    // Mapeado para GET /filme
    public function getAll(): array {
        return $this->dao->findAll();
    }
    
    public function getById(array $data): ?array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID inválido para busca: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID inválido para busca.'];
        }
        
        $resultado = $this->dao->findById($id);

        if (!$resultado) {
            // Filme não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme não encontrado.'];
        }
        
        // Retorna o resultado encontrado (o status 200 será o padrão)
        return $resultado;
    }

    // Mapeado para POST /filme
    public function create(array $data): array {
        $titulo = trim($data['titulo'] ?? '');
        $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);
        $tempo_duracao = trim($data['tempo_duracao'] ?? '');

        if (empty($titulo) || $ano_lancamento <= 0 || empty($tempo_duracao)) { 
            // Validação de campos: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'Título, Ano de Lançamento e Tempo de Duração são obrigatórios e válidos.'];
        }

        if ($this->dao->create($titulo, $ano_lancamento, $tempo_duracao)) { 
            // Sucesso na criação: 201 Created
            return ['http_code' => 201, 'status' => 'success', 'message' => 'Filme criado com sucesso.'];
        }
        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar filme.'];
    }
    
    // Mapeado para PUT/PATCH /filme
    public function atualizar(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        $titulo = trim($data['titulo'] ?? '');
        $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);
        $tempo_duracao = trim($data['tempo_duracao'] ?? '');

        if ($id <= 0 || empty($titulo) || $ano_lancamento <= 0 || empty($tempo_duracao)) { 
            // Validação de campos: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID, Título, Ano de Lançamento e Tempo de Duração são necessários para atualização.'];
        }

        if (!$this->dao->findById($id)) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme a ser atualizado não encontrado.'];
        }

        if ($this->dao->update($id, $titulo, $ano_lancamento, $tempo_duracao)) { 
            // Sucesso na atualização: 200 OK
            return ['http_code' => 200, 'status' => 'success', 'message' => 'Filme atualizado com sucesso.'];
        }
        // Falha interna: 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar filme.'];
    }
    
    // Mapeado para DELETE /filme
    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {
            // ID ausente: 400 Bad Request
            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {
            // Recurso não encontrado: 404 Not Found
            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme não encontrado.'];
        }

        try {
            if ($this->dao->delete($id)) {
                // Sucesso na exclusão: 200 OK
                return ['http_code' => 200, 'status' => 'success', 'message' => 'Filme excluído com sucesso.'];
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {
                // Conflito de Integridade (Chave Estrangeira): 409 Conflict
                return ['http_code' => 409, 'status' => 'error', 'message' => 'Não é possível excluir o filme, pois ele possui avaliações associadas.'];
            }
        }
        // Falha interna (catch geral): 500 Internal Server Error
        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir filme.'];
    }
}
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

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID inválido para busca.'];
        }
        
        $resultado = $this->dao->findById($id);

        if (!$resultado) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme não encontrado.'];
        }
        
        return $resultado;
    }

    public function create(array $data): array {
        $titulo = trim($data['titulo'] ?? '');
        $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);
        $tempo_duracao = trim($data['tempo_duracao'] ?? '');

        if (empty($titulo) || $ano_lancamento <= 0 || empty($tempo_duracao)) { 

            return ['http_code' => 400, 'status' => 'error', 'message' => 'Título, Ano de Lançamento e Tempo de Duração são obrigatórios e válidos.'];
        }

        if ($this->dao->create($titulo, $ano_lancamento, $tempo_duracao)) { 
   
            return ['http_code' => 201, 'status' => 'success', 'message' => 'Filme criado com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Erro interno ao criar filme.'];
    }
    
    public function atualizar(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        $titulo = trim($data['titulo'] ?? '');
        $ano_lancamento = (int) ($data['ano_lancamento'] ?? 0);
        $tempo_duracao = trim($data['tempo_duracao'] ?? '');

        if ($id <= 0 || empty($titulo) || $ano_lancamento <= 0 || empty($tempo_duracao)) { 

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID, Título, Ano de Lançamento e Tempo de Duração são necessários para atualização.'];
        }

        if (!$this->dao->findById($id)) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme a ser atualizado não encontrado.'];
        }

        if ($this->dao->update($id, $titulo, $ano_lancamento, $tempo_duracao)) { 

            return ['http_code' => 200, 'status' => 'success', 'message' => 'Filme atualizado com sucesso.'];
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao atualizar filme.'];
    }
    
    public function excluir(array $data): array {
        $id = (int) ($data['id'] ?? 0);
        
        if ($id <= 0) {

            return ['http_code' => 400, 'status' => 'error', 'message' => 'ID é obrigatório para exclusão.'];
        }

        if (!$this->dao->findById($id)) {

            return ['http_code' => 404, 'status' => 'error', 'message' => 'Filme não encontrado.'];
        }

        try {
            if ($this->dao->delete($id)) {

                return ['http_code' => 200, 'status' => 'success', 'message' => 'Filme excluído com sucesso.'];
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {

                return ['http_code' => 409, 'status' => 'error', 'message' => 'Não é possível excluir o filme, pois ele possui avaliações associadas.'];
            }
        }

        return ['http_code' => 500, 'status' => 'error', 'message' => 'Falha ao excluir filme.'];
    }
}
<?php
// tcc/app/Controllers/CategoriaController.php

// A namespace deve ser 'controller' para corresponder ao Endpoint::classe
namespace controller;

use service\CategoriaService; // Usa a Service Layer

class CategoriaController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaService();
    }

    // LISTAR (Mapeado via Rotas.php para Acao::GET)
    // O Rotas.php original não tem suporte a ID na URI, apenas no GET/POST
    public function listar() {
        // Chamado pela rota GET
        return $this->service->getAll();
    }

    // NOVO MÉTODO: Mapeado via Rotas.php para Acao::GET quando ID está presente
    // Você deve atualizar Rotas.php para mapear GETs com ID para este método.
    public function buscarPorId(array $data) {
        // O ID virá no array $data['id'] graças ao Acao.php
        $id = (int) ($data['id'] ?? 0); 
        
        if ($id > 0) {
            return $this->service->getById(['id' => $id]); 
        }

        // Se não houver ID, você pode chamar o listar() ou retornar um erro
        return ['success' => false, 'message' => 'ID de busca não fornecido.'];
    }

    // INSERIR (Mapeado via Rotas.php para Acao::POST)
    // O Acao.php envia os parâmetros diretamente para o método
    public function inserir(array $data) {
        // Chamado pela rota POST
        return $this->service->create($data);
    }
    
    // ATUALIZAR (Deve ser mapeado para Acao::PUT ou PATCH)
    // O Acao.php garante que os parâmetros de entrada estão disponíveis
    public function atualizar(array $data) {
        // Chamado pela rota PUT
        return $this->service->update($data);
    }
    
    // EXCLUIR (Deve ser mapeado para Acao::DELETE)
    public function excluir(array $data) {
        // Chamado pela rota DELETE
        return $this->service->delete($data);
    }

}
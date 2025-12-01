<?php
namespace controller;

use service\AvaliacaoService;

class AvaliacaoController {
    private $service;

    public function __construct() {
        $this->service = new AvaliacaoService();
    }

    // Mapeado para GET /avaliacao
    public function listar() {
        return $this->service->getAll();
    }
    
    // Mapeado para POST /avaliacao
    public function inserir(array $data) {
        return $this->service->create($data);
    }
    
    // Mapeado para PUT/PATCH /avaliacao
    public function atualizar(array $data) {
        return $this->service->atualizar($data);
    }
    
    // Mapeado para DELETE /avaliacao
    public function excluir(array $data) {
        return $this->service->excluir($data);
    }
}
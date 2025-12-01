<?php
namespace controller;

use service\FilmeService;

class FilmeController {
    private $service;

    public function __construct() {
        $this->service = new FilmeService();
    }

    // Mapeado para GET /filme
    public function listar() {
        return $this->service->getAll();
    }
    
    // Mapeado para POST /filme
    public function inserir(array $data) {
        return $this->service->create($data);
    }
    
    // Mapeado para PUT/PATCH /filme
    public function atualizar(array $data) {
        return $this->service->atualizar($data);
    }
    
    // Mapeado para DELETE /filme
    public function excluir(array $data) {
        return $this->service->excluir($data);
    }
}
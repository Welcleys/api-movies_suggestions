<?php
namespace controller;

use service\AvaliacaoService;

class AvaliacaoController {
    private $service;

    public function __construct() {
        $this->service = new AvaliacaoService();
    }

    public function listar() {
        return $this->service->getAll();
    }
    
    public function inserir(array $data) {
        return $this->service->create($data);
    }
    
    public function atualizar(array $data) {
        return $this->service->atualizar($data);
    }
    
    public function excluir(array $data) {
        return $this->service->excluir($data);
    }
}
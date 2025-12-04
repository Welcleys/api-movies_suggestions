<?php
namespace controller;

use service\CategoriaService;

class CategoriaController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaService();
    }

    public function listar() {
        return $this->service->getAll();
    }

    public function buscarPorId(array $data) {
        $id = (int) ($data['id'] ?? 0); 
        
        if ($id > 0) {
            return $this->service->getById(['id' => $id]); 
        }
        return ['success' => false, 'message' => 'ID de busca não fornecido.'];
    }

    public function inserir(array $data) {
        return $this->service->create($data);
    }
    
    public function atualizar(array $data) {
        return $this->service->update($data);
    }
    
    public function excluir(array $data) {
        return $this->service->delete($data);
    }

}
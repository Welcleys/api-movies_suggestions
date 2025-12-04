<?php
namespace generic;

class Rotas{
    private $endpoints = [];

    public function __construct(){
        $this->endpoints = [
            "categoria" => new Acao([
                Acao::POST => new Endpoint("Categoria", "inserir"),
                Acao::GET => new Endpoint("Categoria", "listar"),
                Acao::PUT => new Endpoint("Categoria", "atualizar"), 
                Acao::DELETE => new Endpoint("Categoria", "excluir")
            ]),
            
            "filme" => new Acao([
                Acao::POST => new Endpoint("Filme", "inserir"),
                Acao::GET => new Endpoint("Filme", "listar"),
                Acao::PUT => new Endpoint("Filme", "atualizar"), 
                Acao::DELETE => new Endpoint("Filme", "excluir")
            ]),

            "avaliacao" => new Acao([
                Acao::POST => new Endpoint("Avaliacao", "inserir"),
                Acao::GET => new Endpoint("Avaliacao", "listar"),
                Acao::PUT => new Endpoint("Avaliacao", "atualizar"), 
                Acao::DELETE => new Endpoint("Avaliacao", "excluir")
            ])
        ];
    }

    public function executar($rota, $id = null){
        if (isset($this->endpoints[$rota])) {
            $endpoint = $this->endpoints[$rota];
            
            $dados = $endpoint->executar($id); // Dados são o array retornado pelo Service
            
            // --- CÓDIGO CORRIGIDO ---
            // ANTES: $retorno = new Retorno();
            // ANTES: $retorno ->dados = $dados;
            // ANTES: return $retorno;

            // NOVO: Retorna o array de dados do Service DIRETAMENTE.
            return $dados;
            // --------------------------
        }
        return null;
    }
}
<?php
/*namespace generic;

class Rotas{
    private $endpoints = [];

    public function __construct(){
        // rotas para o acesso as chamadas
        $this->endpoints = [
            "cliente" => new Acao([
                Acao::POST => new Endpoint("Cliente", "inserir"),
                Acao::GET => new Endpoint("Cliente", "listar")
            ]),
            "alunos" =>new Acao([
                Acao::GET => new Endpoint("Aluno", "teste")
            ])
        ];
    }
    public function executar($rota){
        // verifica o array associativo se a rota existe
        if (isset($this->endpoints[$rota])) {

            $endpoint = $this->endpoints[$rota];
            $dados =$endpoint->executar();
            $retorno = new Retorno();
            $retorno ->dados = $dados;
            return $retorno;
        }
        return null;
    }
}*/

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
    // ... (o método executar permanece o mesmo)
   public function executar($rota, $id = null){
        // verifica o array associativo se a rota existe
        if (isset($this->endpoints[$rota])) {
            $endpoint = $this->endpoints[$rota];
            
            // 1. Executa a Ação, passando o ID
            $dados = $endpoint->executar($id); 
            
            $retorno = new Retorno();
            $retorno ->dados = $dados;
            return $retorno;
        }
        return null;
    }
}



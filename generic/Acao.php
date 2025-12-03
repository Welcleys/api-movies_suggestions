<?php
namespace generic;

use ReflectionMethod;
class Acao{

    const POST = "POST";
    const GET = "GET";
    const PUT = "PUT";
    const PATCH = "PATCH";
    const DELETE = "DELETE";

    private $endpoint;

    public function __construct($endpoint = [])

    {
        $this->endpoint = $endpoint;
    }

    public function executar($idRota = null){
        $end = $this->endpointMetodo();

        if ($end) {
            $reflectMetodo = new ReflectionMethod($end->classe,$end->execucao);
            $parametros = $reflectMetodo->getParameters();
            $returnParam = $this->getParam(); // Coleta POST/GET/Input

            // Se houver um ID na URI, adicione-o aos parâmetros de entrada
            if ($idRota !== null) {
                $returnParam['id'] = $idRota; 
            }

            if($parametros){
                $para=[];
                // Itera sobre os parâmetros esperados pelo método do Controller
                foreach($parametros as $v){
                    $name = $v->getName();

                    // Verifica se o parâmetro (incluindo o 'id') está disponível
                    if(!isset($returnParam[$name]) ){
                        // Se o parâmetro é necessário, mas não foi fornecido
                        // (exceto se for opcional no método do Controller, o que não estamos tratando aqui)
                        // Você pode querer retornar um erro 400 mais descritivo
                        return ['success' => false, 'message' => "Parâmetro '$name' obrigatório faltando."];
                    }
                    $para[$name] = $returnParam[$name];
                }
                // Chama o Controller, passando o array de parâmetros
                return $reflectMetodo->invokeArgs(new $end->classe(),$para);
            }
            
            // Se o método do Controller não espera parâmetros, chama sem argumentos
            return $reflectMetodo->invoke(new $end->classe());
        }
        return null;
    }

    private function endpointMetodo(){

        return isset($this->endpoint[$_SERVER["REQUEST_METHOD"]]) ? $this->endpoint[$_SERVER["REQUEST_METHOD"]] : null;
    }
        private function getPost(){
        if($_POST){
        return $_POST;
        }
        return [];
    }

    private function getGet(){
        if($_GET){
            $get = $_GET;
            unset($get["param"]);
            return $get;
        }
        return [];

    }
    
    private function getInput(){
        $input = file_get_contents("php://input");
        $data = []; // Inicializa como array vazio

        if($input){
            $data = json_decode($input, true);
            
            // CRÍTICO: Se json_decode falhar, ele retorna NULL. 
            // Garante que o retorno seja um array vazio se $data for NULL
            if (!is_array($data)) { 
                $data = [];
            }
        }
        
        // Retorna o array decodificado ou um array vazio
        return $data;
    }
    
    public function getParam(){
        // Usa array_merge com verificação para garantir que todos os argumentos são arrays
        $post = $this->getPost() ?: [];
        $get = $this->getGet() ?: [];
        $input = $this->getInput() ?: [];
        
        return array_merge($post, $get, $input);
    }
}
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
            $returnParam = $this->getParam();

            if ($idRota !== null) {
                $returnParam['id'] = $idRota; 
            }

            if($parametros){
                $para=[];
                foreach($parametros as $v){
                    $name = $v->getName();

                    if(!isset($returnParam[$name]) ){
                        
                        return ['success' => false, 'message' => "Parâmetro '$name' obrigatório faltando."];
                    }
                    $para[$name] = $returnParam[$name];
                }

                return $reflectMetodo->invokeArgs(new $end->classe(),$para);
            }
            
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
        $data = [];

        if($input){
            $data = json_decode($input, true);

            if (!is_array($data)) { 
                $data = [];
            }
        }

        return $data;
    }
    
    public function getParam(){

        $post = $this->getPost() ?: [];
        $get = $this->getGet() ?: [];
        $input = $this->getInput() ?: [];
        
        return array_merge($post, $get, $input);
    }
}
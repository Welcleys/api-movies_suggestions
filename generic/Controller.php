<?php
namespace generic;

class Controller
{
    private $rotas = null;
    
    public function __construct()
    {
        $this->rotas = new Rotas();
    }

    public function verificarChamadas($rotaCompleta)
    {
        $partes = explode('/', trim($rotaCompleta, '/'));
        
        $rotaNome = $partes[0]; 
        
        $id = $partes[1] ?? null; 
        
        $retorno = $this->rotas->executar($rotaNome, $id);
        
        if ($retorno) {
            
            $httpStatus = 200;
            
            if (isset($retorno->dados['http_code'])) {
                $httpStatus = $retorno->dados['http_code'];
                unset($retorno->dados['http_code']); 
            } else {
                if (isset($retorno->dados['status']) && $retorno->dados['status'] === 'error') {
                    $httpStatus = 400;
                }
            }
            
            http_response_code($httpStatus);
            
            header("Content-Type: application/json");
            $json = json_encode($retorno);
            echo $json;
            
        } else {
            http_response_code(404);
            echo json_encode(['erro' => true, 'dados' => ['status' => 'error', 'message' => 'Rota não encontrada.']]);
        }
    }
}
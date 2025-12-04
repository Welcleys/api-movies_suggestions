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
            
            // --- INÍCIO DA CORREÇÃO DO PONTO DE SAÍDA ---
            $httpStatus = 200; // Define 200 OK como o padrão (para GETs, por exemplo)
            
            // Verifica se o resultado do Service tem um código HTTP explícito
            if (isset($retorno->dados['http_code'])) {
                $httpStatus = $retorno->dados['http_code'];
                // Remove o código do array para que ele não vá para o JSON final
                unset($retorno->dados['http_code']); 
            } else {
                // Tratamento de Erros Antigo/Padrão
                // Se o retorno do Service ainda estiver no formato antigo de erro (sem http_code),
                // mas tiver 'status' => 'error' ou 'success' => false, podemos forçar um status de erro (400)
                if (isset($retorno->dados['status']) && $retorno->dados['status'] === 'error') {
                    $httpStatus = 400; // Ou um código mais específico, dependendo do erro
                }
            }
            
            // Define o Status Code HTTP real
            http_response_code($httpStatus);
            // --- FIM DA CORREÇÃO ---
            
            header("Content-Type: application/json");
            $json = json_encode($retorno);
            echo $json;
            
        } else {
            http_response_code(404);
            // Mantenha esta estrutura para erros de rota
            echo json_encode(['erro' => true, 'dados' => ['status' => 'error', 'message' => 'Rota não encontrada.']]);
        }
    }
}
<?php
namespace generic;

class Controller
{
    private $rotas = null;
    
    // Construtor foi corrigido: use __construct
    public function __construct()
    {
        $this->rotas = new Rotas();
    }

    public function verificarChamadas($rotaCompleta)
    {
        // 1. Divide a rota (ex: 'categoria/1' -> ['categoria', '1'])
        $partes = explode('/', trim($rotaCompleta, '/'));
        
        // O nome do recurso é sempre a primeira parte ('categoria')
        $rotaNome = $partes[0]; 
        
        // O ID (se existir) é a segunda parte ('1')
        $id = $partes[1] ?? null; 
        
        // 2. Executa o roteador com o NOME do recurso
        $retorno = $this->rotas->executar($rotaNome, $id);
        
        // 3. Devolve a resposta em formato JSON (igual ao seu código anterior)
        if ($retorno) {
            header("Content-Type: application/json");
            $json = json_encode($retorno);
            echo $json;
        } else {
            http_response_code(404);
            echo json_encode(['erro' => true, 'dados' => 'Rota não encontrada.']);
        }
    }
}
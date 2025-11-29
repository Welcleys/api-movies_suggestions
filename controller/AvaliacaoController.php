<?php
namespace controller;

use service\ClienteService;
use template\ClienteTemp;
use template\ITemplate;

class Cliente
{
    public function __construct(){

    }


    public function listar(){
        $service = new ClienteService();
        $resultado = $service->listarCliente();
        return $resultado;
    }


    public function inserir($nome, $endereco){
        $service = new ClienteService();
        $resultado = $service->inserir($nome, $endereco);
        return $resultado;
    }

    public function teste($nome, $telefone){
        return "$nome, $telefone";
    }

    public function teste2() {
    }
}

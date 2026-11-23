<?php

namespace Controller;
use Core\controller;
use Models\metodologia;
use Throwable;

class controllerMetodologia extends controller
{
    public function index()
    {
        $this->mostrarTela("/metodologia");
    }

    public function listar(){
        header('Content-Type: application/json; charset=utf-8');

        try{
            $dados = metodologia::listar();
            http_response_code(200);
            echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        } catch(Throwable $erro) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro na Listagem']);
        }
    }
}
?>
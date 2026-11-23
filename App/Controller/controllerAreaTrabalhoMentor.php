<?php

namespace Controller;

use Core\controller;

class controllerAreaTrabalhoMentor extends controller
{
    public function index()
    {
        $this->mostrarTela("/areaTrabalhoMentor");
    }
    public function tabela()
    {   
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([[
            "nomestartup" => "Luan SuperMercados",
            "areaatuacao" => "Mercados",
            "email" => "enderecoemail",
            "linkedin" => "enderecolinkedin",
            "instagram" => "enderecoinstagram"
        ]]);
        exit;
    }
}

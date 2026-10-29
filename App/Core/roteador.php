<?php

namespace Core;

class roteador
{
    private $rotas = [];

    public function adicionar($metodo, $uri, $controller)
    {

        $this->rotas[] = [
            'metodo' => $metodo,
            'uri' => $uri,
            'controller' => $controller
        ];
    }
    public function envio() :bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace(BASE_URL, '/', $uri);
        $metodo = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->rotas as $rota) {
            if ($rota['metodo'] === $metodo && $rota['uri'] === $uri) {
                [$classeController, $metodoController] = explode('@', $rota['controller']);
                $classeController = 'Controller\\' . $classeController;
                if(!class_exists($classeController)){
                    http_response_code(500);
                    exit('Classe não encontrada');
                }
                $controller = new $classeController;
                if(!method_exists($controller, $metodoController)){
                    http_response_code(500);
                    exit("Função não encontrada");
                }
                $controller->$metodoController();
                return(true);
            } 
        }
        http_response_code(404);
        exit("404 Not Found");
    }
}

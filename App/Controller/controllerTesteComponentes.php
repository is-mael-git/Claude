<?php

namespace Controller;

use Core\controller;

class controllerTesteComponentes extends controller{
    public function botao(){
        $this->mostrarTela("Componentes/botoesPadrao"); 
    }
}
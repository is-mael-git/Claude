<?php

namespace Models;

use Core\model;

class metodologia extends model
{

    public static function listar($orderBy = "id DESC"): array
    {
        $consulta = self::select("vw_avaliacao_categoria");
        return $consulta->orderBy($orderBy)
            ->buscarTodos();
    }
}

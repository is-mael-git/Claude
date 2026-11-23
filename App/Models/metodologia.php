<?php

namespace Models;
use Core\model;

class metodologia extends model
{
    public static function listar(): array{
        $sql = "
        SELECT
            a.id,
            a.nome,
            a.minimo,
            a.maximo,
            COALESCE(
                ( 
                    SELECT GROUP_CONCAT(c.nome ORDER BY c.nome SEPARATOR ', ')
                    FROM avaliacao_categoria ac
                    INNER JOIN categoria c ON c.id = ac.id_categoria
                    WHERE ac.id_avaliacao = a.id
                ),
                ''
            ) AS categorias
        FROM avaliacao a
        ORDER BY a.id DESC
        ";

        return self::selecionarTodos($sql);
    }

    // public static function listar($orderBy = "id DESC"): array{
    //     $consulta = self::select("avaliacao");
    //     return $consulta->orderBy($orderBy)
    //     ->buscarTodos();
}
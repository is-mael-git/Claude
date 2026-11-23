<?php

namespace Models;

use Core\bancoDados;

class areaTrabalhoAdm
{
    public static function listar(): int
    {
        $pdo = bancoDados::conectar();

        $stmt = $pdo->query(
            'SELECT COUNT(*) FROM startup WHERE ativo = 1'
        );

        return (int) $stmt->fetchColumn();
    }
    public static function listarMentores(): int
    {
        $pdo = bancoDados::conectar();

        $stmt = $pdo->query(
            'SELECT COUNT(*) 
         FROM mentor m
         INNER JOIN usuario u ON u.id = m.id_usuario
         WHERE u.ativo = 1'
        );

        return (int) $stmt->fetchColumn();
    }
}

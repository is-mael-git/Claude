<?php

namespace Models;

use Core\bancoDados;
use PDO;

class mentoria
{
    public static function buscarUltimoId(): ?int
    {
        $pdo = bancoDados::conectar();
        $stmt = $pdo->query('SELECT id FROM mentoria ORDER BY id DESC LIMIT 1');
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public static function buscarDados(int $id): ?array
    {
        $pdo = bancoDados::conectar();

        $stmt = $pdo->prepare("
            SELECT
                s.nome AS startup_nome,
                me.id AS mentor_id,
                u.nome AS mentor_nome
            FROM mentoria m
            INNER JOIN startup s ON s.id = m.id_startup
            INNER JOIN mentor me ON me.id = m.id_mentor
            INNER JOIN usuario u ON u.id = me.id_usuario
            WHERE m.id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }


        $stmt = $pdo->prepare("
            SELECT c.nome
            FROM mentor_competencia mc
            INNER JOIN competencia c ON c.id = mc.id_competencia
            WHERE mc.id_mentor = :id_mentor
        ");

        $stmt->execute(['id_mentor' => $dados['mentor_id']]);
        $dados['competencias'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $dados;
    }

    public static function aprovar(int $id): bool
    {
        $pdo = bancoDados::conectar();

        $stmt = $pdo->prepare('SELECT id FROM mentoria WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        if (!$stmt->fetchColumn()) {
            return false;
        }

        $stmt = $pdo->prepare('UPDATE mentoria SET aprovado = 1 WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
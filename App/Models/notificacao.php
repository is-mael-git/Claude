<?php

namespace Models;

use Core\model;
use PDO;

class notificacao extends model
{
    public static function buscarPorUsuario(int $usuarioId): array
    {
        $stmt = parent::pegarBanco()->prepare("
        SELECT
            n.id,
            n.mensagem,
            n.tipo,
            n.status,
            n.enviado_em,
            CAST(n.visualizado AS UNSIGNED) AS visualizado,
            n.id_tipo
        FROM notificacao n
        WHERE n.id_usuario = :usuario_id
        ORDER BY n.enviado_em DESC, n.id DESC
    ");

        $stmt->execute([
            'usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resetarVisualizacao(int $usuarioId): bool
    {
        $stmt = parent::pegarBanco()->prepare("
        UPDATE notificacao
        SET visualizado = 0
        WHERE id_usuario = :usuario_id
    ");

        return $stmt->execute([
            'usuario_id' => $usuarioId
        ]);
    }

    public static function marcarVisualizadas(int $usuarioId, array $ids): bool
    {
        $marcadores = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = parent::pegarBanco()->prepare("
            UPDATE notificacao
            SET visualizado = 1
            WHERE id_usuario = ? AND status = 'entrada'
                AND visualizado = 0 AND id IN ($marcadores)
        ");

        return $stmt->execute(array_merge([$usuarioId], $ids));
    }

    public static function alterarStatus(int $id, int $usuarioId, string $status): bool
    {
        $stmt = parent::pegarBanco()->prepare("
            UPDATE notificacao
            SET status = :status, visualizado = 1
            WHERE id = :id AND id_usuario = :usuario_id
        ");

        $stmt->execute([
            'status' => $status,
            'id' => $id,
            'usuario_id' => $usuarioId
        ]);

        return $stmt->rowCount() === 1;
    }
}

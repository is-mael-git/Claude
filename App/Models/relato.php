<?php

namespace Models;

use Core\bancoDados;
use JsonException;

class relato
{
    public static function salvarComentarios(array $comentarios): bool
    {
        $db = bancoDados::conectar();

        $stmtAtualizar = $db->prepare("
            UPDATE respos_relato
            SET comentario = :comentario
            WHERE id = :id
        ");
        $stmtExiste = $db->prepare("
            SELECT 1
            FROM respos_relato
            WHERE id = :id
            LIMIT 1
        ");

        $db->beginTransaction();

        try {
            foreach ($comentarios as $item) {
                $stmtAtualizar->execute([
                    'comentario' => $item['comentario'],
                    'id' => $item['id']
                ]);

                if ($stmtAtualizar->rowCount() === 0) {
                    $stmtExiste->execute(['id' => $item['id']]);

                    if (!$stmtExiste->fetchColumn()) {
                        $db->rollBack();
                        return false;
                    }
                }
            }

            $db->commit();
            return true;
        } catch (\Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $erro;
        }
    }

    /**
     * @throws JsonException
     */
    public static function buscarPorRespostaId(int $id): array
    {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            SELECT
                rr.id,
                rr.id_reuniao,
                rr.emissor,
                rr.resposta,
                rr.comentario,
                DATE_FORMAT(rr.enviado_em, '%d/%m/%Y') AS enviado_em,
                r.destinatario,
                r.pergunta,
                s.nome AS startup,
                u.nome AS mentor
            FROM respos_relato rr
            INNER JOIN relato r
                ON r.id = rr.id_relato
            LEFT JOIN startup s
                ON s.id = rr.id_startup
            LEFT JOIN mentor m
                ON m.id = rr.id_mentor
            LEFT JOIN usuario u
                ON u.id = m.id_usuario
            WHERE rr.id_reuniao = (
                SELECT id_reuniao
                FROM respos_relato
                WHERE id = :id
                LIMIT 1
            )
            ORDER BY rr.id ASC
        ");

        $stmt->execute(['id' => $id]);
        $respostas = $stmt->fetchAll();

        foreach ($respostas as &$resposta) {
            $resposta['pergunta'] = json_decode(
                $resposta['pergunta'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            $resposta['resposta'] = json_decode(
                $resposta['resposta'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        unset($resposta);

        return $respostas;
    }
}

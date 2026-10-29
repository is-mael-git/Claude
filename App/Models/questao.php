<?php

namespace Models;

use Core\bancoDados;
use Throwable;

class questao
{

    public static function listarEixos(): array
    {
        $db = bancoDados::conectar();

        $stmt = $db->query("
            SELECT
                e.id AS id_eixo,
                e.nome,
                (SELECT GROUP_CONCAT(c.nome ORDER BY c.nome SEPARATOR ', ')
                 FROM competencia c
                 WHERE c.id_eixo = e.id) AS competencias
            FROM eixo e
            ORDER BY e.nome ASC
        ");

        return $stmt->fetchAll(
            \PDO::FETCH_ASSOC
        );
    }

    public static function listarCompetencias(): array
    {
        $db = bancoDados::conectar();

        $stmt = $db->query("
            SELECT
                c.id AS id_competencia,
                c.nome,
                e.nome AS eixo
            FROM competencia c
            INNER JOIN eixo e ON e.id = c.id_eixo
            ORDER BY e.nome, c.nome
        ");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function listar(bool $somenteAtivas = false): array
    {
        $db = bancoDados::conectar();

        $sql = "
        SELECT
            q.id,
            q.pergunta,
            q.id_eixo,
            NULL AS competencia,
            q.ativo,
            e.nome AS eixos

        FROM questao q

        INNER JOIN eixo e
            ON e.id = q.id_eixo

        " . ($somenteAtivas ? "WHERE q.ativo = 1" : "") . "

        ORDER BY q.id DESC
    ";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(
            \PDO::FETCH_ASSOC
        );
    }

    public static function cadastrar(
        string $pergunta,
        int $idEixo
    ): int {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            INSERT INTO questao (
                pergunta,
                id_eixo,
                ativo
            ) VALUES (
                :pergunta,
                :id_eixo,
                1
            )
        ");

        $stmt->execute([
            'pergunta' => $pergunta,
            'id_eixo' => $idEixo
        ]);

        return (int) $db->lastInsertId();
    }



    // public static function cadastrarVarias(
    //     array $questoes
    // ): array {
    //     $db = bancoDados::conectar();

    //     if (empty($questoes)) {
    //         throw new \InvalidArgumentException(
    //             'Nenhuma questão foi enviada.'
    //         );
    //     }

    //     $idsCriados = [];

    //     try {

    //         $db->beginTransaction();


    //         $stmt = $db->prepare("
    //         INSERT INTO questao (
    //             pergunta,
    //             id_competencia,
    //             ativo
    //         ) VALUES (
    //             :pergunta,
    //             :id_competencia,
    //             1
    //         )
    //     ");


    //         foreach ($questoes as $indice => $questao) {

    //             $numeroQuestao = $indice + 1;


    //             if (!is_array($questao)) {

    //                 throw new \InvalidArgumentException(
    //                     "Questão {$numeroQuestao} inválida."
    //                 );
    //             }


    //             $pergunta = trim(
    //                 (string) ($questao['pergunta'] ?? '')
    //             );


    //             if ($pergunta === '') {

    //                 throw new \InvalidArgumentException(
    //                     "A questão {$numeroQuestao} não possui pergunta."
    //                 );
    //             }


    //             if (mb_strlen($pergunta) > 500) {

    //                 throw new \InvalidArgumentException(
    //                     "A questão {$numeroQuestao} ultrapassa 500 caracteres."
    //                 );
    //             }


    //             $idCompetencia = filter_var(
    //                 $questao['id_competencia'] ?? null,
    //                 FILTER_VALIDATE_INT
    //             );


    //             if (
    //                 $idCompetencia === false ||
    //                 $idCompetencia <= 0
    //             ) {

    //                 throw new \InvalidArgumentException(
    //                     "A competência da questão {$numeroQuestao} é inválida."
    //                 );
    //             }


    //             $stmt->execute([
    //                 'pergunta' => $pergunta,
    //                 'id_competencia' => $idCompetencia
    //             ]);


    //             $idsCriados[] =
    //                 (int) $db->lastInsertId();
    //         }


    //         $db->commit();

    //         return $idsCriados;
    //     } catch (Throwable $erro) {

    //         if ($db->inTransaction()) {

    //             $db->rollBack();
    //         }

    //         throw $erro;
    //     }
    // }


    public static function buscarPorId(
        int $id
    ): ?array {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            SELECT
                q.id,
                q.pergunta,
                q.id_eixo,
                q.ativo,
                e.nome AS eixos,
                EXISTS (
                    SELECT 1
                    FROM questionario_questao qq
                    INNER JOIN questionario questionarioVinculado
                        ON questionarioVinculado.id = qq.id_questionario
                    WHERE qq.id_questao = q.id
                        AND questionarioVinculado.ativo = 1
                ) AS vinculada_questionario_ativo
            FROM questao q

            INNER JOIN eixo e
                ON e.id = q.id_eixo

            WHERE q.id = :id

            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id
        ]);


        $questao = $stmt->fetch(
            \PDO::FETCH_ASSOC
        );

        if (!$questao) {
            return null;
        }

        $stmtQuestionarios = $db->prepare("
            SELECT questionario.nome
            FROM questionario_questao qq
            INNER JOIN questionario ON questionario.id = qq.id_questionario
            WHERE qq.id_questao = :id
            ORDER BY questionario.nome ASC
        ");
        $stmtQuestionarios->execute(['id' => $id]);
        $questao['questionarios'] = $stmtQuestionarios->fetchAll(\PDO::FETCH_COLUMN);

        return $questao;
    }


    public static function atualizar(
        int $id,
        string $pergunta,
        int $idEixo
    ): bool {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            UPDATE questao

            SET
                pergunta = :pergunta,
                id_eixo = :id_eixo

            WHERE id = :id
        ");

        return $stmt->execute([
            'pergunta' => $pergunta,
            'id_eixo' => $idEixo,
            'id' => $id
        ]);
    }


    public static function alterarStatus(
        int $id,
        int $ativo
    ): bool {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            UPDATE questao

            SET ativo = :ativo

            WHERE id = :id
        ");

        return $stmt->execute([
            'ativo' => $ativo,
            'id' => $id
        ]);
    }



    public static function inativarERemoverDeQuestionarios(
        int $idQuestao
    ): bool {
        $db = bancoDados::conectar();

        try {
            $db->beginTransaction();

            $stmtRelacoes = $db->prepare("\n                DELETE FROM questionario_questao\n                WHERE id_questao = :id_questao\n            ");

            $stmtRelacoes->execute([
                'id_questao' => $idQuestao
            ]);

            $stmtQuestao = $db->prepare("\n                UPDATE questao\n                SET ativo = 0\n                WHERE id = :id\n            ");

            $stmtQuestao->execute([
                'id' => $idQuestao
            ]);

            $db->commit();

            return true;
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $erro;
        }
    }
}

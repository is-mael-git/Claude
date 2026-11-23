<?php

namespace Models;

use Core\bancoDados;
use Throwable;

class questionario
{
    public static function listar(): array
    {
        $db = bancoDados::conectar();

        $sql = "SELECT
            q.id,
            q.nome,
            q.descricao,
            q.ativo,
            COUNT(qq.id_questao) AS quantidade_questoes
        FROM questionario q

        LEFT JOIN questionario_questao qq
            ON qq.id_questionario = q.id

        GROUP BY
            q.id,
            q.nome,
            q.descricao,
            q.ativo

        ORDER BY q.id DESC";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

      public static function qtdAtivos() 
    {
        $db = bancoDados::conectar();

        $sql = "SELECT
            COUNT(q.ativo) AS quantidade_questionario
        FROM questionario q WHERE  q.ativo = TRUE";

        $stmt = $db->query($sql);

        return $stmt->fetch();
    }


    public static function listarQuestoes(): array
    {
        $db = bancoDados::conectar();

        $sql = "SELECT
            q.id,
            q.pergunta,
            q.id_eixo,
            q.ativo,
            e.nome AS eixos

        FROM questao q

        INNER JOIN eixo e
            ON e.id = q.id_eixo

        WHERE q.ativo = 1

        ORDER BY q.id ASC";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function cadastrar(
        string $nome,
        string $descricao,
        array $questoes
    ): int {
        $db = bancoDados::conectar();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO questionario (
                    nome,
                    descricao
                ) VALUES (
                    :nome,
                    :descricao
                )
            ");

            $stmt->execute([
                'nome' => $nome,
                'descricao' => $descricao
            ]);

            $questionarioId = (int) $db->lastInsertId();

            $stmtRelacao = $db->prepare("
                INSERT INTO questionario_questao (
                    id_questionario,
                    id_questao
                )
                SELECT :id_questionario, q.id
                FROM questao q
                WHERE q.id = :id_questao AND q.ativo = 1
            ");

            foreach ($questoes as $indice => $questaoId) {

                $stmtRelacao->execute([
                    'id_questionario' => $questionarioId,
                    'id_questao' => (int) $questaoId
                ]);

                if ($stmtRelacao->rowCount() !== 1) {
                    throw new \InvalidArgumentException(
                        'Selecione apenas questões ativas.'
                    );
                }
            }

            $db->commit();

            return $questionarioId;

        } catch (Throwable $erro) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $erro;
        }
    }


    public static function buscarPorId(int $id): ?array
    {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            SELECT
                id,
                nome,
                descricao,
                ativo
            FROM questionario
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $questionario = $stmt->fetch(
            \PDO::FETCH_ASSOC
        );

        if (!$questionario) {
            return null;
        }


        $stmtQuestoes = $db->prepare("
            SELECT
                id_questao
            FROM questionario_questao
            WHERE id_questionario = :id
        ");

        $stmtQuestoes->execute([
            'id' => $id
        ]);

        $questionario['questoes'] =
            $stmtQuestoes->fetchAll(
                \PDO::FETCH_COLUMN
            );

        return $questionario;
    }


    public static function buscarParaResponder(int $id): ?array
    {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            SELECT
                id,
                nome,
                descricao,
                ativo
            FROM questionario
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $questionario = $stmt->fetch(
            \PDO::FETCH_ASSOC
        );

        if (!$questionario) {
            return null;
        }

        $stmtQuestoes = $db->prepare("
            SELECT
                q.id,
                q.pergunta,
                q.id_eixo,
                e.nome AS eixos
            FROM questionario_questao qq

            INNER JOIN questao q
                ON q.id = qq.id_questao

            INNER JOIN eixo e
                ON e.id = q.id_eixo

            WHERE qq.id_questionario = :id
                AND q.ativo = 1

            GROUP BY
                q.id,
                q.pergunta,
                q.id_eixo,
                e.nome
        ");

        $stmtQuestoes->execute([
            'id' => $id
        ]);

        $questionario['questoes'] =
            $stmtQuestoes->fetchAll(
                \PDO::FETCH_ASSOC
            );

        return $questionario;
    }


    public static function atualizar(
        int $id,
        string $nome,
        string $descricao,
        array $questoes
    ): bool {
        $db = bancoDados::conectar();

        try {

            $db->beginTransaction();


            $stmt = $db->prepare("
                UPDATE questionario
                SET
                    nome = :nome,
                    descricao = :descricao
                WHERE id = :id
            ");

            $stmt->execute([
                'nome' => $nome,
                'descricao' => $descricao,
                'id' => $id
            ]);

            $stmtValidarQuestao = $db->prepare("
                SELECT q.id
                FROM questao q
                WHERE q.id = :id_questao
                    AND (q.ativo = 1 OR EXISTS (
                        SELECT 1
                        FROM questionario_questao qq
                        WHERE qq.id_questionario = :id_questionario
                            AND qq.id_questao = q.id
                    ))
            ");

            foreach ($questoes as $questaoId) {
                $stmtValidarQuestao->execute([
                    'id_questao' => (int) $questaoId,
                    'id_questionario' => $id
                ]);

                if (!$stmtValidarQuestao->fetchColumn()) {
                    throw new \InvalidArgumentException(
                        'Selecione apenas questões ativas ou já vinculadas ao questionário.'
                    );
                }
            }


            $stmtExcluir = $db->prepare("
                DELETE FROM questionario_questao
                WHERE id_questionario = :id
            ");

            $stmtExcluir->execute([
                'id' => $id
            ]);


            $stmtRelacao = $db->prepare("
                INSERT INTO questionario_questao (
                    id_questionario,
                    id_questao
                )
                SELECT :id_questionario, q.id
                FROM questao q
                WHERE q.id = :id_questao
            ");


            foreach ($questoes as $indice => $questaoId) {

                $stmtRelacao->execute([
                    'id_questionario' => $id,
                    'id_questao' => (int) $questaoId
                ]);

                if ($stmtRelacao->rowCount() !== 1) {
                    throw new \InvalidArgumentException(
                        'Selecione apenas questões ativas.'
                    );
                }
            }


            $db->commit();

            return true;

        } catch (Throwable $erro) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $erro;
        }
    }


    public static function alterarStatusQuestao(
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


    public static function alterarStatus(
        int $id,
        int $ativo
    ): bool {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("
            UPDATE questionario
            SET ativo = :ativo
            WHERE id = :id
        ");

        $stmt->bindValue(':ativo', $ativo, \PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}

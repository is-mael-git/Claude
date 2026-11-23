<?php

namespace Core;

use InvalidArgumentException;
use PDO;

abstract class model
{
    public static function pegarBanco(): PDO
    {
        return bancoDados::conectar();
    }

    /** Executa um SELECT preparado e retorna todas as linhas. */
    protected static function selecionarTodos(string $sql, array $params = []): array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Executa um SELECT preparado e retorna a primeira linha ou null. */
    protected static function selecionarUm(string $sql, array $params = []): ?array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch();
        return $resultado === false ? null : $resultado;
    }

    /** Executa um SELECT preparado e retorna um único valor escalar (COUNT, id, etc.). */
    protected static function selecionarValor(string $sql, array $params = []): mixed
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /** Executa um SELECT preparado e retorna uma lista simples de uma coluna. */
    protected static function selecionarColuna(string $sql, array $params = []): array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Verifica se existe registro com os pares coluna => valor (ex: cpf, email). */
    protected static function existe(string $tabela, array $condicoes, ?int $ignorarId = null): bool
    {
        $tabelaSegura = self::validarIdentificador($tabela);
        $clausulas = [];
        $params = [];

        foreach ($condicoes as $coluna => $valor) {
            $colSegura = self::validarIdentificador($coluna);
            $param = "cond_{$colSegura}";
            $clausulas[] = "{$colSegura} = :{$param}";
            $params[$param] = $valor;
        }

        if ($ignorarId !== null) {
            $clausulas[] = 'id <> :ignorar_id';
            $params['ignorar_id'] = $ignorarId;
        }

        $sql = "SELECT 1 FROM {$tabelaSegura} WHERE " . implode(' AND ', $clausulas) . ' LIMIT 1';
        return (bool) self::selecionarValor($sql, $params);
    }

    /** Inicia um Query Builder fluente para SELECT dinâmico. */
    protected static function select(string $tabela): select
    {
        return new select($tabela);
    }

    public static function validarIdentificador(string $nome): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $nome)) {
            throw new InvalidArgumentException("Identificador SQL inválido: {$nome}");
        }
        return $nome;
    }
}
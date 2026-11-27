<?php

namespace Core;

use InvalidArgumentException;
use PDO;
use PDOStatement;
use Stringable;

/**
 * Classe base abstrata (Base DAO) com utilitários de leitura, escrita e transação para os Models.
 */
abstract class model
{
    /**
     * Retorna a instância singleton da conexão PDO.
     *
     * @return PDO Conexão ativa com o banco de dados.
     */
    public static function pegarBanco(): PDO
    {
        return bancoDados::conectar();
    }

    /**
     * Executa um SELECT preparado e retorna todas as linhas.
     *
     * @param string $sql Instrução SELECT com placeholders preparados.
     * @param array $params Parâmetros vinculados à consulta.
     * @return array Lista de registros associativos.
     */
    protected static function selecionarTodos(string $sql, array $params = []): array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executa um SELECT preparado e retorna a primeira linha ou null.
     *
     * @param string $sql Instrução SELECT com placeholders preparados.
     * @param array $params Parâmetros vinculados à consulta.
     * @return array|null Registro associativo ou null se nada for encontrado.
     */
    protected static function selecionarUm(string $sql, array $params = []): ?array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch();
        return $resultado === false ? null : $resultado;
    }

    /**
     * Executa um SELECT preparado e retorna um único valor escalar (COUNT, id, etc.).
     *
     * @param string $sql Instrução SELECT com placeholders preparados.
     * @param array $params Parâmetros vinculados à consulta.
     * @return mixed Valor da primeira coluna da primeira linha, ou false se vazio.
     */
    protected static function selecionarValor(string $sql, array $params = []): mixed
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Executa um SELECT preparado e retorna uma lista simples de uma coluna.
     *
     * @param string $sql Instrução SELECT com placeholders preparados.
     * @param array $params Parâmetros vinculados à consulta.
     * @return array Lista indexada com os valores da primeira coluna.
     */
    protected static function selecionarColuna(string $sql, array $params = []): array
    {
        $stmt = self::pegarBanco()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Verifica se existe registro com os pares coluna => valor informados (ex: cpf, email).
     *
     * @param string $tabela Nome da tabela a consultar.
     * @param array $condicoes Pares ['coluna' => valor] combinados com AND.
     * @param int|null $ignorarId ID opcional a ignorar na verificação (útil em edições).
     * @return bool True se existir pelo menos um registro correspondente.
     * @throws InvalidArgumentException Se a tabela ou alguma coluna for inválida.
     */
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

    /**
     * Inicia um Query Builder fluente para SELECT dinâmico.
     *
     * @param string $tabela Nome da tabela ou view base.
     * @return select Instância do construtor de SELECT.
     */
    protected static function select(string $tabela): select
    {
        return new select($tabela);
    }

    /**
     * Inicia um Query Builder fluente para INSERT.
     *
     * @param string $tabela Nome da tabela de destino.
     * @return create Instância do construtor de INSERT.
     */
    protected static function create(string $tabela): create
    {
        return new create($tabela);
    }

    /**
     * Inicia um Query Builder fluente para UPDATE.
     *
     * @param string $tabela Nome da tabela de destino.
     * @return update Instância do construtor de UPDATE.
     */
    protected static function update(string $tabela): update
    {
        return new update($tabela);
    }

    /**
     * Insere um registro na tabela e retorna o ID gerado.
     *
     * @param string $tabela Nome da tabela de destino.
     * @param array $dados Pares ['coluna' => valor] a inserir.
     * @return int Valor retornado por lastInsertId().
     */
    protected static function inserir(string $tabela, array $dados): int
    {
        return self::create($tabela)->dados($dados)->executar();
    }

    /**
     * Insere múltiplos registros na tabela de forma atômica e retorna o total inserido.
     *
     * @param string $tabela Nome da tabela de destino.
     * @param array $linhas Lista de arrays associativos com as mesmas colunas.
     * @return int Quantidade total de registros inseridos.
     */
    protected static function inserirVarios(string $tabela, array $linhas): int
    {
        return self::create($tabela)->inserirVarios($linhas);
    }

    /**
     * Atualiza registros filtrando por ID (ou outra coluna chave).
     *
     * @param string $tabela Nome da tabela de destino.
     * @param int $id Valor do identificador no WHERE.
     * @param array $dados Pares ['coluna' => novoValor] para o SET.
     * @param string $colunaId Nome da coluna chave usada no filtro. Padrão: "id".
     * @return bool True se o UPDATE for executado com sucesso.
     */
    protected static function atualizarPorId(string $tabela, int $id, array $dados, string $colunaId = 'id'): bool
    {
        return self::update($tabela)->dados($dados)->whereId($id, $colunaId)->executar();
    }

    /**
     * Inicia uma transação PDO caso ainda não exista uma ativa.
     *
     * @return void
     */
    protected static function iniciarTransacao(): void
    {
        $db = self::pegarBanco();
        if (!$db->inTransaction()) {
            $db->beginTransaction();
        }
    }

    /**
     * Confirma a transação PDO ativa.
     *
     * @return void
     */
    protected static function confirmarTransacao(): void
    {
        $db = self::pegarBanco();
        if ($db->inTransaction()) {
            $db->commit();
        }
    }

    /**
     * Desfaz a transação PDO ativa em caso de erro.
     *
     * @return void
     */
    protected static function cancelarTransacao(): void
    {
        $db = self::pegarBanco();
        if ($db->inTransaction()) {
            $db->rollBack();
        }
    }

    /**
     * Faz bind tipado respeitando NULL, inteiros e booleanos (incluindo colunas BIT/TINYINT).
     *
     * @param PDOStatement $stmt Statement preparado que receberá o parâmetro.
     * @param string $parametro Nome do placeholder (com ou sem ":" inicial).
     * @param mixed $valor Valor escalar, nulo ou Stringable a ser vinculado.
     * @return void
     * @throws InvalidArgumentException Se o valor não for nulo, escalar nem Stringable.
     */
    public static function vincularValor(PDOStatement $stmt, string $parametro, mixed $valor): void
    {
        $nome = str_starts_with($parametro, ':') ? $parametro : ":{$parametro}";

        if ($valor === null) {
            $stmt->bindValue($nome, null, PDO::PARAM_NULL);
            return;
        }

        if (is_bool($valor)) {
            $stmt->bindValue($nome, $valor ? 1 : 0, PDO::PARAM_INT);
            return;
        }

        if (is_int($valor)) {
            $stmt->bindValue($nome, $valor, PDO::PARAM_INT);
            return;
        }

        if (!is_scalar($valor) && !($valor instanceof Stringable)) {
            throw new InvalidArgumentException("Valor inválido para o parâmetro SQL {$nome}.");
        }

        $stmt->bindValue($nome, (string) $valor, PDO::PARAM_STR);
    }

    /**
     * Valida se um nome de tabela, coluna ou parâmetro segue formato seguro de identificador SQL.
     *
     * @param string $nome Identificador a validar (apenas letras, números e underline).
     * @return string O próprio identificador validado.
     * @throws InvalidArgumentException Se o identificador contiver caracteres inválidos.
     */
    public static function validarIdentificador(string $nome): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $nome)) {
            throw new InvalidArgumentException("Identificador SQL inválido: {$nome}");
        }
        return $nome;
    }
}

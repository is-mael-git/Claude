<?php

namespace Core;

use InvalidArgumentException;
use Throwable;

/**
 * Construtor fluente de instruções INSERT para uso nos Models (DAO).
 */
class create
{
    private string $tabela;
    private array $dados = [];

    /**
     * Inicia o construtor de INSERT validando o nome da tabela.
     *
     * @param string $tabela Nome da tabela de destino.
     * @throws InvalidArgumentException Se o nome da tabela for um identificador SQL inválido.
     */
    public function __construct(string $tabela)
    {
        $this->tabela = model::validarIdentificador($tabela);
    }

    /**
     * Define múltiplos pares ['coluna' => $valor] a serem inseridos.
     *
     * @param array $dados Array associativo no formato ['coluna' => valor].
     * @return self
     * @throws InvalidArgumentException Se alguma chave de coluna for inválida.
     */
    public function dados(array $dados): self
    {
        foreach ($dados as $coluna => $valor) {
            $colSegura = model::validarIdentificador((string) $coluna);
            $this->dados[$colSegura] = $valor;
        }
        return $this;
    }

    /**
     * Define uma única coluna e seu valor para o INSERT.
     *
     * @param string $coluna Nome da coluna na tabela.
     * @param mixed $valor Valor a ser inserido na coluna.
     * @return self
     * @throws InvalidArgumentException Se o nome da coluna for inválido.
     */
    public function valor(string $coluna, mixed $valor): self
    {
        $colSegura = model::validarIdentificador($coluna);
        $this->dados[$colSegura] = $valor;
        return $this;
    }

    /**
     * Monta a instrução SQL INSERT INTO com placeholders nomeados.
     *
     * @return string Instrução INSERT pronta para prepare().
     * @throws InvalidArgumentException Se nenhum dado tiver sido informado.
     */
    public function montarSql(): string
    {
        if (empty($this->dados)) {
            throw new InvalidArgumentException("Nenhum dado informado para INSERT na tabela {$this->tabela}.");
        }

        $colunas = array_keys($this->dados);
        $placeholders = array_map(fn(string $col) => ":{$col}", $colunas);

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tabela,
            implode(', ', $colunas),
            implode(', ', $placeholders)
        );
    }

    /**
     * Retorna os parâmetros acumulados para o INSERT.
     *
     * @return array Mapa associativo ['coluna' => valor].
     */
    public function obterParametros(): array
    {
        return $this->dados;
    }

    /**
     * Executa o INSERT preparado com bind tipado e retorna o ID gerado.
     *
     * @return int Valor retornado por lastInsertId().
     * @throws InvalidArgumentException Se nenhum dado tiver sido informado ou houver valor inválido.
     */
    public function executar(): int
    {
        $db = bancoDados::conectar();
        $stmt = $db->prepare($this->montarSql());

        foreach ($this->dados as $coluna => $valor) {
            model::vincularValor($stmt, $coluna, $valor);
        }

        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    /**
     * Insere múltiplos registros reutilizando o mesmo prepared statement de forma atômica.
     *
     * @param array $linhas Lista de arrays associativos contendo exatamente as mesmas colunas.
     * @return int Quantidade total de registros inseridos.
     * @throws InvalidArgumentException Se alguma linha tiver formato ou colunas incompatíveis.
     * @throws Throwable Em caso de falha na execução (desfaz a transação se aberta por este método).
     */
    public function inserirVarios(array $linhas): int
    {
        if (empty($linhas)) {
            return 0;
        }

        $primeira = reset($linhas);
        if (!is_array($primeira) || empty($primeira)) {
            throw new InvalidArgumentException("Formato inválido para INSERT em lote na tabela {$this->tabela}.");
        }

        $this->dados = [];
        $this->dados($primeira);
        $colunas = array_keys($this->dados);

        foreach ($linhas as $indice => $linha) {
            if (!is_array($linha) || count($linha) !== count($colunas)) {
                throw new InvalidArgumentException("Linha {$indice} incompatível com as colunas esperadas em {$this->tabela}.");
            }
            foreach ($colunas as $col) {
                if (!array_key_exists($col, $linha)) {
                    throw new InvalidArgumentException("Coluna '{$col}' ausente na linha {$indice} para INSERT em {$this->tabela}.");
                }
            }
        }

        $db = bancoDados::conectar();
        $iniciouTransacao = !$db->inTransaction();

        if ($iniciouTransacao) {
            $db->beginTransaction();
        }

        try {
            $stmt = $db->prepare($this->montarSql());
            $total = 0;

            foreach ($linhas as $linha) {
                foreach ($colunas as $col) {
                    model::vincularValor($stmt, $col, $linha[$col]);
                }
                $stmt->execute();
                $total++;
            }

            if ($iniciouTransacao && $db->inTransaction()) {
                $db->commit();
            }

            return $total;
        } catch (Throwable $e) {
            if ($iniciouTransacao && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}

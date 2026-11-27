<?php

namespace Core;

use PDO;

/**
 * Construtor fluente de consultas SELECT para uso nos Models.
 */
class select
{
    private string $tabela;
    private string $colunas = '*';
    private bool $distinto = false;
    private array $joins = [];
    private array $wheres = [];
    private array $params = [];
    private string $orderBy = '';
    private ?int $limit = null;
    private ?int $offset = null;

    /**
     * Inicia a consulta SELECT informando a tabela ou view base.
     *
     * @param string $tabela Nome da tabela ou view (pode incluir alias, ex: "parceiro p").
     */
    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
    }

    /**
     * Define as colunas retornadas pela consulta.
     * Todas colunas por padrão
     * 
     * @param string $colunas Lista de colunas ou expressões SQL (ex: "id, nome"). 
     * @return self
     */
    public function colunas(string $colunas = "*"): self
    {
        $this->colunas = $colunas;
        return $this;
    }

    /**
     * Ativa ou desativa a cláusula DISTINCT na consulta.
     *
     * @param bool $ativo True para aplicar DISTINCT, false para remover.
     * @return self
     */
    public function distinct(bool $ativo = true): self
    {
        $this->distinto = $ativo;
        return $this;
    }

    /**
     * Adiciona uma cláusula JOIN à consulta.
     *
     * @param string $tabela Tabela a ser associada (pode incluir alias).
     * @param string $condicao Condição da cláusula ON (ex: "u.id = e.id_usuario").
     * @param string $tipo Tipo de JOIN (ex: "INNER", "LEFT", "RIGHT"). Padrão: "INNER".
     * @return self
     */
    public function join(string $tabela, string $condicao, string $tipo = 'INNER'): self
    {
        $this->joins[] = "{$tipo} JOIN {$tabela} ON {$condicao}";
        return $this;
    }

    /**
     * Adiciona uma condição à cláusula WHERE combinada com AND.
     *
     * @param string $condicao Expressão SQL da condição com placeholders nomeados.
     * @param array $params Parâmetros nomeados vinculados à condição.
     * @return self
     */
    public function where(string $condicao, array $params = []): self
    {
        $this->wheres[] = $condicao;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Adiciona um filtro WHERE coluna IN (...) gerando placeholders nomeados automaticamente.
     *
     * @param string $coluna Nome da coluna a ser filtrada.
     * @param array $valores Lista de valores permitidos para a cláusula IN.
     * @param string $prefixo Prefixo usado nos placeholders gerados (ex: "area" -> :area_0).
     * @return self
     */
    public function whereIn(string $coluna, array $valores, string $prefixo = 'in'): self
    {
        if (empty($valores)) {
            return $this;
        }

        $placeholders = [];
        foreach (array_values($valores) as $i => $valor) {
            $ph = ":{$prefixo}_{$i}";
            $placeholders[] = $ph;
            $this->params[$ph] = $valor;
        }

        $this->wheres[] = "{$coluna} IN (" . implode(', ', $placeholders) . ')';
        return $this;
    }

    /**
     * Define a ordenação dos resultados (ORDER BY).
     *
     * @param string $ordem Expressão de ordenação SQL (ex: "nome ASC, id DESC").
     * @return self
     */
    public function orderBy(string $ordem): self
    {
        $this->orderBy = $ordem;
        return $this;
    }

    /**
     * Define limite máximo de linhas e deslocamento opcional para paginação.
     *
     * @param int|null $limit Quantidade máxima de registros (ou null para sem limite).
     * @param int|null $offset Quantidade de registros a pular (OFFSET).
     * @return self
     */
    public function limit(?int $limit, ?int $offset = null): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Monta e retorna a instrução SQL final com placeholders preparados.
     *
     * @return string Instrução SELECT pronta para prepare().
     */
    public function montarSql(): string
    {
        $distinctSql = $this->distinto ? 'DISTINCT ' : '';
        $sql = "SELECT {$distinctSql}{$this->colunas} FROM {$this->tabela}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->orderBy !== '') {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . (int) $this->limit;
            if ($this->offset !== null) {
                $sql .= ' OFFSET ' . (int) $this->offset;
            }
        }

        return $sql;
    }

    /**
     * Retorna os parâmetros acumulados para execução da consulta.
     *
     * @return array Mapa de placeholders e valores.
     */
    public function obterParametros(): array
    {
        return $this->params;
    }

    /**
     * Executa a consulta e retorna todas as linhas encontradas.
     *
     * @return array Lista de registros associativos.
     */
    public function buscarTodos(): array
    {
        $stmt = bancoDados::conectar()->prepare($this->montarSql());
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    /**
     * Executa a consulta com LIMIT 1 e retorna o primeiro registro ou null.
     *
     * @return array|null Registro associativo ou null se nada for encontrado.
     */
    public function buscarUm(): ?array
    {
        $clone = clone $this;
        $clone->limit(1);
        $stmt = bancoDados::conectar()->prepare($clone->montarSql());
        $stmt->execute($clone->params);
        $linha = $stmt->fetch();
        return $linha === false ? null : $linha;
    }

    /**
     * Executa a consulta e retorna os valores de uma única coluna como lista simples.
     *
     * @return array Lista indexada com os valores da primeira coluna selecionada.
     */
    public function buscarColuna(): array
    {
        $stmt = bancoDados::conectar()->prepare($this->montarSql());
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Executa um COUNT ignorando ORDER BY, LIMIT e OFFSET da consulta atual.
     *
     * @param string $expressao Expressão passada ao COUNT() (ex: "*", "DISTINCT id").
     * @return int Total de registros encontrados.
     */
    public function contar(string $expressao = '*'): int
    {
        $clone = clone $this;
        $clone->colunas("COUNT({$expressao})")
            ->distinct(false)
            ->orderBy('')
            ->limit(null, null);

        $stmt = bancoDados::conectar()->prepare($clone->montarSql());
        $stmt->execute($clone->params);
        return (int) $stmt->fetchColumn();
    }
}

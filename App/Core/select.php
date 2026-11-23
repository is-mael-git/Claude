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

    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
    }

    public function colunas(string $colunas): self
    {
        $this->colunas = $colunas;
        return $this;
    }

    public function distinct(bool $ativo = true): self
    {
        $this->distinto = $ativo;
        return $this;
    }

    public function join(string $tabela, string $condicao, string $tipo = 'INNER'): self
    {
        $this->joins[] = "{$tipo} JOIN {$tabela} ON {$condicao}";
        return $this;
    }

    public function where(string $condicao, array $params = []): self
    {
        $this->wheres[] = $condicao;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

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

    public function orderBy(string $ordem): self
    {
        $this->orderBy = $ordem;
        return $this;
    }

    public function limit(?int $limit, ?int $offset = null): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

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

    public function obterParametros(): array
    {
        return $this->params;
    }

    public function buscarTodos(): array
    {
        $stmt = bancoDados::conectar()->prepare($this->montarSql());
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    public function buscarUm(): ?array
    {
        $clone = clone $this;
        $clone->limit(1);
        $stmt = bancoDados::conectar()->prepare($clone->montarSql());
        $stmt->execute($clone->params);
        $linha = $stmt->fetch();
        return $linha === false ? null : $linha;
    }

    public function buscarColuna(): array
    {
        $stmt = bancoDados::conectar()->prepare($this->montarSql());
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

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

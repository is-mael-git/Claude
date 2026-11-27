<?php

namespace Core;

use InvalidArgumentException;
use LogicException;
use PDOStatement;

/**
 * Construtor fluente de instruções UPDATE para uso nos Models (DAO).
 */
class update
{
    private string $tabela;
    private array $sets = [];
    private array $wheres = [];
    private array $whereParams = [];

    /**
     * Inicia o construtor de UPDATE validando o nome da tabela.
     *
     * @param string $tabela Nome da tabela de destino.
     * @throws InvalidArgumentException Se o nome da tabela for um identificador SQL inválido.
     */
    public function __construct(string $tabela)
    {
        $this->tabela = model::validarIdentificador($tabela);
    }

    /**
     * Define múltiplos pares ['coluna' => $valor] que serão atualizados no SET.
     *
     * @param array $dados Array associativo no formato ['coluna' => novoValor].
     * @return self
     * @throws InvalidArgumentException Se alguma chave de coluna for inválida.
     */
    public function dados(array $dados): self
    {
        foreach ($dados as $coluna => $valor) {
            $colSegura = model::validarIdentificador((string) $coluna);
            $this->sets[$colSegura] = $valor;
        }
        return $this;
    }

    /**
     * Define uma única coluna e seu novo valor na cláusula SET.
     *
     * @param string $coluna Nome da coluna a ser atualizada.
     * @param mixed $valor Novo valor para a coluna.
     * @return self
     * @throws InvalidArgumentException Se o nome da coluna for inválido.
     */
    public function set(string $coluna, mixed $valor): self
    {
        $colSegura = model::validarIdentificador($coluna);
        $this->sets[$colSegura] = $valor;
        return $this;
    }

    /**
     * Adiciona uma condição ao WHERE (mesma assinatura de Core\select::where).
     *
     * @param string $condicao Expressão SQL da condição com placeholders nomeados.
     * @param array $params Parâmetros nomeados vinculados à condição.
     * @return self
     * @throws InvalidArgumentException Se a condição for vazia ou algum nome de parâmetro for inválido.
     */
    public function where(string $condicao, array $params = []): self
    {
        $condicaoLimpa = trim($condicao);
        if ($condicaoLimpa === '') {
            throw new InvalidArgumentException('A condição WHERE no UPDATE não pode ser vazia.');
        }

        $this->wheres[] = $condicaoLimpa;
        foreach ($params as $chave => $valor) {
            $chaveLimpa = ltrim((string) $chave, ':');
            model::validarIdentificador($chaveLimpa);
            $this->whereParams[$chaveLimpa] = $valor;
        }

        return $this;
    }

    /**
     * Adiciona filtro de igualdade por ID (ou outra coluna chave) no WHERE.
     *
     * @param int $id Valor numérico do identificador a filtrar.
     * @param string $coluna Nome da coluna chave. Padrão: "id".
     * @return self
     * @throws InvalidArgumentException Se o nome da coluna for inválido.
     */
    public function whereId(int $id, string $coluna = 'id'): self
    {
        $colSegura = model::validarIdentificador($coluna);
        $param = "where_{$colSegura}";
        $this->wheres[] = "{$colSegura} = :{$param}";
        $this->whereParams[$param] = $id;
        return $this;
    }

    /**
     * Adiciona filtro WHERE coluna IN (...) gerando placeholders nomeados automaticamente.
     *
     * @param string $coluna Nome da coluna a ser filtrada.
     * @param array $valores Lista não vazia de valores para a cláusula IN.
     * @param string $prefixo Prefixo usado nos placeholders gerados (ex: "ids" -> :where_ids_0).
     * @return self
     * @throws InvalidArgumentException Se a lista de valores estiver vazia ou identificadores forem inválidos.
     */
    public function whereIn(string $coluna, array $valores, string $prefixo = 'in'): self
    {
        if (empty($valores)) {
            throw new InvalidArgumentException('A lista de valores de whereIn no UPDATE não pode ser vazia.');
        }

        $colSegura = model::validarIdentificador($coluna);
        $prefSeguro = model::validarIdentificador($prefixo);
        $placeholders = [];

        foreach (array_values($valores) as $i => $valor) {
            $ph = "where_{$prefSeguro}_{$i}";
            $placeholders[] = ":{$ph}";
            $this->whereParams[$ph] = $valor;
        }

        $this->wheres[] = "{$colSegura} IN (" . implode(', ', $placeholders) . ')';
        return $this;
    }

    /**
     * Monta a instrução SQL UPDATE ... SET ... WHERE ... com trava contra UPDATE sem WHERE.
     *
     * @return string Instrução UPDATE pronta para prepare().
     * @throws InvalidArgumentException Se nenhuma coluna tiver sido definida no SET.
     * @throws LogicException Se nenhuma cláusula WHERE tiver sido informada.
     */
    public function montarSql(): string
    {
        if (empty($this->sets)) {
            throw new InvalidArgumentException("Nenhuma coluna informada para UPDATE na tabela {$this->tabela}.");
        }

        if (empty($this->wheres)) {
            throw new LogicException("UPDATE sem cláusula WHERE bloqueado por segurança na tabela {$this->tabela}.");
        }

        $clausulasSet = array_map(
            fn(string $col) => "{$col} = :set_{$col}",
            array_keys($this->sets)
        );

        return sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->tabela,
            implode(', ', $clausulasSet),
            implode(' AND ', $this->wheres)
        );
    }

    /**
     * Combina os parâmetros de SET (:set_*) e WHERE verificando colisões de nome.
     *
     * @return array Mapa completo de parâmetros sem o prefixo ":".
     * @throws InvalidArgumentException Se um parâmetro do WHERE colidir com um parâmetro do SET.
     */
    public function obterParametros(): array
    {
        $params = [];
        foreach ($this->sets as $coluna => $valor) {
            $params["set_{$coluna}"] = $valor;
        }
        foreach ($this->whereParams as $chave => $valor) {
            if (array_key_exists($chave, $params)) {
                throw new InvalidArgumentException("Conflito de parâmetro PDO ':{$chave}' entre SET e WHERE.");
            }
            $params[$chave] = $valor;
        }
        return $params;
    }

    /**
     * Executa o UPDATE preparado com bind tipado.
     *
     * @return bool True se a execução for bem-sucedida.
     */
    public function executar(): bool
    {
        return $this->prepararStatement()->execute();
    }

    /**
     * Executa o UPDATE preparado e retorna a quantidade de linhas afetadas.
     *
     * @return int Número de registros afetados (rowCount).
     */
    public function executarContando(): int
    {
        $stmt = $this->prepararStatement();
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Prepara a instrução SQL e faz o bind tipado de todos os parâmetros.
     *
     * @return PDOStatement Statement pronto para execute().
     */
    private function prepararStatement(): PDOStatement
    {
        $parametros = $this->obterParametros();
        $stmt = bancoDados::conectar()->prepare($this->montarSql());

        foreach ($parametros as $param => $valor) {
            model::vincularValor($stmt, $param, $valor);
        }

        return $stmt;
    }
}

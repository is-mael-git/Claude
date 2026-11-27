# Camada Base DAO — `Core\model`, `select`, `create` e `update`

Documentação de referência para implementar Models no **ParkTec** utilizando a camada de persistência padronizada em `App/Core/` (estilo Base DAO + Query Builders fluentes).

---

## Arquivos da Camada

| Arquivo | Classe | Responsabilidade |
| :--- | :--- | :--- |
| `App/Core/model.php` | `Core\model` | Classe base abstrata (Base DAO): atalhos de leitura/escrita, controle de transação, bind tipado (`vincularValor`) e validação de identificadores SQL (`validarIdentificador`). |
| `App/Core/select.php` | `Core\select` | Construtor fluente para consultas `SELECT` dinâmicas (`colunas`, `distinct`, `join`, `where`, `whereIn`, `orderBy`, `limit`, `buscarTodos`, `buscarUm`, `buscarColuna`, `contar`). |
| `App/Core/create.php` | `Core\create` | Construtor fluente para `INSERT` simples (`executar` retornando `lastInsertId()`) e em lote atômico (`inserirVarios`). |
| `App/Core/update.php` | `Core\update` | Construtor fluente para `UPDATE` com `where`, `whereId`, `whereIn`, prefixo `:set_` sem colisão e trava obrigatória contra `UPDATE` sem `WHERE`. |

> Referência completa em produção: `App/Models/usuarioParceiros.php`.

---

## 1. Estrutura Base de um Model

Todo Model deve herdar de `Core\model`. Com isso, ele não precisa importar `Core\bancoDados` nem `PDO` para operações comuns de CRUD.

```php
<?php

namespace Models;

use Core\model;
use Core\select;
use Throwable;

class meuModulo extends model
{
    // Usa self::selecionarUm(), self::select(), self::inserir(),
    // self::atualizarPorId(), self::iniciarTransacao(), etc.
}
```

---

## 2. Métodos Disponíveis em `Core\model`

### 2.1. Leitura direta (SQL pronto)

| Método | Retorno | Uso |
| :--- | :--- | :--- |
| `self::selecionarTodos(string $sql, array $params = [])` | `array` | Retorna todas as linhas associativas de um `SELECT`. |
| `self::selecionarUm(string $sql, array $params = [])` | `?array` | Retorna a primeira linha associativa ou `null`. |
| `self::selecionarValor(string $sql, array $params = [])` | `mixed` | Retorna o valor escalar da primeira coluna (ex: `COUNT`, `id`) ou `false` se vazio. |
| `self::selecionarColuna(string $sql, array $params = [])` | `array` | Retorna lista simples com os valores de uma única coluna (`PDO::FETCH_COLUMN`). |
| `self::existe(string $tabela, array $condicoes, ?int $ignorarId = null)` | `bool` | Verifica existência de registro por pares `['coluna' => valor]`, com opção de ignorar um `id` (útil em edições). |

### 2.2. Escrita direta (atalhos de 1 linha)

| Método | Retorno | Uso |
| :--- | :--- | :--- |
| `self::inserir(string $tabela, array $dados)` | `int` | Executa `INSERT` validando tabela/colunas com bind tipado e retorna `lastInsertId()`. |
| `self::inserirVarios(string $tabela, array $linhas)` | `int` | Insere várias linhas com o mesmo schema de forma atômica (abre transação se não houver uma ativa) e retorna o total inserido. |
| `self::atualizarPorId(string $tabela, int $id, array $dados, string $colunaId = 'id')` | `bool` | Atualiza registros filtrando por `$colunaId = $id`. |

### 2.3. Fábricas fluentes

| Método | Retorno | Uso |
| :--- | :--- | :--- |
| `self::select(string $tabela)` | `Core\select` | Inicia consulta `SELECT` dinâmica. |
| `self::create(string $tabela)` | `Core\create` | Inicia instrução `INSERT` fluente. |
| `self::update(string $tabela)` | `Core\update` | Inicia instrução `UPDATE` fluente (para `WHERE` composto, `whereIn` ou `rowCount`). |

### 2.4. Controle de transação (padrão linear)

Para operações que gravam em mais de uma tabela, use os três helpers de transação com `try/catch` (todos verificam `inTransaction()` internamente):

| Método | Comportamento |
| :--- | :--- |
| `self::iniciarTransacao()` | Chama `beginTransaction()` caso ainda não exista transação aberta. |
| `self::confirmarTransacao()` | Chama `commit()` se houver transação ativa. |
| `self::cancelarTransacao()` | Chama `rollBack()` se houver transação ativa. |

### 2.5. Segurança e bind tipado

- **`model::validarIdentificador(string $nome)`**: aceita apenas `/^[a-zA-Z_][a-zA-Z0-9_]*$/` e lança `InvalidArgumentException` caso contrário, bloqueando SQL injection em nomes de tabelas, colunas e placeholders.
- **`model::vincularValor(PDOStatement $stmt, string $parametro, mixed $valor)`**: faz bind pelo tipo real do PHP:
  - `null` -> `PDO::PARAM_NULL`
  - `bool` -> convertido para `1` ou `0` com `PDO::PARAM_INT` (evita erro em colunas `BIT(1)` e `TINYINT` do MySQL, onde `$stmt->execute($array)` enviaria `'0'` como string)
  - `int` -> `PDO::PARAM_INT`
  - `string` / `float` / `Stringable` -> `PDO::PARAM_STR`
  - Qualquer valor não escalar (ex: array) -> lança `InvalidArgumentException`.

---

## 3. Usando `Core\select`

### Métodos principais

- `colunas(string $colunas)` — padrão `'*'`
- `distinct(bool $ativo = true)`
- `join(string $tabela, string $condicao, string $tipo = 'INNER')`
- `where(string $condicao, array $params = [])`
- `whereIn(string $coluna, array $valores, string $prefixo = 'in')`
- `orderBy(string $ordem)`
- `limit(?int $limit, ?int $offset = null)`
- `buscarTodos(): array` | `buscarUm(): ?array` | `buscarColuna(): array` | `contar(string $expressao = '*'): int`

### Exemplo

```php
$itens = self::select('vw_parceiros_detalhes')
    ->whereIn('id_area_atuacao', [1, 2], 'area')
    ->distinct()
    ->orderBy('nome ASC')
    ->limit(10, 0)
    ->buscarTodos();

$total = self::select('vw_parceiros_detalhes')
    ->whereIn('id_area_atuacao', [1, 2], 'area')
    ->contar('DISTINCT id');
```

> `contar()` clona a consulta internamente e remove `ORDER BY`, `LIMIT` e `OFFSET` sem alterar o objeto original.

---

## 4. Usando `Core\create`

### Inserção simples

```php
// Atalho em Core\model (mais comum)
$idArea = self::inserir('area_atuacao', ['nome' => $nome]);

// Forma fluente
$idArea = self::create('area_atuacao')
    ->valor('nome', $nome)
    ->executar();
```

### Inserção em lote (ex: tabelas N:N)

```php
$linhas = array_map(
    fn(int $idSub) => ['id_mentor' => $idMentor, 'id_subcategoria' => $idSub],
    array_unique($idsCompetencia)
);

self::inserirVarios('mentor_subcategoria', $linhas);
```

> Todas as linhas são validadas antes do primeiro `execute()` (mesmas colunas e identificadores válidos) e executadas dentro de transação única.

---

## 5. Usando `Core\update`

### Atualização simples por ID (ou chave estrangeira)

```php
// Troca de status booleano (ativo BIT/TINYINT)
public static function alterarStatus(int $id, bool $ativo): bool
{
    return self::atualizarPorId('parceiro', $id, ['ativo' => $ativo]);
}

// Filtrando por coluna diferente de "id"
self::atualizarPorId('representante_parceiro', $idParceiro, $dadosRep, 'id_parceiro');
```

### Atualização fluente com `WHERE` composto, `whereIn` e `rowCount`

```php
$linhasAfetadas = self::update('notificacao')
    ->set('visualizado', 1)
    ->where("id_usuario = :uid AND status = 'entrada' AND visualizado = 0", ['uid' => $usuarioId])
    ->whereIn('id', $ids)
    ->executarContando();
```

### Garantias de segurança do `Core\update`

1. **Bloqueio de `UPDATE` sem `WHERE`**: tentar montar ou executar sem ao menos um `where()`, `whereId()` ou `whereIn()` lança `LogicException`.
2. **Sem colisão entre `SET` e `WHERE`**: os placeholders do `SET` usam o prefixo `:set_<coluna>` e o `whereIn` usa `:where_<prefixo>_<i>`. Se houver colisão explícita de nome, lança `InvalidArgumentException`.
3. **`whereIn` vazio bloqueado**: diferente do `SELECT`, passar array vazio em `whereIn()` no `UPDATE` lança `InvalidArgumentException` para evitar atualizar linhas indevidas.

---

## 6. Exemplo Completo com Transação (`usuarioParceiros`)

```php
public static function cadastrarParceiro(array $dados): int
{
    self::iniciarTransacao();

    try {
        $idParceiro = self::inserir('parceiro', [
            'foto'            => $dados['foto'],
            'nome'            => $dados['nome'],
            'cnpj'            => $dados['cnpj'],
            'telefone'        => $dados['telefone'],
            'site'            => $dados['site'],
            'id_area_atuacao' => $dados['id_area_atuacao'],
            'instagram'       => $dados['instagram'],
            'linkedin'        => $dados['linkedin'],
            'ativo'           => 1,
        ]);

        self::inserir('representante_parceiro', [
            'id_parceiro' => $idParceiro,
            'nome'        => $dados['nome_representante'],
            'email'       => $dados['email_representante'],
            'telefone'    => $dados['telefone_representante'],
            'cargo'       => $dados['cargo_representante'],
        ]);

        self::confirmarTransacao();
        return $idParceiro;
    } catch (Throwable $e) {
        self::cancelarTransacao();
        throw $e;
    }
}
```

---

## 7. Checklist Rápido para Criar ou Migrar um Model

1. Fazer a classe estender `Core\model` (`use Core\model;`).
2. Remover `use Core\bancoDados;` e `use PDO;` quando não houver SQL manual especial.
3. Usar `self::selecionarUm()` / `self::selecionarTodos()` / `self::selecionarValor()` para `SELECT` fixo e `self::select()` para consultas dinâmicas com filtro/paginação.
4. Usar `self::inserir()` / `self::inserirVarios()` para gravações e `self::atualizarPorId()` / `self::update()` para edições e troca de status.
5. Envolver cadastros/edições em múltiplas tabelas com `self::iniciarTransacao()`, `self::confirmarTransacao()` e `self::cancelarTransacao()` no `catch (Throwable $e)`.

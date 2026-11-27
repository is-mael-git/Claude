# Componente de Tabela — Paginação, Ordenação e Filtros

Documentação de referência para implementar e integrar tabelas dinâmicas no **ParkTec** utilizando o componente reutilizável de tabela, paginação e filtro em lista.

---

## Arquivos do Componente

| Camada | Arquivo | Responsabilidade |
| :--- | :--- | :--- |
| **View (Paginação)** | `App/Views/Componentes/paginacao.php` | Função `paginacao()` — renderiza setas, números e seletor de quantidade por página. |
| **View (Filtro)** | `App/Views/Componentes/modalFiltro.php` | Função `modalFiltro()` — renderiza o dropdown de filtro com busca e checkboxes. |
| **CSS (Tabela)** | `Public/Css/Componentes/tabela.css` | Layout da tabela, cabeçalho (`position: relative`), botões de ordenação, limpar filtros e paginação. |
| **CSS (Filtro)** | `Public/Css/Componentes/modalFiltro.css` | Estilo do card flutuante de filtro, campo de busca arredondado (`24px`) e botões Limpar/Aplicar. Já importado em `Public/Css/style.css`. |
| **JS (Tabela)** | `Public/Js/Componentes/tabela.js` | Busca `/ParkTec/{tela}/tabela`, aplica templates de linha, ordena, filtra, pagina sem reload e sincroniza a URL. |
| **JS (Filtro)** | `Public/Js/Componentes/modalFiltro.js` | Abertura/fechamento, busca instantânea na lista, controle do checkbox "Todas" e integração com `filtrarTabela()`. |
| **Backend (Base)** | `App/Core/controller.php` | Método protegido `ordenacaoTabela()` com whitelist de colunas SQL. |
| **Backend (Consulta)** | `App/Core/model.php` e `App/Core/select.php` | Base DAO (`self::select()`) com `whereIn()`, `distinct()`, `orderBy()`, `limit()` e `contar()`. Veja [`Docs/dao-model.md`](dao-model.md). |

> Referência completa em produção: `App/Views/crudParceiro.php`, `App/Controller/controllerParceiros.php` e `App/Models/usuarioParceiros.php`.

---

## 1. Estrutura HTML na View

### 1.1. Inclusões no topo da View

```php
<?php
require_once BASE_COMPONENTES . "/paginacao.php";
require_once BASE_COMPONENTES . "/modalFiltro.php";
?>
```

### 1.2. Marcação da Tabela e Cabeçalho

As classes `.containerTabela`, `.tabela`, `.tabelaCabecalho`, `.linhaTabelaCabecalho`, `.itemTabelaCabecalho` e `.tabelaCorpo` são obrigatórias para o funcionamento do CSS e do `tabela.js`.

```php
<div class="containerTabela" id="paginar">
    <table class="tabela">
        <thead class="tabelaCabecalho">
            <tr class="linhaTabelaCabecalho">
                <!-- Coluna com ORDENAÇÃO -->
                <th class="itemTabelaCabecalho">
                    Nome
                    <button type="button" class="btnOrdenarTabela" onclick="ordenarTabela('nome', this)" aria-label="Ordenar por nome">
                        <i class="fa-solid fa-chevron-down setaOrdenarTabela"></i>
                    </button>
                </th>

                <!-- Coluna com FILTRO EM LISTA -->
                <th class="itemTabelaCabecalho">
                    Área de Atuação
                    <button type="button" class="btnTriggerFiltro" onclick="toggleModalFiltro('filtroArea', this)" aria-label="Filtrar por Área">
                        <i class="fa-solid fa-filter iconeFiltro"></i>
                    </button>
                    <?php modalFiltro('filtroArea', $areas ?? [], 'Buscar área...', 'Todas', 'area'); ?>
                </th>

                <!-- Coluna simples (sem ordenação/filtro) -->
                <th class="itemTabelaCabecalho">Telefone</th>

                <!-- Última coluna com botão LIMPAR FILTROS no canto direito -->
                <th class="itemTabelaCabecalho">
                    Ações
                    <button type="button" class="btnLimparFiltros" onclick="limparFiltros()" aria-label="Limpar filtros" title="Limpar filtros">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody class="tabelaCorpo">
            <!-- Preenchido automaticamente pelo tabela.js -->
        </tbody>
    </table>
</div>

<?php paginacao(); ?>
```

### 1.3. Scripts no final da View

Carregue `modalFiltro.js` antes de `tabela.js` (se a tela utilizar filtro em lista):

```php
<script src="<?= BASE_JS ?>Componentes/modalFiltro.js"></script>
<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
```

---

## 2. Como usar o `modalFiltro()`

A função `modalFiltro()` aceita parâmetros posicionais ou um array associativo, além de suportar listas em três formatos:
- Array simples de strings: `['Tecnologia', 'Indústria']`
- Array associativo do banco: `[['id' => 1, 'nome' => 'Tecnologia'], ...]`
- Array de objetos: `[(object)['id' => 1, 'nome' => 'Tecnologia'], ...]`

### Assinatura e parâmetros

```php
modalFiltro(
    string|array $configOuId = 'modalFiltro',
    array $itens = [],
    string $placeholder = 'Buscar área...',
    string $labelTodos = 'Todas',
    string $campo = 'area'
): void
```

| Parâmetro | Tipo | Descrição |
| :--- | :--- | :--- |
| `$configOuId` | `string\|array` | ID único do modal no DOM, ou array associativo com todas as chaves abaixo. |
| `$itens` | `array` | Lista de opções vindas do Controller. |
| `$placeholder` | `string` | Texto do campo de busca interna. |
| `$labelTodos` | `string` | Rótulo do checkbox que limpa/engloba todas as opções (ex: `'Todas'`, `'Todos'`). |
| `$campo` | `string` | Nome do parâmetro enviado via `GET` para `/tabela?{campo}=...`. |

### Exemplos de chamada

```php
// Chamada posicional
modalFiltro('filtroArea', $areas, 'Buscar área...', 'Todas', 'area');

// Chamada por array associativo
modalFiltro([
    'id'          => 'filtroSetor',
    'itens'       => $setores,
    'placeholder' => 'Buscar setor...',
    'labelTodos'  => 'Todos',
    'campo'       => 'setor'
]);
```

---

## 3. Configurando o JavaScript (`tabela.js`)

O `tabela.js` identifica a tela pelo segundo segmento da URL (`window.location.pathname.split('/')[2]`) e busca os dados em `/ParkTec/{endpoint}/tabela`.

Para registrar uma nova tela:

1. Crie o template da linha (`<tr>...</tr>`) dentro de `templateCorpo` em `Public/Js/Componentes/tabela.js`.
   - Use o helper `escaparHtml(valor)` (já definido no topo de `tabela.js`) nos campos de texto renderizados no template para evitar XSS.
2. Vincule a chave da URL em `TEMPLATE_POR_TELA`:

```javascript
const TEMPLATE_POR_TELA = {
    'parceiros': templateCorpo.parceiro,
    'questionario': templateCorpo.questionario,
    'questao': templateCorpo.questao,
    'mentores': templateCorpo.mentor,
    'startups': templateCorpo.startup,
    'equipe': templateCorpo.equipe
};
```

### Funções globais disponíveis

| Função | Uso |
| :--- | :--- |
| `ordenarTabela(coluna, botao)` | Alterna `ASC`/`DESC` na coluna, sincroniza a classe `.desc` apenas no botão da coluna ativa e recarrega na página 1. |
| `filtrarTabela(campo, valor)` | Define `state.filtros[campo] = valor` (ou remove a chave com `delete` se `valor` vier vazio), volta para a página 1 e recarrega a tabela. |
| `limparFiltros()` | Reseta `state.filtros` (`orderBy: "id"`, `direction: "DESC"`), limpa e fecha todos os modais de filtro (`fecharTodosModaisFiltro()`), limpa os parâmetros da URL com `sincronizarUrl()` e recarrega na página 1. |
| `recarregarTabela()` | Faz novo `fetch` com os filtros atuais (e `limit`/`offset` quando paginado) e atualiza o `<tbody>` e a paginação. |

### Como funciona a paginação e a URL

- O `tabela.js` suporta **paginação no servidor** (recomendada) e **paginação no cliente** (`QUANTIDADES_POR_PAGINA = [1, 5, 10, 25, 50]`, padrão `10`):
  - **Modo servidor:** quando o backend responde `{ dados: [...], total: N }`, o `tabela.js` envia `limit` e `offset` a cada mudança de página, quantidade ou filtro, usando `total` para calcular `state.totalPage`.
  - **Modo cliente (legado):** quando o backend responde um array simples `[...]`, o corte por página é feito no DOM via `recortarPagina()`.
- A função `sincronizarUrl()` zera a query string (`novaUrl.search = ''`) e remonta apenas os parâmetros ativos (`pagina`, `quantidade`, `orderBy`, `direction` e filtros selecionados) via `window.history.replaceState()`, preservando o estado ao atualizar (F5) sem deixar parâmetros antigos presos na URL.

---

## 4. Configurando o Backend (Controller + Model)

### 4.1. Controller

O método `tabela()` deve validar a ordenação com `$this->ordenacaoTabela()`, capturar os filtros e parâmetros `limit`/`offset` de `$_GET` e retornar JSON no formato `{ dados, total }`:

```php
public function tabela()
{
    $colunasPermitidas = ['id', 'nome', 'area_nome', 'telefone', 'ativo'];
    $orderBy = $this->ordenacaoTabela($colunasPermitidas, 'id', 'ASC');
    $area = $_GET['area'] ?? null;

    $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : null;
    $offset = $limit !== null && isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : null;

    $parceiros = usuarioParceiros::listar($orderBy, $area, $limit, $offset);
    $total = usuarioParceiros::contar($area);

    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode([
        'dados' => $parceiros,
        'total' => $total,
    ]);
    exit;
}
```

> `ordenacaoTabela(array $colunasPermitidas, string $padrao = 'id', string $direcaoPadrao = 'DESC')` (definido em `App/Core/controller.php`) valida `$_GET['orderBy']` contra a whitelist e `$_GET['direction']` contra `['ASC', 'DESC']`, evitando SQL injection.

### 4.2. Model

Regras importantes para manter a paginação e filtragem sincronizadas (usando a camada DAO de `App/Core/model.php` e `App/Core/select.php` — detalhes completos em [`Docs/dao-model.md`](dao-model.md)):
- **Reaproveite a consulta base**: crie um método privado que retorna um objeto `Core\select` já filtrado (`whereIn`) e reutilize-o tanto em `contar()` quanto em `listar()`, garantindo que `total` reflita exatamente os registros filtrados.
- **Suporte múltiplos valores**: o `modalFiltro.js` envia valores separados por vírgula (ex: `?area=1,3`). Quebre com `explode(',', ...)` e passe para `->whereIn($coluna, $valores, 'area')`, que gera os placeholders preparados automaticamente.

```php
use Core\model;
use Core\select;

class usuarioParceiros extends model
{
    private static function montarConsultaArea($area): select
    {
        $consulta = self::select('vw_parceiros_detalhes');

        if (empty($area)) {
            return $consulta;
        }

        $areasArray = is_array($area) ? $area : explode(',', (string) $area);
        $areasArray = array_filter(array_map('trim', $areasArray), fn($v) => $v !== '');

        if (empty($areasArray)) {
            return $consulta;
        }

        $ehNumerico = true;
        foreach ($areasArray as $item) {
            if (!ctype_digit((string) $item)) {
                $ehNumerico = false;
                break;
            }
        }

        $colunaFiltro = $ehNumerico ? 'id_area_atuacao' : 'area_nome';
        $valores = $ehNumerico
            ? array_map('intval', $areasArray)
            : array_values($areasArray);

        return $consulta->whereIn($colunaFiltro, $valores, 'area');
    }

    public static function contar($area = null): int
    {
        return self::montarConsultaArea($area)->contar('DISTINCT id');
    }

    public static function listar(string $orderBy = "id DESC", $area = null, ?int $limit = null, ?int $offset = null): array
    {
        return self::montarConsultaArea($area)
            ->distinct()
            ->orderBy($orderBy)
            ->limit($limit, $offset)
            ->buscarTodos();
    }
}
```

---

## 5. Checklist rápido para nova tela

1. Adicionar a coluna no `<thead>` da view com `btnOrdenarTabela` e/ou `btnTriggerFiltro` + `modalFiltro(...)`.
2. Incluir `btnLimparFiltros` no último `<th>` (`Ações`).
3. Chamar `<?php paginacao(); ?>` abaixo do `.containerTabela`.
4. Importar `modalFiltro.js` e `tabela.js` no fim da view.
5. Registrar o template da linha em `templateCorpo` e `TEMPLATE_POR_TELA` no `tabela.js`.
6. Validar `orderBy` com `ordenacaoTabela()` e filtrar `$_GET` com prepared statements no Controller/Model sem `LIMIT` fixo.

<?php

if (!function_exists('modalFiltro')) {
    /**
     * Renderiza o componente reutilizável de modal/dropdown de filtro em lista com pesquisa e checkboxes.
     *
     * Pode ser chamado passando parâmetros posicionais ou um array associativo de configuração.
     *
     * @example
     * // Exemplo 1 (posicional):
     * modalFiltro('filtroArea', $areas, 'Buscar área...', 'Todas', 'area');
     *
     * // Exemplo 2 (array associativo):
     * modalFiltro([
     *     'id'          => 'filtroSetor',
     *     'itens'       => ['Tecnologia', 'Saúde', 'Educação'],
     *     'placeholder' => 'Buscar setor...',
     *     'labelTodos'  => 'Todos',
     *     'campo'       => 'setor'
     * ]);
     *
     * @param string|array $configOuId ID único do modal (string) ou array associativo com as opções.
     * @param array $itens Lista de itens para listagem (aceita array de strings, arrays associativos ['id' => ..., 'nome' => ...] ou objetos).
     * @param string $placeholder Texto de placeholder do input de busca. Padrão: 'Buscar área...'
     * @param string $labelTodos Rótulo da primeira opção que marca/desmarca todos. Padrão: 'Todas'
     * @param string $campo Nome do parâmetro enviado na requisição AJAX/query string (ex: 'area', 'setor'). Padrão: 'area'
     * @return void
     */
    function modalFiltro(
        string|array $configOuId = 'modalFiltro',
        array $itens = [],
        string $placeholder = 'Buscar área...',
        string $labelTodos = 'Todas',
        string $campo = 'area'
    ): void {
        if (is_array($configOuId)) {
            $id = $configOuId['id'] ?? 'modalFiltro';
            $itens = $configOuId['itens'] ?? $itens;
            $placeholder = $configOuId['placeholder'] ?? $placeholder;
            $labelTodos = $configOuId['labelTodos'] ?? $labelTodos;
            $campo = $configOuId['campo'] ?? $campo;
        } else {
            $id = $configOuId;
        }
        ?>
        <div class="modalFiltroContainer" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" data-campo="<?= htmlspecialchars($campo, ENT_QUOTES, 'UTF-8') ?>">
            <div class="modalFiltroBusca">
                <i class="fa-solid fa-magnifying-glass modalFiltroIconeBusca" aria-hidden="true"></i>
                <input 
                    type="text" 
                    class="modalFiltroInputBusca" 
                    placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>" 
                    oninput="filtrarOpcoesModalFiltro(this)" 
                    autocomplete="off"
                    aria-label="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="modalFiltroLista">
                <label class="modalFiltroItem modalFiltroItemTodos">
                    <input 
                        type="checkbox" 
                        class="modalFiltroCheckbox modalFiltroCheckboxTodos" 
                        value="" 
                        checked 
                        onchange="toggleTodosModalFiltro(this)"
                    >
                    <span class="modalFiltroRotulo"><?= htmlspecialchars($labelTodos, ENT_QUOTES, 'UTF-8') ?></span>
                </label>

                <?php foreach ($itens as $indice => $item): 
                    $valor = is_array($item)
                        ? ($item['id'] ?? $item['valor'] ?? $item['value'] ?? $item['nome'] ?? $indice)
                        : (is_object($item) ? ($item->id ?? $item->valor ?? $item->nome ?? $indice) : $item);

                    $rotulo = is_array($item)
                        ? ($item['nome'] ?? $item['rotulo'] ?? $item['label'] ?? $item['area_nome'] ?? reset($item))
                        : (is_object($item) ? ($item->nome ?? $item->rotulo ?? $item->label ?? $indice) : $item);
                ?>
                    <label class="modalFiltroItem" data-texto="<?= htmlspecialchars(mb_strtolower((string) $rotulo, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>">
                        <input 
                            type="checkbox" 
                            class="modalFiltroCheckbox modalFiltroCheckboxItem" 
                            name="<?= htmlspecialchars($campo, ENT_QUOTES, 'UTF-8') ?>[]" 
                            value="<?= htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8') ?>" 
                            onchange="toggleItemModalFiltro(this)"
                        >
                        <span class="modalFiltroRotulo"><?= htmlspecialchars((string) $rotulo, ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="modalFiltroRodape">
                <button type="button" class="btnCancelar modalFiltroBtnLimpar" onclick="limparSelecaoModalFiltro('<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>')">
                    Limpar
                </button>
                <button type="button" class="btnSalvar modalFiltroBtnAplicar" onclick="aplicarModalFiltro('<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>')">
                    Aplicar
                </button>
            </div>
        </div>
        <?php
    }
}

// Suporte para inclusão direta via include/require com variáveis no escopo
if (isset($itens) || isset($itensFiltro)) {
    modalFiltro(
        $idModal ?? $idFiltro ?? 'modalFiltro',
        $itens ?? $itensFiltro ?? [],
        $placeholder ?? $placeholderFiltro ?? 'Buscar área...',
        $labelTodos ?? $labelTodosFiltro ?? 'Todas',
        $campo ?? $campoFiltro ?? 'area'
    );
}

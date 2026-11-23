/**
 * Componente interativo de modal/dropdown de filtro de coluna.
 */

/**
 * Alterna a exibição (abre/fecha) do modal de filtro e gira a seta do botão acionador.
 * 
 * @param {string} id - ID do elemento do modal (.modalFiltroContainer).
 * @param {HTMLElement|null} botao - Botão acionador (.btnTriggerFiltro) que foi clicado.
 * @return {void}
 */
function toggleModalFiltro(id, botao) {
    const modal = document.getElementById(id);
    if (!modal) return;

    const estaAberto = modal.classList.contains('aberto');

    // Fecha qualquer outro modal de filtro aberto
    fecharTodosModaisFiltro();

    if (!estaAberto) {
        modal.classList.add('aberto');
        botao?.classList.add('aberto');

        // Foca automaticamente no campo de busca
        const inputBusca = modal.querySelector('.modalFiltroInputBusca');
        setTimeout(() => inputBusca?.focus(), 50);
    }
}

/**
 * Fecha todos os modais de filtro abertos na tela e restaura os botões.
 * 
 * @return {void}
 */
function fecharTodosModaisFiltro() {
    document.querySelectorAll('.modalFiltroContainer.aberto').forEach(modal => {
        modal.classList.remove('aberto');
    });
    document.querySelectorAll('.btnTriggerFiltro.aberto').forEach(btn => {
        btn.classList.remove('aberto');
    });
}

/**
 * Filtra as opções visíveis na lista conforme o termo digitado no input de busca.
 * 
 * @param {HTMLInputElement} input - Elemento de input de busca do filtro.
 * @return {void}
 */
function filtrarOpcoesModalFiltro(input) {
    const termo = input.value.trim().toLowerCase();
    const container = input.closest('.modalFiltroContainer');
    if (!container) return;

    const itens = container.querySelectorAll('.modalFiltroItem:not(.modalFiltroItemTodos)');
    itens.forEach(item => {
        const texto = item.getAttribute('data-texto') || item.textContent.toLowerCase();
        if (texto.includes(termo)) {
            item.classList.remove('oculto');
        } else {
            item.classList.add('oculto');
        }
    });
}

/**
 * Gerencia a seleção do checkbox "Todas". Quando marcado, desmarca os itens individuais.
 * 
 * @param {HTMLInputElement} checkboxTodos - O checkbox da opção "Todas".
 * @return {void}
 */
function toggleTodosModalFiltro(checkboxTodos) {
    const container = checkboxTodos.closest('.modalFiltroContainer');
    if (!container) return;

    const checkboxesItens = container.querySelectorAll('.modalFiltroCheckboxItem');
    if (checkboxTodos.checked) {
        checkboxesItens.forEach(chk => {
            chk.checked = false;
        });
    }
}

/**
 * Gerencia a seleção de checkboxes individuais. Desmarca "Todas" se algum estiver marcado,
 * ou remarca "Todas" caso todos fiquem desmarcados.
 * 
 * @param {HTMLInputElement} checkboxItem - Um dos checkboxes individuais.
 * @return {void}
 */
function toggleItemModalFiltro(checkboxItem) {
    const container = checkboxItem.closest('.modalFiltroContainer');
    if (!container) return;

    const checkboxTodos = container.querySelector('.modalFiltroCheckboxTodos');
    const checkboxesItens = Array.from(container.querySelectorAll('.modalFiltroCheckboxItem'));
    const algumMarcado = checkboxesItens.some(chk => chk.checked);

    if (checkboxTodos) {
        checkboxTodos.checked = !algumMarcado;
    }
}

/**
 * Limpa a seleção do modal, marca "Todas", limpa o input de busca e envia filtro vazio à tabela.
 * 
 * @param {string} id - ID do elemento do modal (.modalFiltroContainer).
 * @return {void}
 */
function limparSelecaoModalFiltro(id) {
    const container = document.getElementById(id);
    if (!container) return;

    const checkboxTodos = container.querySelector('.modalFiltroCheckboxTodos');
    const checkboxesItens = container.querySelectorAll('.modalFiltroCheckboxItem');
    const inputBusca = container.querySelector('.modalFiltroInputBusca');

    if (checkboxTodos) checkboxTodos.checked = true;
    checkboxesItens.forEach(chk => {
        chk.checked = false;
    });

    if (inputBusca) {
        inputBusca.value = '';
        filtrarOpcoesModalFiltro(inputBusca);
    }

    const campo = container.dataset.campo || 'area';
    if (typeof window.filtrarTabela === 'function') {
        window.filtrarTabela(campo, '');
    }

    fecharTodosModaisFiltro();
}

/**
 * Coleta os valores selecionados nos checkboxes individuais, envia para filtrarTabela(campo, valor)
 * e fecha o modal.
 * 
 * @param {string} id - ID do elemento do modal (.modalFiltroContainer).
 * @return {void}
 */
function aplicarModalFiltro(id) {
    const container = document.getElementById(id);
    if (!container) return;

    const checkboxTodos = container.querySelector('.modalFiltroCheckboxTodos');
    const checkboxesItens = Array.from(container.querySelectorAll('.modalFiltroCheckboxItem:checked'));
    const campo = container.dataset.campo || 'area';

    let valorFiltro = '';
    if (!checkboxTodos?.checked && checkboxesItens.length > 0) {
        valorFiltro = checkboxesItens.map(chk => chk.value).join(',');
    }

    if (typeof window.filtrarTabela === 'function') {
        window.filtrarTabela(campo, valorFiltro);
    }

    fecharTodosModaisFiltro();
}

/**
 * Reseta o estado visual do modal sem disparar a recarga da tabela
 * (usado pelo botão global de limpar filtros).
 * 
 * @param {string} id - ID do elemento do modal (.modalFiltroContainer).
 * @return {void}
 */
function resetarSelecaoModalFiltro(id) {
    const container = document.getElementById(id);
    if (!container) return;

    const checkboxTodos = container.querySelector('.modalFiltroCheckboxTodos');
    const checkboxesItens = container.querySelectorAll('.modalFiltroCheckboxItem');
    const inputBusca = container.querySelector('.modalFiltroInputBusca');

    if (checkboxTodos) checkboxTodos.checked = true;
    checkboxesItens.forEach(chk => {
        chk.checked = false;
    });

    if (inputBusca) {
        inputBusca.value = '';
        filtrarOpcoesModalFiltro(inputBusca);
    }
}

// Fechar ao clicar fora
document.addEventListener('click', (event) => {
    const modalAberto = document.querySelector('.modalFiltroContainer.aberto');
    if (!modalAberto) return;

    const clicouDentroModal = modalAberto.contains(event.target);
    const clicouNoBotao = event.target.closest('.btnTriggerFiltro');

    if (!clicouDentroModal && !clicouNoBotao) {
        fecharTodosModaisFiltro();
    }
});

// Fechar com tecla ESC
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        fecharTodosModaisFiltro();
    }
});

// Sincroniza estado inicial dos checkboxes com a URL se houver filtro ativo
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    document.querySelectorAll('.modalFiltroContainer').forEach(container => {
        const campo = container.dataset.campo || 'area';
        const valorUrl = params.get(campo);
        if (valorUrl) {
            const valores = valorUrl.split(',').map(v => v.trim());
            const checkboxTodos = container.querySelector('.modalFiltroCheckboxTodos');
            const checkboxesItens = container.querySelectorAll('.modalFiltroCheckboxItem');
            
            let algumMarcado = false;
            checkboxesItens.forEach(chk => {
                const marcado = valores.includes(chk.value);
                chk.checked = marcado;
                if (marcado) algumMarcado = true;
            });

            if (checkboxTodos) {
                checkboxTodos.checked = !algumMarcado;
            }
        }
    });
});

window.toggleModalFiltro = toggleModalFiltro;
window.fecharTodosModaisFiltro = fecharTodosModaisFiltro;
window.filtrarOpcoesModalFiltro = filtrarOpcoesModalFiltro;
window.toggleTodosModalFiltro = toggleTodosModalFiltro;
window.toggleItemModalFiltro = toggleItemModalFiltro;
window.limparSelecaoModalFiltro = limparSelecaoModalFiltro;
window.aplicarModalFiltro = aplicarModalFiltro;
window.resetarSelecaoModalFiltro = resetarSelecaoModalFiltro;

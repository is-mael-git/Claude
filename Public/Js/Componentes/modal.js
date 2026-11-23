/**
 * Posiciona o modal de notificações centralizado logo abaixo do seu botão de acionamento.
 * @param {HTMLDialogElement} modal - Elemento dialog da notificação.
 */
function posicionarModalNotificacao(modal) {
    const botaoNotificacao = document.querySelector(
        '[data-modal="modalNotificacao"]'
    );

    if (!botaoNotificacao) {
        return;
    }

    const posicaoBotao =
        botaoNotificacao.getBoundingClientRect();

    modal.style.top =
        `${posicaoBotao.bottom + 20}px`;

    modal.style.left =
        `${posicaoBotao.left + (posicaoBotao.width / 2) - (modal.offsetWidth / 2)}px`;
}


/**
 * Posiciona o modal de troca de perfil alinhado abaixo do botão de perfil no cabeçalho.
 * @param {HTMLDialogElement} modal - Elemento dialog de perfil.
 */
function posicionarModalPerfil(modal) {
    const botaoTrocaPerfil = document.querySelector(
        '[data-modal="modalPerfil"]'
    );

    if (!botaoTrocaPerfil) {
        return;
    }

    const posicaoBotao =
        botaoTrocaPerfil.getBoundingClientRect();

    modal.style.top =
        `${posicaoBotao.bottom + 10}px`;

    modal.style.left =
        `${posicaoBotao.right - 50}px`;
}


/**
 * Abre um elemento <dialog> pelo seu ID e aplica o posicionamento caso seja um menu flutuante.
 * @param {string} idModal - ID do elemento dialog a ser aberto.
 */
function abrirModal(idModal) {
    const modal = document.getElementById(idModal);

    if (!modal) {
        console.warn(
            `[modal.js] Modal não encontrado: #${idModal}`
        );

        return;
    }

    modal.showModal();

    if (idModal === 'modalNotificacao') {
        posicionarModalNotificacao(modal);
    }

    if (idModal === 'modalPerfil') {
        posicionarModalPerfil(modal);
    }
}


/**
 * Fecha um elemento <dialog> diretamente (sem passar pelo interceptador de confirmação).
 * @param {string|HTMLDialogElement} idModal - ID do dialog ou a própria referência do elemento DOM.
 */
function fecharModal(idModal) {
    const modal = typeof idModal === 'string' ? document.getElementById(idModal) : idModal;

    if (!modal) {
        console.warn(
            `[modal.js] Modal não encontrado: #${idModal}`
        );

        return;
    }

    modal.close();
}


/**
 * Configura o envio assíncrono (AJAX via fetch) para um formulário, evitando o recarregamento da página.
 * Dispara os eventos customizados 'envioSucesso' ou 'envioErro' no formulário após a resposta.
 *
 * @param {string|HTMLFormElement} idFormulario - Seletor CSS ou elemento DOM do formulário.
 * @param {string|null} urlEnvio - URL opcional de destino (se omitida, usa o atributo action do form).
 * @param {string|null} idModal - ID opcional do modal a ser fechado após o sucesso.
 */
function envioFormulario(idFormulario, urlEnvio = null, idModal = null) {
    const form = typeof idFormulario === 'string'
        ? document.querySelector(idFormulario)
        : idFormulario;

    if (!form || form.dataset.ajaxConfigurado === 'true') {
        return;
    }

    form.dataset.ajaxConfigurado = 'true';

    form.addEventListener('submit', async (event) => {
        if (event.defaultPrevented) {
            return;
        }

        event.preventDefault();

        const url = urlEnvio || form.action || form.getAttribute('action') || window.location.href;
        const formData = event.submitter ? new FormData(form, event.submitter) : new FormData(form);

        const botoesSubmit = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        botoesSubmit.forEach((btn) => { btn.disabled = true; });

        try {
            const response = await fetch(url, {
                method: form.method ? form.method.toUpperCase() : 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // O middleware de autenticaÃ§Ã£o redireciona para o login quando a
            // sessÃ£o expira. O fetch segue esse redirect sem trocar a pÃ¡gina;
            // detectamos o destino final e navegamos a janela inteira.
            if (response.redirected) {
                const destino = new URL(response.url, window.location.href);
                if (destino.pathname.replace(/\/+$/, '').endsWith('/login')) {
                    window.location.assign(destino.href);
                    return;
                }
            }

            const respostaTexto = await response.text();

            if (!response.ok) {
                throw new Error(respostaTexto || `Erro HTTP: ${response.status}`);
            }

            let resultado = null;
            try {
                resultado = JSON.parse(respostaTexto);
            } catch {
                resultado = respostaTexto;
            }

            form.reset();

            const modalPai = form.closest('dialog.templateModal');
            if (idModal) {
                fecharModal(idModal);
            } else if (modalPai) {
                fecharModal(modalPai.id);
            }

            if (typeof window.recarregarTabela === 'function') {
                window.recarregarTabela();
            }

            if (typeof window.mostrarModalSalvo === 'function') {
                window.mostrarModalSalvo();
            }

            form.dispatchEvent(new CustomEvent('envioSucesso', {
                bubbles: true,
                detail: { response, resultado }
            }));
        } catch (error) {
            console.error('Erro ao enviar formulário:', error);

            form.dispatchEvent(new CustomEvent('envioErro', {
                bubbles: true,
                detail: { error }
            }));
        } finally {
            botoesSubmit.forEach((btn) => { btn.disabled = false; });
        }
    });
}



document.addEventListener('DOMContentLoaded', () => {
    const btnsair = document.querySelectorAll('.sairMenuLateral');
    const textoFecharModal = 'Tem certeza que deseja fechar?';
    const textoSair = 'Tem certeza que deseja sair do SGR?';
    const confirmacao = document.createElement('dialog');
    confirmacao.id = 'modalConfirmarFechamento';
    confirmacao.className = 'templateModal';
    confirmacao.innerHTML = `
        <div class="modalHeader">
            <button type="button" class="fecharModal" data-modal="modalConfirmarFechamento" aria-label="Fechar confirmação">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="areaModal">
            <p>${textoFecharModal}</p>
            <div class="modalConfirmarAcoes">
                <button type="button" class="btnCancelar">Não</button>
                <button type="button" class="btnSalvar">Sim</button>
            </div>
        </div>`;
    const perguntaConfirmacao = confirmacao.querySelector('p');
    let saindoFormulario = null;
    btnsair.forEach(function (botao) {
        const formulario = botao.form;
        formulario.addEventListener('submit', function (evento) {
            evento.preventDefault();
            saindoFormulario = formulario;
            perguntaConfirmacao.textContent = textoSair;
            confirmacao.showModal();

        });
    });

    document.body.appendChild(confirmacao);

    let modalPendente = null;

    const semConfirmacao = new Set([
        'modalInativar', 'modalSalvo', 'modalSessaoExpirada', 'modalAvisoQuestoesQuestionario',
        'modalNotificacao', 'modalPerfil', 'modalConfirmarFechamento',
        'visualizarParceiro', 'visualizarMentor',
    ]);
    const exigeConfirmacao = modal => modal?.matches('.templateModal')
        && !semConfirmacao.has(modal.id);

    /**
     * Solicita o fechamento de um modal: fecha direto se não exigir confirmação,
     * ou abre o dialog de confirmação guardando a referência em `modalPendente`.
     */
    function solicitarFechamento(modal) {
        if (!modal?.open || modalPendente) return;
        if (!exigeConfirmacao(modal)) {
            modal.close();
            return;
        }
        modalPendente = modal;
        perguntaConfirmacao.textContent = textoFecharModal;
        confirmacao.showModal();
    }

    confirmacao.querySelector('.btnCancelar').addEventListener('click', () => confirmacao.close());
    confirmacao.querySelector('.btnSalvar').addEventListener('click', () => {
        const modal = modalPendente;
        const formSair = saindoFormulario;
        confirmacao.close();
        if (formSair) {
            formSair.submit(); return
        }
        modal?.close();
        modal?.dispatchEvent(new Event('fechamentoConfirmado'));
    });
    confirmacao.addEventListener('close', () => { modalPendente = null; saindoFormulario = null; });

    document.addEventListener('click', e => {
        const botao = e.target.closest('button');
        const modal = botao?.closest('dialog.templateModal');
        if (!modal || !exigeConfirmacao(modal)) return;
        const cancelar = botao.matches('.btnCancelar, [class^="btnCancelar"], [class*=" btnCancelar"]');
        if (!botao.matches('.fecharModal') && !cancelar) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        solicitarFechamento(modal);
    }, true);

    document.addEventListener('click', (e) => {

        const abrir = e.target.closest('.abrirModal');

        if (abrir) {
            abrirModal(abrir.dataset.modal);
            return;
        }


        const fechar = e.target.closest('.fecharModal');

        if (fechar) {
            fecharModal(fechar.dataset.modal);
            return;
        }

    });


    document.querySelectorAll('dialog').forEach(modal => {

        let iniciouForaDoModal = false;

        modal.addEventListener('pointerdown', (e) => {
            const rect = modal.getBoundingClientRect();

            iniciouForaDoModal =
                e.clientX < rect.left ||
                e.clientX > rect.right ||
                e.clientY < rect.top ||
                e.clientY > rect.bottom;
        });

        modal.addEventListener('pointerup', (e) => {
            const rect = modal.getBoundingClientRect();

            const terminouForaDoModal =
                e.clientX < rect.left ||
                e.clientX > rect.right ||
                e.clientY < rect.top ||
                e.clientY > rect.bottom;

            if (iniciouForaDoModal && terminouForaDoModal) {
                solicitarFechamento(modal);
            }

            iniciouForaDoModal = false;
        });

        modal.addEventListener('cancel', e => {
            if (e.target !== modal || !exigeConfirmacao(modal)) return;
            e.preventDefault();
            solicitarFechamento(modal);
        });

    });

    document.querySelectorAll('dialog.templateModal form').forEach(form => {
        envioFormulario(form);
    });

});

if (typeof window !== 'undefined') {
    window.abrirModal = abrirModal;
    window.fecharModal = fecharModal;
    window.posicionarModalNotificacao = posicionarModalNotificacao;
    window.posicionarModalPerfil = posicionarModalPerfil;
    window.envioFormulario = envioFormulario;
}

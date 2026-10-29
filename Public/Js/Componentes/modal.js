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


function fecharModal(idModal) {
    const modal = document.getElementById(idModal);

    if (!modal) {
        console.warn(
            `[modal.js] Modal não encontrado: #${idModal}`
        );

        return;
    }

    modal.close();
}


document.addEventListener('DOMContentLoaded', () => {
    const confirmacao = document.createElement('dialog');
    confirmacao.id = 'modalConfirmarFechamento';
    confirmacao.className = 'templateModal';
    confirmacao.innerHTML = `
        <div class="modalHeader"></div>
        <div class="areaModal">
            <p>Tem certeza que deseja fechar?</p>
            <div class="modalConfirmarAcoes">
                <button type="button" class="btnCancelar">Não</button>
                <button type="button" class="btnSalvar">Sim</button>
            </div>
        </div>`;
    document.body.appendChild(confirmacao);

    let modalPendente = null;
    const semConfirmacao = new Set([
        'modalInativar', 'modalSalvo', 'modalAvisoQuestoesQuestionario',
        'modalNotificacao', 'modalPerfil', 'modalConfirmarFechamento'
    ]);
    const exigeConfirmacao = modal => modal?.matches('.templateModal')
        && !semConfirmacao.has(modal.id);

    function solicitarFechamento(modal) {
        if (!modal?.open || modalPendente) return;
        if (!exigeConfirmacao(modal)) {
            modal.close();
            return;
        }
        modalPendente = modal;
        confirmacao.showModal();
    }

    confirmacao.querySelector('.btnCancelar').addEventListener('click', () => confirmacao.close());
    confirmacao.querySelector('.btnSalvar').addEventListener('click', () => {
        const modal = modalPendente;
        confirmacao.close();
        modal?.close();
        modal?.dispatchEvent(new Event('fechamentoConfirmado'));
    });
    confirmacao.addEventListener('close', () => { modalPendente = null; });

    // Captura antes dos onclick e listeners locais que fecham os formulários.
    document.addEventListener('click', e => {
        const botao = e.target.closest('button');
        const modal = botao?.closest('dialog.templateModal');
        if (!modal || !exigeConfirmacao(modal)) return;
        const cancelar = botao.matches('.btnCancelar, [class^="btnCancelar"], [class*=" btnCancelar"]')
            && botao.textContent.trim().toLowerCase() === 'cancelar';
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
            if (!exigeConfirmacao(modal)) return;
            e.preventDefault();
            solicitarFechamento(modal);
        });

    });

});

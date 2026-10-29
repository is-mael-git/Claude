
const MODAL_INATIVAR = 'modalInativar';

let inativacaoPendente = null;

function endpointStatus() {
    const entidade = window.location.pathname.split('/')[2];

    return `/ParkTec/${entidade}/status`;
}

function pintarStatus(status, ativo) {
    if (!status) {
        return;
    }

    status.textContent = ativo ? 'Ativo' : 'Inativo';

    status.classList.toggle('statusAtivo', ativo);
}

async function salvarStatus(id, ativo) {
    const resposta = await fetch(endpointStatus(), {
        method: 'POST',
        body: new URLSearchParams({ id, ativo: ativo ? '1' : '0' })
    });

    const dados = await resposta.json();

    return Boolean(dados.sucesso);
}

function desfazerAlteracao(alteracao) {
    alteracao.toggle.checked = !alteracao.ativo;

    pintarStatus(alteracao.status, !alteracao.ativo);
}

async function gravarAlteracao(alteracao) {
    alteracao.toggle.disabled = true;

    try {
        const salvou = await salvarStatus(alteracao.id, alteracao.ativo);

        if (!salvou) {
            throw new Error('O banco não aceitou a alteração.');
        }
    } catch (erro) {
        desfazerAlteracao(alteracao);

        alert('Não foi possível alterar o status.');
    } finally {
        alteracao.toggle.disabled = false;
    }
}

document.addEventListener('change', (evento) => {
    const toggle = evento.target.closest('.toggle');

    if (!(toggle instanceof HTMLInputElement) || toggle.type !== 'checkbox') {
        return;
    }

    const linha = toggle.closest('.linhaTabelaCorpo');
    const id = linha?.dataset.id;

    if (!id) {
        return;
    }

    const alteracao = {
        toggle,
        id,
        status: linha.querySelector('.status'),
        ativo: toggle.checked
    };

    pintarStatus(alteracao.status, alteracao.ativo);
    document.querySelector(`#${MODAL_INATIVAR} .textoConfirmacao`).textContent =
        alteracao.ativo ? 'ativar' : 'inativar';

    inativacaoPendente = alteracao;
    abrirModal(MODAL_INATIVAR);
});

document.addEventListener('click', (evento) => {
    const botao = evento.target.closest(
        `#${MODAL_INATIVAR} .btnSalvar, #${MODAL_INATIVAR} .btnCancelar`
    );

    if (!botao) {
        return;
    }

    if (botao.classList.contains('btnSalvar') && inativacaoPendente) {
        const confirmada = inativacaoPendente;

        inativacaoPendente = null;

        gravarAlteracao(confirmada);
    }

    fecharModal(MODAL_INATIVAR);
});

document.getElementById(MODAL_INATIVAR)?.addEventListener('close', () => {
    if (!inativacaoPendente) {
        return;
    }

    desfazerAlteracao(inativacaoPendente);

    inativacaoPendente = null;
});

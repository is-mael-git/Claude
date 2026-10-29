(() => {
    const modal = document.getElementById('validarRelato');

    if (!modal) {
        return;
    }

    const area = modal.querySelector('.areaValidarRelato');
    const endpoint = area.dataset.endpoint;
    const comentarioEndpoint = area.dataset.comentarioEndpoint;
    const iconesAvaliacao = area.dataset.iconeAvaliacao;
    const detalhes = document.getElementById('validarRelatoDetalhes');
    const estado = document.getElementById('validarRelatoEstado');
    const conteudo = document.getElementById('validarRelatoConteudo');
    const titulo = document.getElementById('validarRelatoTitulo');
    const campoComentario = document.getElementById('validarRelatoObservacao');
    const contadorComentario = document.getElementById('validarRelatoContador');
    const botaoSalvar = modal.querySelector('.btnSalvar');
    const modalConfirmacao = document.getElementById('modalInativar');
    const textoConfirmacao = modalConfirmacao.querySelector('.textoConfirmacao');
    const botaoCancelarConfirmacao = modalConfirmacao.querySelector('.btnCancelar');
    const botaoEnviarSemObservacao = modalConfirmacao.querySelector('.btnSalvar');
    const abas = [...modal.querySelectorAll('.validarRelatoAba')];
    let relatos = [];
    let relatoAtual = null;
    let confirmacoesPendentes = [];
    let confirmacaoAceita = false;
    let confirmacaoSemNenhumaObservacao = false;

    function atualizarContadorComentario() {
        contadorComentario.textContent = `${campoComentario.value.length}/${campoComentario.maxLength} caracteres`;
    }

    function formatarResposta(valor) {
        if (valor === null || valor === undefined || valor === '') {
            return '—';
        }

        if (Array.isArray(valor)) {
            return valor.map(formatarResposta).join('\n');
        }

        if (typeof valor === 'object') {
            return Object.entries(valor)
                .map(([chave, item]) => `${chave}: ${formatarResposta(item)}`)
                .join('\n');
        }

        return String(valor);
    }

    function selecionarRelato(emissor) {
        if (relatoAtual) {
            relatoAtual.comentario = campoComentario.value;
        }

        const relato = relatos.find(item => item.emissor === emissor);

        abas.forEach(aba => {
            const ativo = aba.dataset.emissor === emissor && relato;
            aba.classList.toggle('validarRelatoAbaAtiva', Boolean(ativo));
            aba.toggleAttribute('aria-current', Boolean(ativo));
        });

        conteudo.replaceChildren();
        relatoAtual = relato || null;

        if (!relato) {
            titulo.textContent = 'Relato da reunião';
            estado.textContent = 'Não há relato disponível para este participante.';
            campoComentario.value = '';
            atualizarContadorComentario();
            return;
        }

        estado.textContent = '';
        campoComentario.value = relato.comentario ?? '';
        atualizarContadorComentario();
        titulo.textContent = `Relato enviado pelo(a) ${emissor === 'mentor' ? 'mentor' : 'startup'}`;
        detalhes.textContent = [
            relato.startup,
            relato.mentor,
            relato.enviado_em
        ].filter(Boolean).join(' × ');

        const perguntas = Array.isArray(relato.pergunta) ? relato.pergunta : [];
        const respostas = Array.isArray(relato.resposta) ? relato.resposta : [];

        perguntas.forEach((pergunta, indice) => {
            const resposta = respostas.find(item =>
                Number(item.numero) === Number(pergunta.numero)
            );
            const secao = document.createElement('section');
            const tituloPergunta = document.createElement('h3');
            const textoResposta = document.createElement('p');

            secao.className = 'validarRelatoSecao';
            tituloPergunta.textContent = `${pergunta.numero ?? indice + 1}. ${pergunta.pergunta ?? 'Pergunta'}`;

            const nota = Number(resposta?.resposta);
            if (
                emissor === 'startup' &&
                pergunta.tipo === 'nota' &&
                Number.isInteger(nota) &&
                nota >= 1 &&
                nota <= 5
            ) {
                const avaliacao = document.createElement('div');
                const icone = document.createElement('img');

                avaliacao.className = 'validarRelatoAvaliacao';
                icone.src = `${iconesAvaliacao}avaliacao${nota}.svg`;
                icone.alt = `Avaliação ${nota} de 5`;
                avaliacao.appendChild(icone);
                secao.append(tituloPergunta, avaliacao);
            } else {
                textoResposta.textContent = formatarResposta(resposta?.resposta);
                secao.append(tituloPergunta, textoResposta);
            }

            conteudo.appendChild(secao);
        });

    }

    async function carregarRelato(id) {
        estado.textContent = 'Carregando perguntas e respostas...';
        conteudo.replaceChildren();
        abas.forEach(aba => {
            aba.disabled = true;
        });
        relatoAtual = null;
        campoComentario.value = '';
        atualizarContadorComentario();

        try {
            const resposta = await fetch(`${endpoint}?id=${encodeURIComponent(id)}`, {
                headers: { Accept: 'application/json' }
            });
            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(dados.mensagem || 'Não foi possível carregar o relato.');
            }

            relatos = dados.relatos;
            abas.forEach(aba => {
                const disponivel = relatos.some(item => item.emissor === aba.dataset.emissor);
                aba.disabled = !disponivel;
            });

            const primeiroRelato = relatos[0];
            selecionarRelato(primeiroRelato.emissor);
        } catch (erro) {
            relatos = [];
            estado.textContent = erro.message || 'Não foi possível carregar o relato.';
            abas.forEach(aba => {
                aba.disabled = true;
            });
        }
    }

    async function salvarComentarios() {
        if (relatos.length === 0) {
            estado.textContent = 'Carregue os relatos antes de salvar os comentários.';
            return;
        }

        if (relatoAtual) {
            relatoAtual.comentario = campoComentario.value;
        }

        botaoSalvar.disabled = true;

        try {
            const resposta = await fetch(comentarioEndpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    comentarios: relatos.map(relato => ({
                        id: relato.id,
                        comentario: relato.comentario ?? ''
                    }))
                })
            });
            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(dados.mensagem || 'Não foi possível salvar o comentário.');
            }

            relatos.forEach(relato => {
                relato.comentario = (relato.comentario ?? '').trim();
            });
            modal.close();
            window.mostrarModalSalvo?.();
        } catch (erro) {
            estado.textContent = erro.message || 'Não foi possível salvar o comentário.';
        } finally {
            botaoSalvar.disabled = false;
        }
    }

    function exibirConfirmacaoSemObservacao(relato) {
        textoConfirmacao.textContent = relato.emissor === 'mentor'
            ? 'enviar sem observação do mentor'
            : 'enviar sem observação da startup';
        abrirModal('modalInativar');
    }

    function confirmarObservacoesPendentes() {
        const semObservacao = relatos.filter(relato =>
            !(relato.comentario ?? '').trim()
        );

        if (semObservacao.length === 0) {
            salvarComentarios();
            return;
        }

        confirmacoesPendentes = [...semObservacao];
        confirmacaoSemNenhumaObservacao =
            relatos.length === 2 && semObservacao.length === 2;

        if (confirmacaoSemNenhumaObservacao) {
            textoConfirmacao.textContent = 'enviar sem nenhuma observação';
            abrirModal('modalInativar');
            return;
        }

        exibirConfirmacaoSemObservacao(confirmacoesPendentes[0]);
    }

    botaoCancelarConfirmacao.addEventListener('click', () => {
        confirmacoesPendentes = [];
        confirmacaoSemNenhumaObservacao = false;
        fecharModal('modalInativar');
    });

    botaoEnviarSemObservacao.addEventListener('click', () => {
        confirmacaoAceita = true;
        fecharModal('modalInativar');
    });

    modalConfirmacao.addEventListener('close', () => {
        if (!confirmacaoAceita) {
            confirmacoesPendentes = [];
            return;
        }

        confirmacaoAceita = false;

        if (confirmacaoSemNenhumaObservacao) {
            confirmacaoSemNenhumaObservacao = false;
            confirmacoesPendentes = [];
            salvarComentarios();
            return;
        }

        confirmacoesPendentes.shift();

        if (confirmacoesPendentes.length === 0) {
            salvarComentarios();
            return;
        }

        exibirConfirmacaoSemObservacao(confirmacoesPendentes[0]);
    });

    campoComentario.addEventListener('input', () => {
        if (relatoAtual) {
            relatoAtual.comentario = campoComentario.value;
        }

        atualizarContadorComentario();
    });
    atualizarContadorComentario();

    document.addEventListener('click', evento => {
        const botaoRelato = evento.target.closest('[data-relato-id]');

        if (botaoRelato) {
            carregarRelato(botaoRelato.dataset.relatoId);
            return;
        }

        const aba = evento.target.closest('.validarRelatoAba');

        if (aba && modal.contains(aba) && !aba.disabled) {
            selecionarRelato(aba.dataset.emissor);
            return;
        }

        if (evento.target.closest('.btnSalvar') && modal.contains(evento.target)) {
            confirmarObservacoesPendentes();
        }
    });
})();

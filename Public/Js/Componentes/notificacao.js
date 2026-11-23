document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('#modalNotificacao');

    if (!modal) {
        return;
    }

    const abas = modal.querySelectorAll('.menuLateralBotoesNotificacao[data-status]');
    const lista = modal.querySelector('.menuLateralListaNotificacoes');
    const mensagemVazia = modal.querySelector('.menuLateralSemNotificacoes');
    const botaoAbrir = document.querySelector('[data-modal="modalNotificacao"]');
    const indicador = document.querySelector('.menuLateralIndicadorNaoLidas');
    const mensagens = {
        entrada: 'Nenhuma notificação na caixa de entrada.',
        arquivado: 'Nenhuma notificação arquivada.',
        lixeira: 'Nenhuma notificação na lixeira.'
    };
    let abaAtual = null;
    let consultaAtual = 0;
    let abrindoNotificacoes = false;
    const visualizacoesPendentes = new Set();
    const idsReconhecidos = new Set();
    const recentesDaAbertura = new Set();

    function atualizarIndicador() {
        if (indicador && lista) {
            const novas = [...lista.querySelectorAll('.menuLateralNotificacao[data-status="entrada"][data-visualizado="0"]')]
                .some(notificacao => !idsReconhecidos.has(Number(notificacao.dataset.id)));
            indicador.toggleAttribute('hidden', abrindoNotificacoes || !novas);
        }
    }

    async function sincronizarNotificacoes() {
        const consulta = ++consultaAtual;
        const resposta = await fetch('/ParkTec/notificacoes/listar', { cache: 'no-store' });
        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {
            throw new Error('Não foi possível atualizar as notificações.');
        }

        if (consulta !== consultaAtual || !lista) {
            return false;
        }

        aplicarDados(dados);
        return true;
    }

    function aplicarDados(dados) {
        lista.innerHTML = dados.html;
        mostrarAba(abaAtual);
        atualizarIndicador();
    }

    function ajustarListaERegistrarVistas() {
        const visiveis = [...lista.querySelectorAll('.menuLateralNotificacao:not([hidden])')];
        const alturaCinco = visiveis.slice(0, 5)
            .reduce((altura, notificacao) => altura + notificacao.offsetHeight, 0);

        lista.style.maxHeight = alturaCinco
            ? `min(${alturaCinco}px, calc(100vh - 11rem))`
            : '';

        registrarVistas();
    }

    function registrarVistas() {
        if (!modal.open || abaAtual !== null) {
            return;
        }

        const limite = lista.getBoundingClientRect();
        lista.querySelectorAll('.menuLateralNotificacao:not([hidden])').forEach(notificacao => {
            const posicao = notificacao.getBoundingClientRect();
            if (posicao.top < limite.bottom && posicao.bottom > limite.top) {
                const id = Number(notificacao.dataset.id);
                if (!idsReconhecidos.has(id)) {
                    visualizacoesPendentes.add(id);
                }
            }
        });
    }

    function mostrarAba(status = null) {
        abaAtual = status;
        let quantidade = 0;

        abas.forEach(aba => {
            aba.setAttribute('aria-pressed', String(aba.dataset.status === status));
        });

        lista?.querySelectorAll('.menuLateralNotificacao[data-status]').forEach(notificacao => {
            const statusNotificacao = notificacao.dataset.status;
            const estaNaEntrada = statusNotificacao === 'entrada';
            const corresponde = status === null
                ? estaNaEntrada && (
                    notificacao.dataset.visualizado === '0'
                    || recentesDaAbertura.has(Number(notificacao.dataset.id))
                )
                : statusNotificacao === status;
            const visivel = corresponde;

            notificacao.hidden = !visivel;
            quantidade += Number(visivel);

            const acoes = notificacao.querySelector('.caixaOpcaolerArquivar');
            const dispensar = notificacao.querySelector('.menuLateralDispensarNotificacao');
            const restaurar = notificacao.querySelector('.menuLateralRestaurarNotificacao');

            if (acoes) {
                acoes.hidden = !estaNaEntrada;
            }

            if (dispensar) {
                dispensar.hidden = statusNotificacao === 'lixeira';
            }

            if (restaurar) {
                restaurar.hidden = estaNaEntrada;
            }
        });

        if (mensagemVazia) {
            mensagemVazia.textContent = status === null
                ? 'Nenhuma notificação nova.'
                : mensagens[status];
            mensagemVazia.hidden = quantidade > 0;
        }

        requestAnimationFrame(ajustarListaERegistrarVistas);
    }

    async function confirmarVisualizacoes() {
        const ids = [...visualizacoesPendentes];
        if (!ids.length) {
            return;
        }

        visualizacoesPendentes.clear();
        const body = new URLSearchParams();
        ids.forEach(id => body.append('ids[]', id));

        try {
            const resposta = await fetch('/ParkTec/notificacoes/visualizar', {
                method: 'POST',
                body
            });
            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error('Não foi possível marcar as notificações como vistas.');
            }

            const visualizadas = new Set(ids);
            ids.forEach(id => idsReconhecidos.add(id));
            lista.querySelectorAll('.menuLateralNotificacao[data-visualizado="0"]').forEach(notificacao => {
                if (visualizadas.has(Number(notificacao.dataset.id))) {
                    notificacao.dataset.visualizado = '1';
                }
            });
            atualizarIndicador();
        } catch (erro) {
            ids.forEach(id => visualizacoesPendentes.add(id));
            console.error(erro);
        }
    }

    abas.forEach(aba => {
        aba.addEventListener('click', () => {
            if (abaAtual === null) {
                confirmarVisualizacoes();
            }
            mostrarAba(aba.dataset.status);
        });
    });

    lista.addEventListener('scroll', registrarVistas);

    modal.addEventListener('click', async event => {
        const botao = event.target.closest('[data-acao-notificacao]');
        const notificacao = botao?.closest('.menuLateralNotificacao');

        if (!notificacao || !modal.contains(notificacao)) {
            return;
        }

        const id = Number(notificacao.dataset.id);
        const status = botao.dataset.acaoNotificacao;
        const botoes = notificacao.querySelectorAll('[data-acao-notificacao]');

        botoes.forEach(item => { item.disabled = true; });

        try {
            const resposta = await fetch('/ParkTec/notificacoes/status', {
                method: 'POST',
                body: new URLSearchParams({ id, status })
            });
            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error(dados.mensagem || 'Não foi possível mover a notificação.');
            }

            notificacao.dataset.status = status;
            notificacao.dataset.visualizado = '1';
            idsReconhecidos.add(id);
            mostrarAba(abaAtual);
            atualizarIndicador();
            sincronizarNotificacoes().catch(() => {});
        } catch (erro) {
            alert(erro.message || 'Não foi possível mover a notificação.');
        } finally {
            botoes.forEach(item => { item.disabled = false; });
        }
    });

    botaoAbrir?.addEventListener('click', async () => {
        recentesDaAbertura.clear();
        lista.querySelectorAll('.menuLateralNotificacao[data-status="entrada"][data-visualizado="0"]')
            .forEach(notificacao => recentesDaAbertura.add(Number(notificacao.dataset.id)));

        abrindoNotificacoes = true;
        if (indicador) {
            indicador.setAttribute('hidden', '');
        }

        requestAnimationFrame(ajustarListaERegistrarVistas);

        try {
            const resposta = await fetch('/ParkTec/notificacoes/visualizar', {
                method: 'POST',
                body: new URLSearchParams({ abrir: '1' })
            });
            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error('Não foi possível registrar a abertura das notificações.');
            }

            if (modal.open) {
                recentesDaAbertura.clear();
                dados.ids.forEach(id => recentesDaAbertura.add(Number(id)));
            }

            dados.ids.forEach(id => {
                const numero = Number(id);
                idsReconhecidos.add(numero);
                visualizacoesPendentes.delete(numero);
            });

            abrindoNotificacoes = false;
            await sincronizarNotificacoes();
            atualizarIndicador();
        } catch (erro) {
            abrindoNotificacoes = false;
            atualizarIndicador();
            console.error(erro);
        }
    });

    modal.addEventListener('close', () => {
        confirmarVisualizacoes();
        recentesDaAbertura.clear();
        mostrarAba();
        sincronizarNotificacoes().catch(() => {});
    });

    mostrarAba();
    sincronizarNotificacoes().catch(() => {});

    const eventos = new EventSource('/ParkTec/notificacoes/eventos');
    eventos.onmessage = event => {
        try {
            const dados = JSON.parse(event.data);
            if (dados.sucesso) {
                consultaAtual++;
                aplicarDados(dados);
            }
        } catch (erro) {
            console.error('Não foi possível receber as notificações.', erro);
        }
    };
});

(() => {
    const script = document.currentScript;
    const endpoint = script?.dataset.endpoint || '/ParkTec/sessao/renovar';
    const urlLogin = script?.dataset.loginUrl || '/ParkTec/login';
    const tempoMaximoInativo = Math.max(1, Number(script?.dataset.ttl) || 1800) * 1000;
    const intervaloMinimoRequisicao = 1000;
    const idSessao = script?.dataset.sessionId || 'sessao-atual';
    const chaveInteracoes = `parktec:interacoes:${idSessao}`;

    let penultimaInteracao = 0;
    let ultimaInteracao = 0;
    let ultimaRequisicao = 0;
    let requisicaoEmAndamento = false;
    let atividadePendente = false;
    let temporizadorRequisicao = null;
    let temporizadorArmazenamento = null;
    let sessaoExpirada = false;

    try {
        const interacoes = JSON.parse(sessionStorage.getItem(chaveInteracoes) || '[]');
        if (Array.isArray(interacoes)) {
            const validas = interacoes.filter(valor => Number.isFinite(valor) && valor > 0).slice(-2);
            penultimaInteracao = validas.length === 2 ? validas[0] : 0;
            ultimaInteracao = validas.length ? validas[validas.length - 1] : 0;
        }
    } catch (erro) {
        // Se o armazenamento do navegador estiver indisponível, a sessão
        // continua sendo validada pelo token no servidor.
    }

    function irParaLogin() {
        if (sessaoExpirada) return;
        sessaoExpirada = true;
        window.location.assign(`${urlLogin}?sessaoExpirada=1`);
    }

    function salvarInteracoes() {
        temporizadorArmazenamento = null;
        try {
            sessionStorage.setItem(chaveInteracoes, JSON.stringify(
                [penultimaInteracao, ultimaInteracao].filter(valor => valor > 0)
            ));
        } catch (erro) { }
    }

    function agendarArmazenamento() {
        if (temporizadorArmazenamento !== null) return;
        temporizadorArmazenamento = window.setTimeout(salvarInteracoes, 250);
    }

    function agendarRenovacao() {
        if (temporizadorRequisicao !== null || sessaoExpirada) return;

        const espera = Math.max(0, intervaloMinimoRequisicao - (Date.now() - ultimaRequisicao));
        temporizadorRequisicao = window.setTimeout(() => {
            temporizadorRequisicao = null;
            if (atividadePendente) renovarToken();
        }, espera);
    }

    async function renovarToken() {
        if (sessaoExpirada) return;

        if (requisicaoEmAndamento) {
            atividadePendente = true;
            return;
        }

        const espera = intervaloMinimoRequisicao - (Date.now() - ultimaRequisicao);
        if (espera > 0) {
            atividadePendente = true;
            agendarRenovacao();
            return;
        }

        atividadePendente = false;
        requisicaoEmAndamento = true;
        ultimaRequisicao = Date.now();

        try {
            const resposta = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            });

            if (resposta.status === 401 || resposta.redirected) {
                irParaLogin();
            }
        } catch (erro) {
            // A próxima interação tenta novamente se a rede falhar.
        } finally {
            requisicaoEmAndamento = false;
            if (atividadePendente && !sessaoExpirada) agendarRenovacao();
        }
    }

    function registrarInteracao() {
        if (sessaoExpirada) return;

        const agora = Date.now();
        penultimaInteracao = ultimaInteracao;

        if (penultimaInteracao && agora - penultimaInteracao >= tempoMaximoInativo) {
            irParaLogin();
            return;
        }

        ultimaInteracao = agora;
        agendarArmazenamento();

        // Qualquer interação válida renova o JWT. Eventos frequentes, como
        // pointermove, são agrupados em no máximo uma requisição por segundo.
        atividadePendente = true;
        renovarToken();
    }

    [
        'pointerdown', 'pointermove', 'mousemove', 'click', 'keydown', 'keyup',
        'input', 'change', 'scroll', 'wheel', 'touchstart', 'touchmove'
    ].forEach(evento => {
        window.addEventListener(evento, registrarInteracao, {
            capture: true,
            passive: true
        });
    });

    window.addEventListener('pagehide', salvarInteracoes);
})();

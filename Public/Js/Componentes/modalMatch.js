let mentoriaSelecionadaId = null;

document.addEventListener('click', async (event) => {
    const botao = event.target.closest('[data-modal="modalMatch"]');

    if (!botao) {
        return;
    }

    const id = botao.dataset.mentoriaId;
    mentoriaSelecionadaId = id;
    const botaoAprovar = document.getElementById('modalMatchAprovar');
    const startup = document.getElementById('modalMatchStartup');
    const mentor = document.getElementById('modalMatchMentor');
    const competencias = document.querySelector(
        '#modalMatch .areaCardInformativoCompetencias'
    );
    const fragilidades = document.getElementById('modalMatchFragilidades');
    const icones = [...competencias.querySelectorAll('i')]
        .map((icone) => icone.className);

    startup.textContent = 'Carregando...';
    mentor.textContent = 'Carregando...';
    botaoAprovar.disabled = false;
    botaoAprovar.textContent = 'APROVAR';
    competencias.replaceChildren();
    fragilidades.replaceChildren();

    try {
        if (!id || Number(id) < 1) {
            throw new Error('Não há mentoria cadastrada para demonstrar.');
        }

        const resposta = await fetch(
            `/ParkTec/areaTrabalho/adm/mentoria?id=${encodeURIComponent(id)}`
        );

        const resultado = await resposta.json();

        if (!resposta.ok || !resultado.sucesso) {
            throw new Error(resultado.mensagem || 'Não foi possível carregar a mentoria.');
        }

        startup.textContent = resultado.mentoria.startup_nome;
        mentor.textContent = resultado.mentoria.mentor_nome;

        if (resultado.mentoria.competencias.length === 0) {
            competencias.textContent = 'Este mentor ainda não possui competências cadastradas.';
            fragilidades.textContent = 'Nenhuma fragilidade para exibir.';
            return;
        }

        resultado.mentoria.competencias.forEach((nome, indice) => {
            const cartao = document.createElement('div');
            const icone = document.createElement('i');
            cartao.className = 'cardInformativoCompetencias';
            icone.className = icones[indice] || 'fa-solid fa-star';
            cartao.append(icone, document.createTextNode(` ${nome}`));
            competencias.appendChild(cartao);

            const cartaoFragilidade = cartao.cloneNode(true);
            cartaoFragilidade.className = 'cardInformativoFragilidades';
            fragilidades.appendChild(cartaoFragilidade);
        });
    } catch (erro) {
        startup.textContent = 'Erro ao carregar';
        mentor.textContent = 'Erro ao carregar';
        competencias.textContent = erro.message;
        fragilidades.textContent = erro.message;
    }
});

document.getElementById('modalMatchAprovar')?.addEventListener('click', async (event) => {
    const botao = event.currentTarget;

    if (!mentoriaSelecionadaId) {
        window.alert('Não foi selecionada uma mentoria.');
        return;
    }

    botao.disabled = true;

    try {
        const resposta = await fetch('/ParkTec/areaTrabalho/adm/mentoria/aprovar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json'
            },
            body: JSON.stringify({ id: mentoriaSelecionadaId })
        });
        const resultado = await resposta.json();

        if (!resposta.ok || !resultado.sucesso) {
            throw new Error(resultado.mensagem || 'Não foi possível aprovar a mentoria.');
        }

        fecharModal('modalMatch');

        if (typeof window.mostrarModalSalvo === 'function') {
            window.mostrarModalSalvo();
        } else {
            window.alert('Salvo com Sucesso!');
        }
    } catch (erro) {
        botao.disabled = false;
        window.alert(erro.message);
    }
});
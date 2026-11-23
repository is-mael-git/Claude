document.addEventListener('DOMContentLoaded', () => {

    const modalCriarQuestoes =
        document.querySelector(
            '#modalCriarQuestoes'
        );

    const formQuestao =
        document.querySelector(
            '.formQuestao'
        );

    const perguntaQuestao =
        document.querySelector(
            '#perguntaQuestao'
        );

    const erroPerguntaQuestao =
        document.querySelector('#erroPerguntaQuestao');

    const erroCategoriaQuestao =
        document.querySelector('#erroCategoriaQuestao');

    const contadorQuestao =
        document.querySelector(
            '.contadorQuestao'
        );

    const textoSelectQuestao =
        document.querySelector(
            '.textoSelectQuestao'
        );

    const botaoSelectQuestao =
        document.querySelector('.selecionadoSelectQuestao');

    const inputEixoQuestao =
        document.querySelector(
            '#idCategoriaQuestao'
        );

    const opcoesQuestao =
        document.querySelectorAll(
            '.opcaoSelectQuestao'
        );

    const selectSubcategoriaQuestao = document.querySelector('#idSubcategoriaQuestao');

    function filtrarSubcategorias() {
        const categoria = inputEixoQuestao.value;
        selectSubcategoriaQuestao.value = '';
        selectSubcategoriaQuestao.disabled = !categoria;
        selectSubcategoriaQuestao.querySelectorAll('option[data-categoria]').forEach(opcao => {
            const disponivel = opcao.dataset.categoria === categoria;
            opcao.hidden = !disponivel;
            opcao.disabled = !disponivel;
        });
    }
    inputEixoQuestao.addEventListener('change', filtrarSubcategorias);

    const botaoSalvarQuestao =
        document.querySelector(
            '.btnSalvarQuestao'
        );

    const listaGerenciarQuestoes =
        document.querySelector(
            '#listaGerenciarQuestoes'
        );

    const botaoFiltroGerenciarQuestao =
        document.querySelector(
            '#botaoFiltroGerenciarQuestao'
        );

    const nomeFiltroGerenciarQuestao =
        document.querySelector(
            '#nomeFiltroGerenciarQuestao'
        );

    const opcoesFiltroGerenciarQuestao =
        document.querySelector(
            '#opcoesFiltroGerenciarQuestao'
        );

    const opcoesEixo =
        document.querySelectorAll(
            '.opcaoFiltroGerenciarQuestao'
        );


    let eixoAtual = 'todos';

    function resetarFormularioQuestao() {

        if (!formQuestao) {
            return;
        }

        formQuestao.reset();

        erroPerguntaQuestao.hidden = true;
        erroCategoriaQuestao.hidden = true;

        if (contadorQuestao) {
            contadorQuestao.textContent =
                '0 / 255';
        }

        if (textoSelectQuestao) {
            textoSelectQuestao.textContent =
                'Selecione uma categoria';
        }

        if (inputEixoQuestao) {
            inputEixoQuestao.value = '';
            filtrarSubcategorias();
        }

        opcoesQuestao.forEach(opcao => {
            opcao.classList.remove(
                'opcaoAtualQuestao'
            );
        });
    }


    function fecharModalQuestao() {

        if (
            modalCriarQuestoes &&
            modalCriarQuestoes.open
        ) {

            modalCriarQuestoes.close();
        }
    }


    if (modalCriarQuestoes) {

        modalCriarQuestoes.addEventListener(
            'close',
            () => {

                modalCriarQuestoes.dataset.origem =
                    '';
            }
        );
    }


    function normalizarTexto(texto) {

        return texto
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }


    function escaparHTML(texto) {

        const elemento =
            document.createElement('div');

        elemento.textContent =
            texto ?? '';

        return elemento.innerHTML;
    }


    if (perguntaQuestao && contadorQuestao) {

        perguntaQuestao.addEventListener(
            'input',
            () => {

                contadorQuestao.textContent =
                    `${perguntaQuestao.value.length} / 255`;

                if (perguntaQuestao.value.trim() !== '') {
                    erroPerguntaQuestao.hidden = true;
                }
            }
        );
    }

    opcoesQuestao.forEach(opcao => {
        opcao.addEventListener('click', () => {
            erroCategoriaQuestao.hidden = true;
        });
    });


    if (formQuestao) {

        formQuestao.addEventListener(
            'submit',
            async event => {

                event.preventDefault();

                const pergunta =
                    perguntaQuestao
                        ? perguntaQuestao.value.trim()
                        : '';

                const idEixo =
                    inputEixoQuestao
                        ? inputEixoQuestao.value
                        : '';

                erroPerguntaQuestao.hidden = pergunta !== '';
                erroCategoriaQuestao.hidden = idEixo !== '';

                if (pergunta === '' || idEixo === '' || !selectSubcategoriaQuestao.value) {
                    if (idEixo !== '' && !selectSubcategoriaQuestao.value) {
                        alert('Selecione uma subcategoria.');
                    }
                    if (pergunta === '') {
                        perguntaQuestao.focus();
                    } else {
                        botaoSelectQuestao.focus();
                    }
                    return;
                }

                const dados =
                    new FormData(formQuestao);

                try {

                    if (botaoSalvarQuestao) {
                        botaoSalvarQuestao.disabled =
                            true;
                    }

                    const resposta =
                        await fetch(
                            formQuestao.action,
                            {
                                method: 'POST',
                                body: dados
                            }
                        );

                    const resultado =
                        await resposta.json();

                    if (!resposta.ok) {

                        alert(
                            resultado.mensagem ||
                            'Não foi possível cadastrar a questão.'
                        );

                        return;
                    }

                    const origemModalQuestao =
                        modalCriarQuestoes
                            ? modalCriarQuestoes.dataset.origem
                            : '';

                    const abertoPeloQuestionario =
                        origemModalQuestao === 'questionario' ||
                        origemModalQuestao === 'atualizarQuestionario';

                    const finalizarCadastroQuestao = () => {

                        fecharModalQuestao();
                        resetarFormularioQuestao();

                        if (abertoPeloQuestionario) {

                            modalCriarQuestoes.dataset.origem =
                                '';

                            const seletorModalQuestionario =
                                origemModalQuestao ===
                                'atualizarQuestionario'
                                    ? '#modalAtualizarQuestionario'
                                    : '#modalNovoQuestionario';

                            const modalQuestionario =
                                document.querySelector(
                                    seletorModalQuestionario
                                );

                            if (modalQuestionario?.open) {

                                modalQuestionario.close();
                            }

                            const retornarAoQuestionario = () => {

                                if (modalQuestionario) {

                                    modalQuestionario.showModal();
                                }

                                document.dispatchEvent(
                                    new CustomEvent(
                                        'questaoCadastradaQuestionario',
                                        {
                                            detail: {
                                                origem: origemModalQuestao
                                            }
                                        }
                                    )
                                );
                            };

                            retornarAoQuestionario();

                            return;
                        }

                        sessionStorage.setItem('mostrarModalSalvo', '1');

                        window.location.reload();
                    };

                    finalizarCadastroQuestao();

                } catch (erro) {

                    console.error(erro);

                    alert(
                        'Ocorreu um erro ao cadastrar a questão.'
                    );

                } finally {

                    if (botaoSalvarQuestao) {
                        botaoSalvarQuestao.disabled =
                            false;
                    }
                }
            }
        );
    }


    async function carregarQuestoes() {

        if (!listaGerenciarQuestoes) {
            return;
        }

        try {

            listaGerenciarQuestoes.innerHTML =
                '<p>Carregando questões...</p>';


            const resposta = await fetch(
                '/ParkTec/questao/listar'
            );


            if (!resposta.ok) {

                throw new Error(
                    'Erro ao carregar questões.'
                );
            }


            const questoes =
                await resposta.json();


            listaGerenciarQuestoes.innerHTML =
                '';


            if (questoes.length === 0) {

                listaGerenciarQuestoes.innerHTML =
                    '<p>Nenhuma questão cadastrada.</p>';

                return;
            }


            questoes.forEach(
                questao => {

                    const eixos =
                        questao.categoria
                            ? questao.categoria
                                .split(',')
                                .map(
                                    eixo =>
                                        normalizarTexto(eixo)
                                )
                            : [];


                    const eixosTexto =
                        eixos.join(',');


                    const nomesEixos =
                        questao.categoria
                            ? questao.categoria
                                .split(',')
                                .join(' • ')
                            : 'Sem categoria';


                    const checked =
                        Number(questao.ativo) === 1
                            ? 'checked'
                            : '';


                    listaGerenciarQuestoes
                        .insertAdjacentHTML(
                            'beforeend',
                            `
                            <div
                                class="itemGerenciarQuestao"
                                data-id="${questao.id}"
                                data-categoria="${eixosTexto}"
                            >

                                <div class="textoGerenciarQuestao">

                                    <span class="numeroGerenciarQuestao">
                                        ${questao.id}
                                    </span>

                                    <p>
                                        ${escaparHTML(
                                            questao.pergunta
                                        )}
                                    </p>

                                </div>


                                <div class="acoesGerenciarQuestao">

                                    <span class="eixoGerenciarQuestao">
                                        ${escaparHTML(
                                            nomesEixos
                                        )}
                                    </span>


                                    <div class="btnSwitch">

                                        <label class="switch">

                                            <input
                                                type="checkbox"
                                                class="toggle toggleQuestao"
                                                data-id="${questao.id}"
                                                ${checked}
                                            >

                                            <span class="slider"></span>

                                        </label>

                                    </div>

                                </div>

                            </div>
                            `
                        );
                }
            );


            filtrarQuestoes();

        } catch (erro) {

            console.error(erro);

            listaGerenciarQuestoes.innerHTML = `
                <p>
                    Não foi possível carregar as questões.
                </p>
            `;
        }
    }


    function filtrarQuestoes() {

        const itens =
            document.querySelectorAll(
                '.itemGerenciarQuestao'
            );


        itens.forEach(item => {

            const eixos =
                (
                    item.dataset.categoria || ''
                )
                    .split(',')
                    .filter(
                        eixo => eixo !== ''
                    );


            const mostrar =
                eixoAtual === 'todos' ||
                eixos.includes(eixoAtual);


            item.style.display =
                mostrar
                    ? 'flex'
                    : 'none';
        });
    }


    if (botaoFiltroGerenciarQuestao) {

        botaoFiltroGerenciarQuestao
            .addEventListener(
                'click',
                event => {

                    event.stopPropagation();

                    opcoesFiltroGerenciarQuestao
                        ?.classList
                        .toggle(
                            'abertoGerenciarQuestao'
                        );
                }
            );
    }


    opcoesEixo.forEach(opcao => {

        opcao.addEventListener(
            'click',
            () => {

                eixoAtual =
                    opcao.dataset.categoria;


                if (nomeFiltroGerenciarQuestao) {

                    nomeFiltroGerenciarQuestao
                        .textContent =
                        opcao.textContent.trim();
                }


                opcoesFiltroGerenciarQuestao
                    ?.classList
                    .remove(
                        'abertoGerenciarQuestao'
                    );


                filtrarQuestoes();
            }
        );
    });


    document.addEventListener(
        'click',
        event => {

            if (
                botaoFiltroGerenciarQuestao &&
                opcoesFiltroGerenciarQuestao &&
                !botaoFiltroGerenciarQuestao
                    .contains(
                        event.target
                    ) &&
                !opcoesFiltroGerenciarQuestao
                    .contains(
                        event.target
                    )
            ) {

                opcoesFiltroGerenciarQuestao
                    .classList
                    .remove(
                        'abertoGerenciarQuestao'
                    );
            }
        }
    );


    const modalConfirmacaoFecharQuestao = document.querySelector('#modalInativar');
    const botaoNaoFecharQuestao = modalConfirmacaoFecharQuestao?.querySelector('.btnCancelar');
    const botaoSimFecharQuestao = modalConfirmacaoFecharQuestao?.querySelector('.btnSalvar');

    modalCriarQuestoes?.addEventListener('fechamentoConfirmado', resetarFormularioQuestao);

    const tabelaQuestoes = document.querySelector('.tabelaQuestoes');
    const paragrafoStatusQuestao = modalConfirmacaoFecharQuestao?.querySelector('p');

    if (tabelaQuestoes && paragrafoStatusQuestao && botaoNaoFecharQuestao
        && botaoSimFecharQuestao) {
        const mensagemPadrao = paragrafoStatusQuestao.innerHTML;
        const textoBotaoNao = botaoNaoFecharQuestao.textContent;
        const textoBotaoSim = botaoSimFecharQuestao.textContent;
        let alteracaoPendente = null;

        function restaurarModalStatus() {
            modalConfirmacaoFecharQuestao.classList.remove('modalQuestaoComVinculos');
            paragrafoStatusQuestao.innerHTML = mensagemPadrao;
            botaoNaoFecharQuestao.textContent = textoBotaoNao;
            botaoSimFecharQuestao.textContent = textoBotaoSim;
        }

        function pintarStatusQuestao(status, ativo) {
            status.textContent = ativo ? 'Ativo' : 'Inativo';
            status.classList.toggle('statusAtivo', ativo);
        }

        function desfazerAlteracaoQuestao(alteracao) {
            alteracao.toggle.checked = !alteracao.ativo;
            pintarStatusQuestao(alteracao.status, !alteracao.ativo);
        }

        document.addEventListener('change', async event => {
            const toggle = event.target.closest('.tabelaQuestoes .toggle');
            if (!toggle) return;

            const linha = toggle.closest('.linhaTabelaCorpo');
            const id = linha?.dataset.id;
            const status = linha?.querySelector('.status');
            const ativo = toggle.checked;

            if (!id || !status) {
                toggle.checked = !ativo;
                return;
            }

            toggle.disabled = true;

            try {
                let nomesQuestionarios = [];

                if (!ativo) {
                    const resposta = await fetch(
                        `/ParkTec/questao/buscar?id=${encodeURIComponent(id)}`,
                        { cache: 'no-store' }
                    );
                    const questao = await resposta.json();

                    if (!resposta.ok || !Array.isArray(questao.questionarios)) {
                        throw new Error('Não foi possível verificar os questionários da questão.');
                    }

                    nomesQuestionarios = questao.questionarios;
                }

                restaurarModalStatus();

                if (nomesQuestionarios.length > 0) {
                    const lista = nomesQuestionarios.join(', ');
                    modalConfirmacaoFecharQuestao.classList.add('modalQuestaoComVinculos');
                    paragrafoStatusQuestao.textContent = nomesQuestionarios.length === 1
                        ? `Esta questão está vinculada ao questionário ${lista}. Ao inativar, ela será removida dele. Deseja continuar?`
                        : `Esta questão está vinculada aos questionários ${lista}. Ao inativar, ela será removida deles. Deseja continuar?`;
                    botaoNaoFecharQuestao.textContent = 'Não';
                    botaoSimFecharQuestao.textContent = 'Sim';
                } else {
                    paragrafoStatusQuestao.querySelector('.textoConfirmacao').textContent =
                        ativo ? 'ativar' : 'inativar';
                }

                alteracaoPendente = { toggle, id, status, ativo };
                pintarStatusQuestao(status, ativo);
                abrirModal('modalInativar');
            } catch (erro) {
                alteracaoPendente = null;
                restaurarModalStatus();
                desfazerAlteracaoQuestao({ toggle, status, ativo });
                toggle.disabled = false;
                alert(erro.message || 'Não foi possível verificar os questionários da questão.');
            }
        });

        botaoNaoFecharQuestao.addEventListener('click', event => {
            if (!alteracaoPendente) return;
            event.stopPropagation();
            fecharModal('modalInativar');
        });

        botaoSimFecharQuestao.addEventListener('click', async event => {
            if (!alteracaoPendente) return;
            event.stopPropagation();

            const alteracao = alteracaoPendente;
            alteracaoPendente = null;
            fecharModal('modalInativar');

            try {
                const resposta = await fetch('/ParkTec/questao/status', {
                    method: 'POST',
                    body: new URLSearchParams({
                        id: alteracao.id,
                        ativo: alteracao.ativo ? '1' : '0'
                    })
                });
                const dados = await resposta.json();

                if (!resposta.ok || !dados.sucesso) {
                    throw new Error(dados.mensagem || 'Não foi possível alterar o status da questão.');
                }

                pintarStatusQuestao(alteracao.status, alteracao.ativo);

            } catch (erro) {
                desfazerAlteracaoQuestao(alteracao);
                alert(erro.message || 'Não foi possível alterar o status da questão.');
            } finally {
                alteracao.toggle.disabled = false;
            }
        });

        modalConfirmacaoFecharQuestao.addEventListener('close', () => {
            if (alteracaoPendente) {
                desfazerAlteracaoQuestao(alteracaoPendente);
                alteracaoPendente.toggle.disabled = false;
                alteracaoPendente = null;
            }

            restaurarModalStatus();
        });
    }

    carregarQuestoes();

});

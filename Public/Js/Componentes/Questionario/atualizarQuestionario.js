document.addEventListener('DOMContentLoaded', () => {

    const modalAtualizarQuestionario = document.querySelector(
        '#modalAtualizarQuestionario'
    );

    const formularioAtualizarQuestionario = document.querySelector(
        '.formularioAtualizarQuestionario'
    );

    const telasAtualizarQuestionario = document.querySelectorAll(
        '.telaAtualizarQuestionario'
    );

    const etapasAtualizarQuestionario = document.querySelectorAll(
        '.etapaAtualizarQuestionario'
    );

    const botoesAvancarAtualizarQuestionario = document.querySelectorAll(
        '.btnAvancarAtualizarQuestionario'
    );

    const botoesVoltarAtualizarQuestionario = document.querySelectorAll(
        '.btnVoltarAtualizarQuestionario'
    );

    const botoesCancelarAtualizarQuestionario = document.querySelectorAll(
        '.btnCancelarAtualizarQuestionario'
    );

    const botaoSalvarAtualizarQuestionario = document.querySelector(
        '.btnSalvarAtualizarQuestionario'
    );

    const botaoAdicionarQuestaoAtualizarQuestionario = document.querySelector(
        '#botaoAdicionarQuestaoAtualizarQuestionario'
    );

    const modalCriarQuestoes = document.querySelector(
        '#modalCriarQuestoes'
    );

    const nomeAtualizarQuestionario = document.querySelector(
        '#nomeAtualizarQuestionario'
    );

    const erroNomeAtualizarQuestionario = document.querySelector(
        '#erroNomeAtualizarQuestionario'
    );

    const descricaoAtualizarQuestionario = document.querySelector(
        '#descricaoAtualizarQuestionario'
    );

    const contadorCaracteresAtualizarQuestionario = document.querySelector(
        '.contadorCaracteresAtualizarQuestionario'
    );

    const revisaoNomeAtualizarQuestionario = document.querySelector(
        '#revisaoNomeAtualizarQuestionario'
    );

    const revisaoDescricaoAtualizarQuestionario = document.querySelector(
        '#revisaoDescricaoAtualizarQuestionario'
    );

    const revisaoQuantidadeAtualizarQuestionario = document.querySelector(
        '#revisaoQuantidadeAtualizarQuestionario'
    );

    const listaQuestoesAtualizarQuestionario = document.querySelector(
        '#listaQuestoesAtualizarQuestionario'
    );

    const buscaQuestoesAtualizarQuestionario = document.querySelector(
        '#buscaQuestoesAtualizarQuestionario'
    );

    const botaoFiltroAtualizarQuestionario = document.querySelector(
        '#botaoFiltroAtualizarQuestionario'
    );

    const opcoesFiltroAtualizarQuestionario = document.querySelector(
        '#opcoesFiltroAtualizarQuestionario'
    );

    const opcoesEixoAtualizarQuestionario = document.querySelectorAll(
        '.opcaoFiltroAtualizarQuestionario'
    );

    const nomeFiltroAtualizarQuestionario = document.querySelector(
        '#nomeFiltroAtualizarQuestionario'
    );

    const quantidadeSelecionadaAtualizarQuestionario = document.querySelector(
        '.quantidadeSelecionadaAtualizarQuestionario'
    );

    const contadoresEixoAtualizarQuestionario = document.querySelectorAll(
        '.quantidadeEixosAtualizarQuestionario strong[data-eixo]'
    );


    let etapaAtualAtualizarQuestionario = 1;
    let eixoAtualAtualizarQuestionario = 'todos';
    let buscaAtualAtualizarQuestionario = '';
    const limiteQuestoesAtualizarQuestionario = 50;

    function mostrarErroNomeAtualizarQuestionario() {
        erroNomeAtualizarQuestionario.hidden = false;
        nomeAtualizarQuestionario.focus();
    }

    nomeAtualizarQuestionario.addEventListener('input', () => {
        if (nomeAtualizarQuestionario.value.trim() !== '') {
            erroNomeAtualizarQuestionario.hidden = true;
        }
    });


    function normalizarTextoAtualizarQuestionario(texto) {

        return texto
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }


    function escaparHTMLAtualizarQuestionario(texto) {

        const elemento = document.createElement('div');

        elemento.textContent = texto ?? '';

        return elemento.innerHTML;
    }


    function atualizarTelaAtualizarQuestionario() {

        telasAtualizarQuestionario.forEach(tela => {

            const numeroTela = Number(
                tela.dataset.tela
            );

            tela.classList.toggle(
                'telaAtivaAtualizarQuestionario',
                numeroTela === etapaAtualAtualizarQuestionario
            );

        });


        etapasAtualizarQuestionario.forEach(etapa => {

            const numeroEtapa = Number(
                etapa.dataset.tela
            );

            etapa.classList.remove(
                'etapaAtivaAtualizarQuestionario',
                'etapaConcluidaAtualizarQuestionario'
            );

            if (
                numeroEtapa ===
                etapaAtualAtualizarQuestionario
            ) {

                etapa.classList.add(
                    'etapaAtivaAtualizarQuestionario'
                );

            }

            if (
                numeroEtapa <
                etapaAtualAtualizarQuestionario
            ) {

                etapa.classList.add(
                    'etapaConcluidaAtualizarQuestionario'
                );

            }

        });


        if (etapaAtualAtualizarQuestionario === 3) {

            atualizarRevisaoAtualizarQuestionario();

        }

    }


    async function carregarQuestoesAtualizarQuestionario(
        questoesSelecionadas = []
    ) {

        try {

            listaQuestoesAtualizarQuestionario.innerHTML = `
                <p>Carregando questões...</p>
            `;


            const resposta = await fetch(
                '/ParkTec/questao/listar',
                { cache: 'no-store' }
            );


            if (!resposta.ok) {

                throw new Error(
                    'Não foi possível carregar as questões.'
                );

            }


            const questoes = (await resposta.json())
                .filter(questao => Number(questao.ativo) === 1
                    || questoesSelecionadas.includes(Number(questao.id)))
                .sort((a, b) => Number(b.id) - Number(a.id));


            listaQuestoesAtualizarQuestionario.innerHTML = '';


            if (questoes.length === 0) {

                listaQuestoesAtualizarQuestionario.innerHTML = `
                    <p>Nenhuma questão cadastrada.</p>
                `;

                return;

            }


            questoes.forEach(questao => {

                const idEixo = String(questao.id_eixo);
                const nomeEixo = questao.eixos || '';


                const selecionada =
                    questoesSelecionadas.includes(
                        Number(questao.id)
                    );


                listaQuestoesAtualizarQuestionario.insertAdjacentHTML(
                    'beforeend',
                    `
                        <label
                            class="itemQuestaoAtualizarQuestionario"
                            data-id="${questao.id}"
                            data-eixo="${escaparHTMLAtualizarQuestionario(idEixo)}"
                            data-pesquisa="${escaparHTMLAtualizarQuestionario(
                                normalizarTextoAtualizarQuestionario(
                                    questao.pergunta
                                )
                            )}"
                        >

                            <div class="textoQuestaoAtualizarQuestionario">

                                <span>
                                    ${questao.id}
                                </span>

                                <p>
                                    ${escaparHTMLAtualizarQuestionario(
                                        questao.pergunta
                                    )}
                                </p>

                            </div>

                            <div class="tagsEixosQuestaoAtualizarQuestionario">
                                <span class="tagEixoQuestaoAtualizarQuestionario">
                                    ${escaparHTMLAtualizarQuestionario(nomeEixo)}
                                </span>
                                ${Number(questao.ativo) === 0 ? '<span class="tagEixoQuestaoAtualizarQuestionario">Inativa</span>' : ''}
                            </div>

                            <input
                                type="checkbox"
                                value="${questao.id}"
                                data-eixo="${escaparHTMLAtualizarQuestionario(idEixo)}"
                                data-eixos="${escaparHTMLAtualizarQuestionario(nomeEixo)}"
                                class="checkboxQuestaoAtualizarQuestionario"
                                ${selecionada ? 'checked' : ''}
                            >

                        </label>
                    `
                );

            });


            atualizarQuantidadesAtualizarQuestionario();

            filtrarQuestoesAtualizarQuestionario();


        } catch (erro) {

            console.error(erro);

            listaQuestoesAtualizarQuestionario.innerHTML = `
                <p>
                    Não foi possível carregar as questões.
                </p>
            `;

        }

    }


    function atualizarQuantidadesAtualizarQuestionario() {

        const selecionadas =
            document.querySelectorAll(
                '.checkboxQuestaoAtualizarQuestionario:checked'
            );


        const contagem = {};


        selecionadas.forEach(checkbox => {

            const eixo = checkbox.dataset.eixo;
            contagem[eixo] = (contagem[eixo] || 0) + 1;

        });


        listaQuestoesAtualizarQuestionario.querySelectorAll(
            '.checkboxQuestaoAtualizarQuestionario'
        ).forEach(checkbox => {
            checkbox.disabled = !checkbox.checked &&
                selecionadas.length >= limiteQuestoesAtualizarQuestionario;
        });

        quantidadeSelecionadaAtualizarQuestionario.textContent =
            `${selecionadas.length}/${limiteQuestoesAtualizarQuestionario} selecionadas`;


        contadoresEixoAtualizarQuestionario.forEach(contador => {
            contador.textContent = contagem[contador.dataset.eixo] || 0;
        });

    }


    function filtrarQuestoesAtualizarQuestionario() {

        const itens =
            document.querySelectorAll(
                '.itemQuestaoAtualizarQuestionario'
            );


        itens.forEach(item => {

            const mostrar =
                (
                    eixoAtualAtualizarQuestionario === 'todos' ||
                    item.dataset.eixo === eixoAtualAtualizarQuestionario
                ) && (
                    buscaAtualAtualizarQuestionario === '' ||
                    item.dataset.id === buscaAtualAtualizarQuestionario ||
                    (item.dataset.pesquisa || '').includes(
                        buscaAtualAtualizarQuestionario
                    )
                );


            item.style.display =
                mostrar ? 'flex' : 'none';

        });

    }


    async function preencherAtualizarQuestionario(dados) {

        formularioAtualizarQuestionario.dataset.idQuestionario =
            dados.id;


        nomeAtualizarQuestionario.value =
            dados.nome ?? '';


        descricaoAtualizarQuestionario.value =
            dados.descricao ?? '';


        contadorCaracteresAtualizarQuestionario.textContent =
            `${descricaoAtualizarQuestionario.value.length}/500`;

        erroNomeAtualizarQuestionario.hidden = true;


        etapaAtualAtualizarQuestionario = 1;

        eixoAtualAtualizarQuestionario = 'todos';
        buscaAtualAtualizarQuestionario = '';


        nomeFiltroAtualizarQuestionario.textContent =
            'Todos os eixos';

        if (buscaQuestoesAtualizarQuestionario) {

            buscaQuestoesAtualizarQuestionario.value = '';
        }


        await carregarQuestoesAtualizarQuestionario(
            (dados.questoes ?? []).map(Number)
        );


        atualizarTelaAtualizarQuestionario();

    }


    botoesAvancarAtualizarQuestionario.forEach(botao => {

        botao.addEventListener('click', () => {

            if (etapaAtualAtualizarQuestionario === 1) {

                const nome =
                    nomeAtualizarQuestionario.value.trim();


                if (nome === '') {
                    mostrarErroNomeAtualizarQuestionario();
                    return;

                }

            }


            if (etapaAtualAtualizarQuestionario === 2) {

                const selecionadas =
                    document.querySelectorAll(
                        '.checkboxQuestaoAtualizarQuestionario:checked'
                    );


                if (selecionadas.length === 0) {

                    alert(
                        'Selecione pelo menos uma questão.'
                    );

                    return;

                }


            }


            if (etapaAtualAtualizarQuestionario < 3) {

                etapaAtualAtualizarQuestionario++;

                atualizarTelaAtualizarQuestionario();

            }

        });

    });


    botoesVoltarAtualizarQuestionario.forEach(botao => {

        botao.addEventListener('click', event => {

            event.preventDefault();
            event.stopPropagation();


            if (etapaAtualAtualizarQuestionario > 1) {

                etapaAtualAtualizarQuestionario--;

                atualizarTelaAtualizarQuestionario();

            }

        });

    });


    botoesCancelarAtualizarQuestionario.forEach(botao => {

        botao.addEventListener(
            'click',
            () => {

                modalAtualizarQuestionario.close();

                etapaAtualAtualizarQuestionario = 1;

                atualizarTelaAtualizarQuestionario();

            }
        );

    });


    if (botaoAdicionarQuestaoAtualizarQuestionario) {

        botaoAdicionarQuestaoAtualizarQuestionario.addEventListener(
            'click',
            () => {

                if (!modalCriarQuestoes) {

                    return;
                }

                modalCriarQuestoes.dataset.origem =
                    'atualizarQuestionario';

                modalCriarQuestoes.showModal();
            }
        );
    }


    document.addEventListener(
        'questaoCadastradaQuestionario',
        () => {

            const selecionadas = Array.from(
                document.querySelectorAll(
                    '.checkboxQuestaoAtualizarQuestionario:checked'
                )
            ).map(checkbox => Number(checkbox.value));

            carregarQuestoesAtualizarQuestionario(
                selecionadas
            );
        }
    );


    if (descricaoAtualizarQuestionario) {

        descricaoAtualizarQuestionario.addEventListener(
            'input',
            () => {

                contadorCaracteresAtualizarQuestionario.textContent =
                    `${descricaoAtualizarQuestionario.value.length}/500`;

            }
        );

    }


    function atualizarRevisaoAtualizarQuestionario() {

        const selecionadas =
            document.querySelectorAll(
                '.checkboxQuestaoAtualizarQuestionario:checked'
            );


        revisaoNomeAtualizarQuestionario.value =
            nomeAtualizarQuestionario.value.trim();


        revisaoDescricaoAtualizarQuestionario.value =
            descricaoAtualizarQuestionario.value.trim();


        revisaoQuantidadeAtualizarQuestionario.textContent =
            `${selecionadas.length} questões`;

    }

    revisaoNomeAtualizarQuestionario.addEventListener('input', () => {
        nomeAtualizarQuestionario.value = revisaoNomeAtualizarQuestionario.value;
        if (nomeAtualizarQuestionario.value.trim() !== '') {
            erroNomeAtualizarQuestionario.hidden = true;
        }
    });

    revisaoDescricaoAtualizarQuestionario.addEventListener('input', () => {
        descricaoAtualizarQuestionario.value = revisaoDescricaoAtualizarQuestionario.value;
        contadorCaracteresAtualizarQuestionario.textContent =
            `${descricaoAtualizarQuestionario.value.length}/500`;
    });


    if (botaoFiltroAtualizarQuestionario) {

        botaoFiltroAtualizarQuestionario.addEventListener(
            'click',
            event => {

                event.stopPropagation();

                opcoesFiltroAtualizarQuestionario.classList.toggle(
                    'abertoAtualizarQuestionario'
                );

            }
        );

    }


    opcoesEixoAtualizarQuestionario.forEach(opcao => {

        opcao.addEventListener('click', () => {

            eixoAtualAtualizarQuestionario =
                opcao.dataset.eixo;


            nomeFiltroAtualizarQuestionario.textContent =
                opcao.textContent.trim();


            opcoesFiltroAtualizarQuestionario.classList.remove(
                'abertoAtualizarQuestionario'
            );


            filtrarQuestoesAtualizarQuestionario();

        });

    });


    listaQuestoesAtualizarQuestionario.addEventListener(
        'change',
        event => {

            if (
                event.target.classList.contains(
                    'checkboxQuestaoAtualizarQuestionario'
                )
            ) {

                atualizarQuantidadesAtualizarQuestionario();

            }

        }
    );

    buscaQuestoesAtualizarQuestionario?.addEventListener('input', () => {

        buscaAtualAtualizarQuestionario =
            normalizarTextoAtualizarQuestionario(
                buscaQuestoesAtualizarQuestionario.value
            );

        filtrarQuestoesAtualizarQuestionario();
    });


    document.addEventListener('click', async event => {

        const botaoEditar =
            event.target.closest(
                '.botaoEditarQuestionario'
            );


        if (!botaoEditar) {

            return;

        }


        const idQuestionario =
            Number(botaoEditar.dataset.id);


        try {

            const resposta = await fetch(
                `/ParkTec/questionario/buscar?id=${idQuestionario}`
            );


            const dados = await resposta.json();


            if (!resposta.ok) {

                alert(
                    dados.mensagem ||
                    'Não foi possível carregar o questionário.'
                );

                return;

            }


            await preencherAtualizarQuestionario(
                dados
            );


            if (!modalAtualizarQuestionario.open) {

                modalAtualizarQuestionario.showModal();

            }


        } catch (erro) {

            console.error(erro);

            alert(
                'Erro ao carregar o questionário.'
            );

        }

    });


    if (botaoSalvarAtualizarQuestionario) {

        botaoSalvarAtualizarQuestionario.addEventListener(
            'click',
            async () => {

                const idQuestionario =
                    Number(
                        formularioAtualizarQuestionario
                            .dataset.idQuestionario
                    );


                const selecionadas =
                    document.querySelectorAll(
                        '.checkboxQuestaoAtualizarQuestionario:checked'
                    );


                if (idQuestionario <= 0) {

                    alert(
                        'Questionário inválido.'
                    );

                    return;

                }


                if (
                    nomeAtualizarQuestionario
                        .value
                        .trim() === ''
                ) {
                    etapaAtualAtualizarQuestionario = 1;
                    atualizarTelaAtualizarQuestionario();
                    mostrarErroNomeAtualizarQuestionario();
                    return;

                }


                if (selecionadas.length === 0) {

                    alert(
                        'Selecione pelo menos uma questão.'
                    );

                    return;

                }


                const dados = new FormData();


                dados.append(
                    'id',
                    idQuestionario
                );


                dados.append(
                    'nome',
                    nomeAtualizarQuestionario
                        .value
                        .trim()
                );


                dados.append(
                    'descricao',
                    descricaoAtualizarQuestionario
                        .value
                        .trim()
                );


                selecionadas.forEach(checkbox => {

                    dados.append(
                        'questoes[]',
                        checkbox.value
                    );

                });


                try {

                    botaoSalvarAtualizarQuestionario.disabled =
                        true;


                    const resposta = await fetch(
                        '/ParkTec/questionario/atualizar',
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
                            'Não foi possível atualizar o questionário.'
                        );

                        return;

                    }


                    modalAtualizarQuestionario.close();

                    sessionStorage.setItem('mostrarModalSalvo', '1');


                    window.location.reload();


                } catch (erro) {

                    console.error(erro);

                    alert(
                        'Ocorreu um erro ao atualizar o questionário.'
                    );


                } finally {

                    botaoSalvarAtualizarQuestionario.disabled =
                        false;

                }

            }
        );

    }


    document.addEventListener(
        'click',
        event => {

            if (
                botaoFiltroAtualizarQuestionario &&
                opcoesFiltroAtualizarQuestionario &&
                !botaoFiltroAtualizarQuestionario.contains(
                    event.target
                ) &&
                !opcoesFiltroAtualizarQuestionario.contains(
                    event.target
                )
            ) {

                opcoesFiltroAtualizarQuestionario.classList.remove(
                    'abertoAtualizarQuestionario'
                );

            }

        }
    );


    atualizarTelaAtualizarQuestionario();

});

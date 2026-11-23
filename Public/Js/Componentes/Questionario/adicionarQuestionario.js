document.addEventListener('DOMContentLoaded', () => {

    const telasQuestionario = document.querySelectorAll(
        '.telaQuestionario'
    );

    const etapasQuestionario = document.querySelectorAll(
        '.etapaQuestionario'
    );

    const botoesAvancarQuestionario = document.querySelectorAll(
        '.btnAvancarQuestionario'
    );

    const botoesVoltarQuestionario = document.querySelectorAll(
        '.btnVoltarQuestionario'
    );

    const botaoSalvarQuestionario = document.querySelector(
        '.btnSalvarQuestionario'
    );

    const modalNovoQuestionario = document.querySelector(
        '#modalNovoQuestionario'
    );

    const botaoFiltroQuestionario = document.querySelector(
        '#botaoFiltroQuestionario'
    );

    const botaoAdicionarQuestaoQuestionario = document.querySelector(
        '#botaoAdicionarQuestaoQuestionario'
    );

    const modalCriarQuestoes = document.querySelector(
        '#modalCriarQuestoes'
    );

    const opcoesFiltroQuestionario = document.querySelector(
        '#opcoesFiltroQuestionario'
    );

    const opcoesEixoQuestionario = document.querySelectorAll(
        '.opcaoFiltroQuestionario'
    );

    const nomeFiltroQuestionario = document.querySelector(
        '#nomeFiltroQuestionario'
    );

    const listaQuestoesQuestionario = document.querySelector(
        '#listaQuestoesQuestionario'
    );

    const buscaQuestoesQuestionario = document.querySelector(
        '#buscaQuestoesQuestionario'
    );

    const quantidadeSelecionadaQuestionario = document.querySelector(
        '.quantidadeSelecionadaQuestionario'
    );

    const contadoresEixoQuestionario = document.querySelectorAll(
        '.quantidadeEixosQuestionario strong[data-eixo]'
    );

    const nomeQuestionario = document.querySelector(
        '#nomeQuestionario'
    );
    const erroNomeQuestionario = document.querySelector(
        '#erroNomeQuestionario'
    );

    const descricaoQuestionario = document.querySelector(
        '#descricaoQuestionario'
    );

    const contadorCaracteresQuestionario = document.querySelector(
        '.contadorCaracteresQuestionario'
    );

    const revisaoNomeQuestionario = document.querySelector(
        '#revisaoNomeQuestionario'
    );

    const revisaoDescricaoQuestionario = document.querySelector(
        '#revisaoDescricaoQuestionario'
    );

    const revisaoQuantidadeQuestionario = document.querySelector(
        '#revisaoQuantidadeQuestionario'
    );

    let etapaAtualQuestionario = 1;
    let eixoAtualQuestionario = 'todos';
    let buscaAtualQuestionario = '';
    const limiteQuestoesQuestionario = 50;

    function mostrarErroNomeQuestionario() {
        erroNomeQuestionario.hidden = false;
        nomeQuestionario.focus();
    }

    nomeQuestionario.addEventListener('input', () => {
        if (nomeQuestionario.value.trim() !== '') {
            erroNomeQuestionario.hidden = true;
        }
    });


    function normalizarTexto(texto) {

        return texto
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }


    function escaparHTML(texto) {

        const elemento = document.createElement('div');

        elemento.textContent = texto ?? '';

        return elemento.innerHTML;
    }


    function pegarCheckboxesQuestionario() {

        return document.querySelectorAll(
            '.checkboxQuestaoQuestionario'
        );
    }


    function pegarItensQuestionario() {

        return document.querySelectorAll(
            '.itemQuestaoQuestionario'
        );
    }


    let consultaQuestoesQuestionario = 0;

    async function carregarQuestoesQuestionario() {

        const consultaAtual = ++consultaQuestoesQuestionario;

        try {

            const questoesSelecionadas = new Set(
                Array.from(
                    document.querySelectorAll(
                        '.checkboxQuestaoQuestionario:checked'
                    )
                ).map(checkbox => checkbox.value)
            );

            listaQuestoesQuestionario.innerHTML = `
                <p>Carregando questões...</p>
            `;


            const resposta = await fetch(
                '/ParkTec/questao/listar?somenteAtivas=1',
                { cache: 'no-store' }
            );


            if (!resposta.ok) {

                throw new Error(
                    'Não foi possível carregar as questões.'
                );
            }


            const questoes = (await resposta.json())
                .filter(questao => Number(questao.ativo) === 1)
                .sort((a, b) => Number(b.id) - Number(a.id));

            if (consultaAtual !== consultaQuestoesQuestionario) {
                return;
            }


            listaQuestoesQuestionario.innerHTML = '';


            if (questoes.length === 0) {

                listaQuestoesQuestionario.innerHTML = `
                    <p>Nenhuma questão cadastrada.</p>
                `;

                return;
            }


            questoes.forEach(questao => {

                const idEixo = String(questao.id_eixo);
                const nomeEixo = questao.eixos || '';


                listaQuestoesQuestionario.insertAdjacentHTML(
                    'beforeend',
                    `
                        <label
                            class="itemQuestaoQuestionario"
                            data-id="${questao.id}"
                            data-eixo="${escaparHTML(idEixo)}"
                            data-pesquisa="${escaparHTML(
                                normalizarTexto(questao.pergunta)
                            )}"
                        >

                            <div class="textoQuestaoQuestionario">

                                <span>
                                    ${questao.id}
                                </span>

                                <p>
                                    ${escaparHTML(questao.pergunta)}
                                </p>

                            </div>

                            <div class="tagsEixosQuestaoQuestionario">
                                <span class="tagEixoQuestaoQuestionario">
                                    ${escaparHTML(nomeEixo)}
                                </span>
                            </div>

                            <input
                                type="checkbox"
                                name="questoes[]"
                                value="${questao.id}"
                                data-eixo="${escaparHTML(idEixo)}"
                                data-eixos="${escaparHTML(nomeEixo)}"
                                class="checkboxQuestaoQuestionario"
                                ${questoesSelecionadas.has(String(questao.id)) ? 'checked' : ''}
                            >

                        </label>
                    `
                );

            });


            atualizarQuantidadesQuestionario();
            filtrarQuestoesQuestionario();


        } catch (erro) {

            if (consultaAtual !== consultaQuestoesQuestionario) {
                return;
            }

            console.error(erro);

            listaQuestoesQuestionario.innerHTML = `
                <p>
                    Não foi possível carregar as questões.
                </p>
            `;
        }
    }


    function atualizarTelaQuestionario() {

        telasQuestionario.forEach(tela => {

            const numeroTela = Number(
                tela.dataset.tela
            );

            tela.classList.toggle(
                'telaAtivaQuestionario',
                numeroTela === etapaAtualQuestionario
            );
        });


        etapasQuestionario.forEach(etapa => {

            const numeroEtapa = Number(
                etapa.dataset.tela
            );

            etapa.classList.remove(
                'etapaAtivaQuestionario',
                'etapaConcluidaQuestionario'
            );


            if (numeroEtapa === etapaAtualQuestionario) {

                etapa.classList.add(
                    'etapaAtivaQuestionario'
                );
            }


            if (numeroEtapa < etapaAtualQuestionario) {

                etapa.classList.add(
                    'etapaConcluidaQuestionario'
                );
            }
        });


        if (etapaAtualQuestionario === 3) {

            atualizarRevisaoQuestionario();
        }
    }


    botoesAvancarQuestionario.forEach(botao => {

        botao.addEventListener('click', () => {

            if (etapaAtualQuestionario === 1) {

                const nome =
                    nomeQuestionario.value.trim();


                if (nome === '') {
                    mostrarErroNomeQuestionario();
                    return;
                }
            }


            if (etapaAtualQuestionario === 2) {

                const questoesSelecionadas =
                    document.querySelectorAll(
                        '.checkboxQuestaoQuestionario:checked'
                    );


                if (questoesSelecionadas.length === 0) {

                    abrirModal('modalAvisoQuestoesQuestionario');

                    return;
                }
            }


            if (etapaAtualQuestionario < 3) {

                etapaAtualQuestionario++;

                atualizarTelaQuestionario();

                if (etapaAtualQuestionario === 2) {
                    carregarQuestoesQuestionario();
                }
            }
        });
    });


    botoesVoltarQuestionario.forEach(botao => {

        botao.addEventListener('click', () => {

            if (etapaAtualQuestionario > 1) {

                etapaAtualQuestionario--;

                atualizarTelaQuestionario();
            }
        });
    });


    botaoFiltroQuestionario.addEventListener(
        'click',
        event => {

            event.stopPropagation();

            opcoesFiltroQuestionario.classList.toggle(
                'abertoQuestionario'
            );
        }
    );


    if (botaoAdicionarQuestaoQuestionario) {

        botaoAdicionarQuestaoQuestionario.addEventListener(
            'click',
            () => {

                if (!modalCriarQuestoes) {

                    return;
                }

                modalCriarQuestoes.dataset.origem =
                    'questionario';

                modalCriarQuestoes.showModal();
            }
        );
    }


    document.addEventListener(
        'questaoCadastradaQuestionario',
        () => {
            carregarQuestoesQuestionario();
        }
    );


    opcoesEixoQuestionario.forEach(opcao => {

        opcao.addEventListener('click', () => {

            eixoAtualQuestionario =
                opcao.dataset.eixo;


            nomeFiltroQuestionario.textContent =
                opcao.textContent.trim();


            opcoesFiltroQuestionario.classList.remove(
                'abertoQuestionario'
            );


            filtrarQuestoesQuestionario();
        });
    });


    function filtrarQuestoesQuestionario() {

        const itens = pegarItensQuestionario();


        itens.forEach(questao => {

            const mostrar =
                (
                    eixoAtualQuestionario === 'todos' ||
                    questao.dataset.eixo === eixoAtualQuestionario
                ) && (
                    buscaAtualQuestionario === '' ||
                    questao.dataset.id === buscaAtualQuestionario ||
                    (questao.dataset.pesquisa || '').includes(
                        buscaAtualQuestionario
                    )
                );


            questao.style.display =
                mostrar ? 'flex' : 'none';
        });
    }


    function atualizarQuantidadesQuestionario() {

        const selecionadas =
            document.querySelectorAll(
                '.checkboxQuestaoQuestionario:checked'
            );

        const contagem = {};
        selecionadas.forEach(checkbox => {
            const idEixo = checkbox.dataset.eixo;
            contagem[idEixo] = (contagem[idEixo] || 0) + 1;
        });

        contadoresEixoQuestionario.forEach(contador => {
            contador.textContent = contagem[contador.dataset.eixo] || 0;
        });


        pegarCheckboxesQuestionario().forEach(checkbox => {
            checkbox.disabled =
                !checkbox.checked &&
                selecionadas.length >= limiteQuestoesQuestionario;
        });

        quantidadeSelecionadaQuestionario.textContent =
            `${selecionadas.length}/${limiteQuestoesQuestionario} selecionadas`;


    }


    listaQuestoesQuestionario.addEventListener(
        'change',
        event => {

            if (
                event.target.classList.contains(
                    'checkboxQuestaoQuestionario'
                )
            ) {

                atualizarQuantidadesQuestionario();
            }
        }
    );

    buscaQuestoesQuestionario?.addEventListener('input', () => {

        buscaAtualQuestionario = normalizarTexto(
            buscaQuestoesQuestionario.value
        );

        filtrarQuestoesQuestionario();
    });


    descricaoQuestionario.addEventListener(
        'input',
        () => {

            contadorCaracteresQuestionario.textContent =
                `${descricaoQuestionario.value.length}/500`;
        }
    );


    function atualizarRevisaoQuestionario() {

        const selecionadas =
            document.querySelectorAll(
                '.checkboxQuestaoQuestionario:checked'
            );


        revisaoNomeQuestionario.value =
            nomeQuestionario.value.trim();


        revisaoDescricaoQuestionario.value =
            descricaoQuestionario.value.trim();


        revisaoQuantidadeQuestionario.textContent =
            `${selecionadas.length} questões`;
    }

    if (revisaoNomeQuestionario) {

        revisaoNomeQuestionario.addEventListener(
            'input',
            () => {

                nomeQuestionario.value =
                    revisaoNomeQuestionario.value;
            }
        );
    }


    if (revisaoDescricaoQuestionario) {

        revisaoDescricaoQuestionario.addEventListener(
            'input',
            () => {

                descricaoQuestionario.value =
                    revisaoDescricaoQuestionario.value;

                contadorCaracteresQuestionario.textContent =
                    `${descricaoQuestionario.value.length}/500`;
            }
        );
    }


    botaoSalvarQuestionario.addEventListener(
        'click',
        async () => {

            const selecionadas =
                document.querySelectorAll(
                    '.checkboxQuestaoQuestionario:checked'
                );


            if (nomeQuestionario.value.trim() === '') {
                etapaAtualQuestionario = 1;
                atualizarTelaQuestionario();
                mostrarErroNomeQuestionario();
                return;
            }


            if (selecionadas.length === 0) {

                abrirModal('modalAvisoQuestoesQuestionario');

                return;
            }


            const dados = new FormData();


            dados.append(
                'nome',
                nomeQuestionario.value.trim()
            );


            dados.append(
                'descricao',
                descricaoQuestionario.value.trim()
            );


            selecionadas.forEach(checkbox => {

                dados.append(
                    'questoes[]',
                    checkbox.value
                );
            });


            try {

                botaoSalvarQuestionario.disabled = true;


                const resposta = await fetch(
                    '/ParkTec/questionario/adicionar',
                    {
                        method: 'POST',
                        body: dados
                    }
                );


                const resultado = await resposta.json();


                if (!resposta.ok) {

                    alert(
                        resultado.mensagem ||
                        'Não foi possível cadastrar o questionário.'
                    );

                    return;
                }


                const finalizarCadastroQuestionario = () => {

                    modalNovoQuestionario.close();
                    resetarQuestionario();

                    sessionStorage.setItem('mostrarModalSalvo', '1');

                    window.location.reload();
                };

                finalizarCadastroQuestionario();


            } catch (erro) {

                console.error(erro);

                alert(
                    'Ocorreu um erro ao cadastrar o questionário.'
                );


            } finally {

                botaoSalvarQuestionario.disabled = false;
            }
        }
    );


    document.addEventListener(
        'click',
        event => {

            if (
                !botaoFiltroQuestionario.contains(
                    event.target
                ) &&
                !opcoesFiltroQuestionario.contains(
                    event.target
                )
            ) {

                opcoesFiltroQuestionario.classList.remove(
                    'abertoQuestionario'
                );
            }
        }
    );


    function resetarQuestionario() {

        etapaAtualQuestionario = 1;
        eixoAtualQuestionario = 'todos';
        buscaAtualQuestionario = '';


        nomeQuestionario.value = '';
        erroNomeQuestionario.hidden = true;
        descricaoQuestionario.value = '';


        contadorCaracteresQuestionario.textContent =
            '0/500';


        pegarCheckboxesQuestionario().forEach(
            checkbox => {

                checkbox.checked = false;
            }
        );


        nomeFiltroQuestionario.textContent =
            'Todos os eixos';

        if (buscaQuestoesQuestionario) {

            buscaQuestoesQuestionario.value = '';
        }


        atualizarQuantidadesQuestionario();
        filtrarQuestoesQuestionario();
        atualizarTelaQuestionario();
    }

    modalNovoQuestionario?.addEventListener('fechamentoConfirmado', resetarQuestionario);

    document.querySelector('[data-modal="modalNovoQuestionario"]')
        ?.addEventListener('click', carregarQuestoesQuestionario);

    carregarQuestoesQuestionario();

    atualizarTelaQuestionario();

});

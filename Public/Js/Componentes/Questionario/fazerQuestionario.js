document.addEventListener('DOMContentLoaded', () => {

    const modalFazerQuestionario = document.querySelector(
        '#modalFazerQuestionario'
    );

    const tituloFazerQuestionario = document.querySelector(
        '#tituloFazerQuestionario'
    );

    const tituloModalFazerQuestionario = document.querySelector(
        '#modalFazerQuestionario .modalTitulo'
    );

    const descricaoFazerQuestionario = document.querySelector(
        '#descricaoFazerQuestionario'
    );

    const listaFazerQuestionario = document.querySelector(
        '#listaFazerQuestionario'
    );

    const etapaFazerQuestionario = document.querySelector(
        '#etapaFazerQuestionario'
    );

    const botaoCancelarFazerQuestionario = document.querySelector(
        '.btnCancelarFazerQuestionario'
    );

    const botaoVoltarFazerQuestionario = document.querySelector(
        '.btnVoltarFazerQuestionario'
    );

    const botaoAvancarFazerQuestionario = document.querySelector(
        '.btnAvancarFazerQuestionario'
    );

    const botaoSalvarFazerQuestionario = document.querySelector(
        '.btnSalvarFazerQuestionario'
    );


    if (
        !modalFazerQuestionario ||
        !tituloFazerQuestionario ||
        !descricaoFazerQuestionario ||
        !listaFazerQuestionario
    ) {

        return;

    }

    const QUESTOES_POR_ETAPA = 10;

    let questoesFazerQuestionario = [];
    let etapaAtualFazerQuestionario = 1;
    let respostasFazerQuestionario = {};
    let somenteLeituraFazerQuestionario = false;


    function escaparHTMLFazerQuestionario(texto) {

        const elemento = document.createElement('div');

        elemento.textContent = texto || '';

        return elemento.innerHTML;
    }


    function guardarRespostasFazerQuestionario() {

        const respostasMarcadas = document.querySelectorAll(
            '.escalaFazerQuestionario input:checked'
        );

        respostasMarcadas.forEach(input => {

            respostasFazerQuestionario[input.name] =
                input.value;

        });
    }


    function desmarcarRespostaFazerQuestionario(input) {

        input.checked = false;

        delete respostasFazerQuestionario[input.name];
    }


    function encontrarPrimeiraQuestaoSemResposta() {

        return questoesFazerQuestionario.findIndex(questao => {

            const nomeCampo = `resposta_${questao.id}`;

            return !respostasFazerQuestionario[nomeCampo];
        });
    }


    function alertarQuestaoSemResposta(indiceQuestao) {

        etapaAtualFazerQuestionario =
            Math.floor(indiceQuestao / QUESTOES_POR_ETAPA) + 1;

        renderizarEtapaFazerQuestionario();

        const questao = document.querySelector(
            `[data-id-questao="${questoesFazerQuestionario[indiceQuestao].id}"]`
        );

        if (questao) {

            questao.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        alert(
            `Responda a questão ${indiceQuestao + 1} antes de finalizar.`
        );
    }


    function validarRespostasFazerQuestionario() {

        guardarRespostasFazerQuestionario();

        const primeiraSemResposta =
            encontrarPrimeiraQuestaoSemResposta();

        if (primeiraSemResposta === -1) {

            return true;
        }

        alertarQuestaoSemResposta(primeiraSemResposta);

        return false;
    }


    function montarEscalaFazerQuestionario(questao) {

        const nomeCampo = `resposta_${questao.id}`;
        const valores = [
            { valor: 0, legenda: 'Não' },
            { valor: 1, legenda: 'Muito pouco' },
            { valor: 2, legenda: 'Pouco' },
            { valor: 3, legenda: 'Regular' },
            { valor: 4, legenda: 'Bom' }
        ];


        return valores
            .map(opcao => {

                const selecionado =
                    respostasFazerQuestionario[nomeCampo] === String(opcao.valor);


                return `
                    <label class="opcaoEscalaFazerQuestionario">
                        <input
                            type="radio"
                            name="${nomeCampo}"
                            value="${opcao.valor}"
                            ${selecionado ? 'checked' : ''}
                            ${somenteLeituraFazerQuestionario ? 'disabled' : ''}
                        >
                        <span>
                            <strong>${opcao.valor}</strong>
                            <small>${opcao.legenda}</small>
                        </span>
                    </label>
                `;
            })
            .join('');
    }


    function montarQuestaoFazerQuestionario(questao, indice) {

        const eixo =
            questao.eixos ||
            'Sem eixo';


        return `
            <div
                class="itemFazerQuestionario"
                data-id-questao="${questao.id}">

                <div class="cabecalhoQuestaoFazerQuestionario">

                    <span class="numeroQuestaoFazerQuestionario">
                        ${indice + 1}
                    </span>

                    <div class="textoQuestaoFazerQuestionario">

                        <p>
                            ${escaparHTMLFazerQuestionario(questao.pergunta)}
                        </p>

                        <small>
                            ${escaparHTMLFazerQuestionario(eixo)}
                        </small>

                    </div>

                </div>


                <div
                    class="escalaFazerQuestionario"
                    role="radiogroup"
                    aria-label="Resposta de 0 a 4">
                    ${montarEscalaFazerQuestionario(questao)}
                </div>

            </div>
        `;
    }


    function atualizarBotoesFazerQuestionario() {

        const totalEtapas = Math.max(
            1,
            Math.ceil(
                questoesFazerQuestionario.length / QUESTOES_POR_ETAPA
            )
        );

        const inicio =
            ((etapaAtualFazerQuestionario - 1) * QUESTOES_POR_ETAPA) + 1;

        const fim = Math.min(
            etapaAtualFazerQuestionario * QUESTOES_POR_ETAPA,
            questoesFazerQuestionario.length
        );


        if (etapaFazerQuestionario) {

            etapaFazerQuestionario.textContent = questoesFazerQuestionario.length > 0
                ? `Etapa ${etapaAtualFazerQuestionario} de ${totalEtapas} - Perguntas ${inicio}-${fim}`
                : 'Nenhuma pergunta';

        }


        if (botaoVoltarFazerQuestionario) {

            botaoVoltarFazerQuestionario.style.display =
                etapaAtualFazerQuestionario > 1 ? 'inline-flex' : 'none';

        }


        if (botaoAvancarFazerQuestionario) {

            botaoAvancarFazerQuestionario.style.display =
                etapaAtualFazerQuestionario < totalEtapas ? 'inline-flex' : 'none';

        }


        if (botaoSalvarFazerQuestionario) {

            botaoSalvarFazerQuestionario.style.display =
                !somenteLeituraFazerQuestionario &&
                etapaAtualFazerQuestionario === totalEtapas
                    ? 'inline-flex'
                    : 'none';

        }
    }


    function renderizarEtapaFazerQuestionario() {

        const inicio =
            (etapaAtualFazerQuestionario - 1) * QUESTOES_POR_ETAPA;

        const questoesEtapa =
            questoesFazerQuestionario.slice(
                inicio,
                inicio + QUESTOES_POR_ETAPA
            );


        listaFazerQuestionario.innerHTML =
            questoesEtapa
                .map((questao, indice) =>
                    montarQuestaoFazerQuestionario(
                        questao,
                        inicio + indice
                    )
                )
                .join('');

        listaFazerQuestionario.scrollTop = 0;

        atualizarBotoesFazerQuestionario();
    }


    function preencherFazerQuestionario(
        questionario,
        somenteLeitura = false
    ) {

        somenteLeituraFazerQuestionario = somenteLeitura;

        if (botaoCancelarFazerQuestionario) {

            botaoCancelarFazerQuestionario.textContent =
                somenteLeitura
                    ? 'FECHAR'
                    : 'CANCELAR';
        }

        if (tituloModalFazerQuestionario) {

            tituloModalFazerQuestionario.textContent =
                `${questionario.nome || 'Questionário'}${somenteLeitura ? ' - apenas visualização' : ''}`;

        }


        tituloFazerQuestionario.textContent =
            questionario.nome || 'Questionário';


        descricaoFazerQuestionario.textContent =
            questionario.descricao || '';


        questoesFazerQuestionario =
            Array.isArray(questionario.questoes)
                ? questionario.questoes
                : [];

        etapaAtualFazerQuestionario = 1;
        respostasFazerQuestionario = {};


        if (questoesFazerQuestionario.length === 0) {

            listaFazerQuestionario.innerHTML = `
                <p>Nenhuma questão cadastrada neste questionário.</p>
            `;

            atualizarBotoesFazerQuestionario();

            return;
        }


        renderizarEtapaFazerQuestionario();
    }


    function montarQuestoesSelecionadas(seletorCheckbox, seletorItem) {

        return Array.from(
            document.querySelectorAll(seletorCheckbox)
        )
            .filter(checkbox => checkbox.checked)
            .map(checkbox => {

                const item = checkbox.closest(seletorItem);
                const pergunta = item
                    ? item.querySelector('p').textContent.trim()
                    : '';

                return {
                    id: checkbox.value,
                    pergunta,
                    eixos: checkbox.dataset.eixos || ''
                };
            });
    }


    function montarPreviaQuestionario(botaoPrevia) {

        const modalCriacao =
            botaoPrevia.closest('#modalNovoQuestionario');

        const modalAtualizacao =
            botaoPrevia.closest('#modalAtualizarQuestionario');


        if (modalCriacao) {

            const revisaoNome = document.querySelector(
                '#revisaoNomeQuestionario'
            );
            const nome = document.querySelector(
                '#nomeQuestionario'
            );
            const revisaoDescricao = document.querySelector(
                '#revisaoDescricaoQuestionario'
            );
            const descricao = document.querySelector(
                '#descricaoQuestionario'
            );

            return {
                nome: (revisaoNome && revisaoNome.value) ||
                    (nome && nome.value) ||
                    'Questionário',
                descricao: (revisaoDescricao && revisaoDescricao.value) ||
                    (descricao && descricao.value) ||
                    '',
                questoes: montarQuestoesSelecionadas(
                    '.checkboxQuestaoQuestionario',
                    '.itemQuestaoQuestionario'
                )
            };
        }


        if (modalAtualizacao) {

            const revisaoNome = document.querySelector(
                '#revisaoNomeAtualizarQuestionario'
            );
            const nome = document.querySelector(
                '#nomeAtualizarQuestionario'
            );
            const revisaoDescricao = document.querySelector(
                '#revisaoDescricaoAtualizarQuestionario'
            );
            const descricao = document.querySelector(
                '#descricaoAtualizarQuestionario'
            );

            return {
                nome: (revisaoNome && revisaoNome.value) ||
                    (nome && nome.value) ||
                    'Questionário',
                descricao: (revisaoDescricao && revisaoDescricao.value) ||
                    (descricao && descricao.value) ||
                    '',
                questoes: montarQuestoesSelecionadas(
                    '.checkboxQuestaoAtualizarQuestionario',
                    '.itemQuestaoAtualizarQuestionario'
                )
            };
        }


        return null;
    }


    function abrirFazerQuestionario(
        questionario,
        somenteLeitura = false
    ) {

        preencherFazerQuestionario(
            questionario,
            somenteLeitura
        );


        if (!modalFazerQuestionario.open) {

            modalFazerQuestionario.showModal();

        }
    }


    document.addEventListener('click', async event => {

        const opcaoEscala = event.target.closest(
            '.opcaoEscalaFazerQuestionario'
        );

        if (opcaoEscala) {

            if (somenteLeituraFazerQuestionario) {

                event.preventDefault();

                return;
            }

            const input = opcaoEscala.querySelector('input');

            if (
                input &&
                input.dataset.estavaMarcado === '1'
            ) {

                desmarcarRespostaFazerQuestionario(input);

                input.dataset.estavaMarcado = '0';

                event.preventDefault();

                return;
            }
        }

        const botaoPrevia = event.target.closest(
            '.botaoPreviaQuestionario'
        );

        if (botaoPrevia) {

            const previa = montarPreviaQuestionario(
                botaoPrevia
            );

            if (!previa) {

                return;
            }

            abrirFazerQuestionario(previa, true);

            return;
        }


        const botaoVisualizar = event.target.closest(
            '.botaoVisualizarQuestionario'
        );


        if (!botaoVisualizar) {

            return;
        }


        const idQuestionario = Number(
            botaoVisualizar.dataset.id
        );


        if (idQuestionario <= 0) {

            alert('Questionário inválido.');

            return;
        }


        tituloFazerQuestionario.textContent =
            'Carregando questionário...';

        if (tituloModalFazerQuestionario) {

            tituloModalFazerQuestionario.textContent =
                'Questionário';

        }

        descricaoFazerQuestionario.textContent = '';

        listaFazerQuestionario.innerHTML = `
            <p>Carregando questões...</p>
        `;


        try {

            const resposta = await fetch(
                `/ParkTec/questionario/fazer?id=${idQuestionario}`
            );

            const dados = await resposta.json();


            if (!resposta.ok) {

                alert(
                    dados.mensagem ||
                    'Não foi possível carregar o questionário.'
                );

                return;
            }


            abrirFazerQuestionario(dados, true);

        } catch (erro) {

            console.error(erro);

            alert('Erro ao carregar o questionário.');

        }

    });


    document.addEventListener('pointerdown', event => {

        const opcaoEscala = event.target.closest(
            '.opcaoEscalaFazerQuestionario'
        );

        if (!opcaoEscala) {

            return;
        }

        if (somenteLeituraFazerQuestionario) {

            event.preventDefault();

            return;
        }

        const input = opcaoEscala.querySelector('input');

        if (!input) {

            return;
        }

        input.dataset.estavaMarcado =
            input.checked ? '1' : '0';
    });


    if (botaoCancelarFazerQuestionario) {

        botaoCancelarFazerQuestionario.addEventListener('click', () => {

            modalFazerQuestionario.close();

        });

    }


    if (botaoVoltarFazerQuestionario) {

        botaoVoltarFazerQuestionario.addEventListener('click', () => {

            guardarRespostasFazerQuestionario();

            if (etapaAtualFazerQuestionario > 1) {

                etapaAtualFazerQuestionario--;

                renderizarEtapaFazerQuestionario();

            }

        });

    }


    if (botaoAvancarFazerQuestionario) {

        botaoAvancarFazerQuestionario.addEventListener('click', () => {

            const totalEtapas = Math.max(
                1,
                Math.ceil(
                    questoesFazerQuestionario.length / QUESTOES_POR_ETAPA
                )
            );

            guardarRespostasFazerQuestionario();

            if (etapaAtualFazerQuestionario < totalEtapas) {

                etapaAtualFazerQuestionario++;

                renderizarEtapaFazerQuestionario();

            }

        });

    }


    if (botaoSalvarFazerQuestionario) {

        botaoSalvarFazerQuestionario.addEventListener('click', () => {

            if (!validarRespostasFazerQuestionario()) {

                return;
            }

            modalFazerQuestionario.close();

        });

    }

});

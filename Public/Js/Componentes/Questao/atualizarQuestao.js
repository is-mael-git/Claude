document.addEventListener('DOMContentLoaded', () => {

    const modalAtualizarQuestao =
        document.querySelector(
            '#modalAtualizarQuestao'
        );

    const formAtualizarQuestao =
        document.querySelector(
            '.formAtualizarQuestao'
        );

    const idAtualizarQuestao =
        document.querySelector(
            '#idAtualizarQuestao'
        );

    const perguntaAtualizarQuestao =
        document.querySelector(
            '#perguntaAtualizarQuestao'
        );

    const contadorAtualizarQuestao =
        document.querySelector(
            '.contadorAtualizarQuestao'
        );

    const selectAtualizarQuestao =
        document.querySelector(
            '.selectCustomizadoAtualizarQuestao'
        );

    const botaoSelectAtualizarQuestao =
        document.querySelector(
            '.selecionadoSelectAtualizarQuestao'
        );

    const textoSelectAtualizarQuestao =
        document.querySelector(
            '.textoSelectAtualizarQuestao'
        );

    const inputEixoAtualizarQuestao =
        document.querySelector(
            '#idEixoAtualizarQuestao'
        );

    const opcoesAtualizarQuestao =
        document.querySelectorAll(
            '.opcaoSelectAtualizarQuestao'
        );

    const botoesEditarCampoQuestao =
        document.querySelectorAll(
            '#modalAtualizarQuestao .botaoEditarCampoQuestao'
        );

    const botaoCancelarAtualizarQuestao =
        document.querySelector(
            '.btnCancelarAtualizarQuestao'
        );

    const botaoSalvarAtualizarQuestao =
        document.querySelector(
            '.btnSalvarAtualizarQuestao'
        );


    function bloquearCamposAtualizarQuestao() {

        if (perguntaAtualizarQuestao) {

            perguntaAtualizarQuestao.readOnly =
                true;

            perguntaAtualizarQuestao.classList.remove(
                'campoQuestaoEditando'
            );
        }

        if (selectAtualizarQuestao) {

            selectAtualizarQuestao.classList.add(
                'bloqueadoQuestao'
            );
        }

        if (botaoSelectAtualizarQuestao) {

            botaoSelectAtualizarQuestao.disabled =
                false;
        }
    }


    function atualizarContadorQuestao() {

        if (
            perguntaAtualizarQuestao &&
            contadorAtualizarQuestao
        ) {

            contadorAtualizarQuestao.textContent =
                `${perguntaAtualizarQuestao.value.length} / 255`;
        }
    }


    function selecionarEixoQuestao(idEixo, nomeEixo) {

        if (inputEixoAtualizarQuestao) {

            inputEixoAtualizarQuestao.value =
                idEixo ?? '';
        }

        if (textoSelectAtualizarQuestao) {

            textoSelectAtualizarQuestao.textContent =
                nomeEixo || 'Selecione um eixo';
        }

        opcoesAtualizarQuestao.forEach(opcao => {

            opcao.classList.toggle(
                'opcaoAtualQuestao',
                String(opcao.dataset.value) === String(idEixo)
            );
        });
    }


    async function carregarQuestaoParaAtualizar(id) {

        if (!id) {

            return;
        }

        try {

            const resposta =
                await fetch(
                    `/ParkTec/questao/buscar?id=${encodeURIComponent(id)}`
                );

            const questao =
                await resposta.json();

            if (!resposta.ok) {

                alert(
                    questao.mensagem ||
                    'Não foi possível carregar a questão.'
                );

                return;
            }

            if (idAtualizarQuestao) {

                idAtualizarQuestao.value =
                    questao.id;
            }

            if (perguntaAtualizarQuestao) {

                perguntaAtualizarQuestao.value =
                    questao.pergunta || '';
            }

            selecionarEixoQuestao(
                questao.id_eixo,
                questao.eixos
            );

            atualizarContadorQuestao();
            bloquearCamposAtualizarQuestao();

        } catch (erro) {

            console.error(erro);

            alert(
                'Erro de conexão ao carregar a questão.'
            );
        }
    }


    document.addEventListener(
        'click',
        event => {

            const botaoEditar =
                event.target.closest(
                    '.botaoEditarQuestao'
                );

            if (!botaoEditar) {

                return;
            }

            carregarQuestaoParaAtualizar(
                botaoEditar.dataset.id
            );
        }
    );


    if (perguntaAtualizarQuestao) {

        perguntaAtualizarQuestao.addEventListener(
            'input',
            atualizarContadorQuestao
        );
    }


    if (botaoSelectAtualizarQuestao) {

        botaoSelectAtualizarQuestao.addEventListener(
            'click',
            event => {

                event.stopPropagation();

                selectAtualizarQuestao?.classList.remove(
                    'bloqueadoQuestao'
                );

                selectAtualizarQuestao.classList.toggle(
                    'aberto'
                );
            }
        );
    }


    opcoesAtualizarQuestao.forEach(opcao => {

        opcao.addEventListener(
            'click',
            () => {

                selecionarEixoQuestao(
                    opcao.dataset.value,
                    opcao.dataset.nome
                );

                selectAtualizarQuestao?.classList.remove(
                    'aberto'
                );
            }
        );
    });


    botoesEditarCampoQuestao.forEach(botao => {

        botao.addEventListener(
            'click',
            () => {

                const campoEditavel =
                    botao.closest(
                        '.campoEditavelQuestao'
                    );

                const textarea =
                    campoEditavel?.querySelector(
                        'textarea'
                    );

                if (textarea) {

                    textarea.readOnly =
                        false;

                    textarea.classList.add(
                        'campoQuestaoEditando'
                    );

                    textarea.focus();

                    const tamanhoValor =
                        textarea.value.length;

                    textarea.setSelectionRange(
                        tamanhoValor,
                        tamanhoValor
                    );
                }

            }
        );
    });


    if (botaoCancelarAtualizarQuestao) {

        botaoCancelarAtualizarQuestao.addEventListener(
            'click',
            () => {

                modalAtualizarQuestao?.close();
            }
        );
    }


    document.addEventListener(
        'click',
        event => {

            if (
                selectAtualizarQuestao &&
                !selectAtualizarQuestao.contains(
                    event.target
                )
            ) {

                selectAtualizarQuestao.classList.remove(
                    'aberto'
                );
            }
        }
    );


    if (formAtualizarQuestao) {

        formAtualizarQuestao.addEventListener(
            'submit',
            async event => {

                event.preventDefault();

                const pergunta =
                    perguntaAtualizarQuestao
                        ? perguntaAtualizarQuestao.value.trim()
                        : '';

                const idEixo =
                    inputEixoAtualizarQuestao
                        ? inputEixoAtualizarQuestao.value
                        : '';

                if (pergunta === '') {

                    alert(
                        'Digite a pergunta.'
                    );

                    perguntaAtualizarQuestao?.focus();

                    return;
                }

                if (idEixo === '') {

                    alert(
                        'Selecione um eixo válido.'
                    );

                    return;
                }

                const dados =
                    new FormData(formAtualizarQuestao);

                try {

                    if (botaoSalvarAtualizarQuestao) {

                        botaoSalvarAtualizarQuestao.disabled =
                            true;
                    }

                    const resposta =
                        await fetch(
                            formAtualizarQuestao.action,
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
                            'Não foi possível atualizar a questão.'
                        );

                        return;
                    }

                    modalAtualizarQuestao?.close();

                    sessionStorage.setItem('mostrarModalSalvo', '1');
                    window.location.reload();

                } catch (erro) {

                    console.error(erro);

                    alert(
                        'Erro de conexão ao atualizar a questão.'
                    );
                } finally {

                    if (botaoSalvarAtualizarQuestao) {

                        botaoSalvarAtualizarQuestao.disabled =
                            false;
                    }
                }
            }
        );
    }
});

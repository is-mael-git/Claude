<?php abrirModal(
    'modalAtualizarQuestionario',
    'Editar Questionário',
    '',
    '',
    ''
); ?>

<div
    class="formularioAtualizarQuestionario"
    data-id-questionario="">

    <div class="etapasAtualizarQuestionario">

        <div
            class="etapaAtualizarQuestionario etapaAtivaAtualizarQuestionario"
            data-tela="1">
            <span class="numeroEtapaAtualizarQuestionario">1</span>
            <p>Informações</p>
        </div>

        <div class="linhaEtapaAtualizarQuestionario"></div>

        <div
            class="etapaAtualizarQuestionario"
            data-tela="2">
            <span class="numeroEtapaAtualizarQuestionario">2</span>
            <p>Questões</p>
        </div>

        <div class="linhaEtapaAtualizarQuestionario"></div>

        <div
            class="etapaAtualizarQuestionario"
            data-tela="3">
            <span class="numeroEtapaAtualizarQuestionario">3</span>
            <p>Revisão</p>
        </div>

    </div>


    <!-- ========================= -->
    <!-- UPDATE - ETAPA 1 -->
    <!-- ========================= -->

    <div
        class="telaAtualizarQuestionario telaAtivaAtualizarQuestionario"
        data-tela="1">

        <div class="areaFormularioAtualizarQuestionario">

            <div class="campoFormularioAtualizarQuestionario">

                <label for="nomeAtualizarQuestionario">
                    Nome do Questionário
                    <span class="erroNomeAtualizarQuestionario" id="erroNomeAtualizarQuestionario" hidden>Campo obrigatório.</span>
                </label>

                <input
                    type="text"
                    id="nomeAtualizarQuestionario"
                    name="nomeAtualizarQuestionario"
                    placeholder="Nome do questionário"
                    maxlength="100"
                    required>

            </div>


            <div class="campoFormularioAtualizarQuestionario campoDescricaoAtualizarQuestionario">

                <label for="descricaoAtualizarQuestionario">
                    Descrição
                </label>

                <textarea
                    id="descricaoAtualizarQuestionario"
                    name="descricaoAtualizarQuestionario"
                    placeholder="Descrição do questionário..."
                    maxlength="500"></textarea>

                <span class="contadorCaracteresAtualizarQuestionario">
                    0/500
                </span>

            </div>


            <div class="areaBotoesAtualizarQuestionario">

                <button
                    type="button"
                    class="btnCancelarAtualizarQuestionario">
                    CANCELAR
                </button>

                <button
                    type="button"
                    class="btnAvancarAtualizarQuestionario">
                    AVANÇAR
                </button>

            </div>

        </div>

    </div>


    <!-- ========================= -->
    <!-- UPDATE - ETAPA 2 -->
    <!-- ========================= -->

    <div
        class="telaAtualizarQuestionario"
        data-tela="2">

        <div class="areaQuestoesAtualizarQuestionario">

            <div class="cabecalhoQuestoesAtualizarQuestionario">

                <div class="tituloQuestoesAtualizarQuestionario">

                    <h2>Selecione as questões</h2>

                </div>


                <button
                    type="button"
                    class="botaoAdicionarQuestaoQuestionario botaoAdicionarQuestaoAtualizarQuestionario"
                    id="botaoAdicionarQuestaoAtualizarQuestionario">
                    <i class="fa-solid fa-plus"></i>
                    Questão
                </button>

                <input
                    type="search"
                    class="buscaQuestoesAtualizarQuestionario"
                    id="buscaQuestoesAtualizarQuestionario"
                    placeholder="Buscar questão ou ID"
                    aria-label="Buscar questão ou ID">


                <div class="filtroQuestoesAtualizarQuestionario">

                    <button
                        type="button"
                        class="botaoFiltroAtualizarQuestionario"
                        id="botaoFiltroAtualizarQuestionario">

                        <span
                            class="nomeFiltroAtualizarQuestionario"
                            id="nomeFiltroAtualizarQuestionario">
                            Todos os eixos
                        </span>

                        <svg
                            class="iconeFiltroGerenciarQuestao"
                            width="16"
                            height="16"
                            viewBox="0 0 16 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4 6L8 10L12 6"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>

                    </button>


                    <div
                        class="opcoesFiltroAtualizarQuestionario"
                        id="opcoesFiltroAtualizarQuestionario">

                        <button
                            type="button"
                            class="opcaoFiltroAtualizarQuestionario"
                            data-eixo="todos">
                            Todos os eixos
                        </button>

                        <?php foreach (($eixos ?? []) as $eixo): ?>
                            <button
                                type="button"
                                class="opcaoFiltroAtualizarQuestionario"
                                data-eixo="<?= htmlspecialchars((string) $eixo['id_eixo'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($eixo['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </button>
                        <?php endforeach; ?>

                    </div>

                </div>

            </div>


            <div class="resumoSelecaoAtualizarQuestionario">

                <div class="quantidadeEixosAtualizarQuestionario">

                    <?php foreach (($eixos ?? []) as $eixo): ?>
                        <span>
                            <?= htmlspecialchars($eixo['nome'], ENT_QUOTES, 'UTF-8') ?>
                            <strong data-eixo="<?= htmlspecialchars((string) $eixo['id_eixo'], ENT_QUOTES, 'UTF-8') ?>">0</strong>
                        </span>
                    <?php endforeach; ?>

                </div>


                <span class="quantidadeSelecionadaAtualizarQuestionario">
                    0/50 selecionadas
                </span>

            </div>


            <!-- QUESTÕES SERÃO CARREGADAS PELO JS -->

            <div
                class="listaQuestoesAtualizarQuestionario"
                id="listaQuestoesAtualizarQuestionario">
                <p>Carregando questões...</p>
            </div>


            <div class="areaBotoesAtualizarQuestionario">

                <button
                    type="button"
                    class="btnVoltarAtualizarQuestionario btnVoltarFinalAtualizarQuestionario"
                    aria-label="Voltar para informações">
                    <i class="fa-solid fa-angle-left"></i>
                </button>

                <div class="botoesSalvarAtualizarQuestionario">
                    <button type="button" class="btnCancelarAtualizarQuestionario">CANCELAR</button>
                    <button type="button" class="btnAvancarAtualizarQuestionario">AVANÇAR</button>
                </div>

            </div>

        </div>

    </div>


    <!-- ========================= -->
    <!-- UPDATE - ETAPA 3 -->
    <!-- ========================= -->

    <div
        class="telaAtualizarQuestionario"
        data-tela="3">

        <div class="areaRevisaoAtualizarQuestionario">

            <h2>Revise o questionário</h2>


            <div class="itemRevisaoAtualizarQuestionario">

                <label for="revisaoNomeAtualizarQuestionario">Nome</label>

                <div class="campoEditavelRevisaoAtualizarQuestionario">
                    <input type="text" id="revisaoNomeAtualizarQuestionario" class="inputRevisaoAtualizarQuestionario" maxlength="100">
                </div>

            </div>


            <div class="itemRevisaoAtualizarQuestionario">

                <label for="revisaoDescricaoAtualizarQuestionario">Descrição</label>

                <div class="campoEditavelRevisaoAtualizarQuestionario">
                    <textarea id="revisaoDescricaoAtualizarQuestionario" class="inputRevisaoAtualizarQuestionario" maxlength="500"></textarea>
                </div>

            </div>


            <div class="itemRevisaoAtualizarQuestionario">

                <span>Questões selecionadas</span>

                <p id="revisaoQuantidadeAtualizarQuestionario">
                    0 questões
                </p>

            </div>


            <button
                type="button"
                class="botaoPreviaQuestionario">
                <i class="fa-solid fa-arrow-right"></i>
                Prévia do Questionário
            </button>


            <div class="areaBotoesAtualizarQuestionario">

                <button
                    type="button"
                    class="btnVoltarAtualizarQuestionario btnVoltarFinalAtualizarQuestionario"
                    aria-label="Voltar para questões">
                    <i class="fa-solid fa-angle-left"></i>
                </button>

                <div class="botoesSalvarAtualizarQuestionario">
                    <button type="button" class="btnCancelarAtualizarQuestionario">CANCELAR</button>
                    <button type="button" class="btnSalvarAtualizarQuestionario">SALVAR</button>
                </div>

            </div>

        </div>

    </div>

</div>

<?php fecharModal(); ?>

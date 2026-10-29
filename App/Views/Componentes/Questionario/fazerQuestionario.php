<?php abrirModal(
    'modalFazerQuestionario',
    'Questionário',
    '',
    '',
    ''
); ?>

<div class="formularioFazerQuestionario">

    <div class="cabecalhoFazerQuestionario">

        <h2 id="tituloFazerQuestionario">
            Carregando questionário...
        </h2>

        <p id="descricaoFazerQuestionario"></p>

    </div>

    <div
        class="etapaFazerQuestionario"
        id="etapaFazerQuestionario">
        Etapa 1 de 1
    </div>


    <form class="areaFazerQuestionario">

        <div
            class="listaFazerQuestionario"
            id="listaFazerQuestionario">
            <p>Carregando questões...</p>
        </div>


        <div class="areaBotoesFazerQuestionario">

            <button
                type="button"
                class="btnVoltarFazerQuestionario"
                aria-label="Voltar para a etapa anterior">
                <i class="fa-solid fa-angle-left"></i>
            </button>

            <button
                type="button"
                class="btnCancelarFazerQuestionario">
                CANCELAR
            </button>

            <button
                type="button"
                class="btnAvancarFazerQuestionario">
                AVANÇAR
            </button>

            <button
                type="button"
                class="btnSalvarFazerQuestionario">
                FINALIZAR
            </button>

        </div>

    </form>

</div>

<?php fecharModal(); ?>

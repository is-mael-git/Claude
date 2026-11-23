<?php
include_once BASE_MENU;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "Questionario/adicionarQuestionario.php";
require_once BASE_COMPONENTES . "Questionario/atualizarQuestionario.php";
require_once BASE_COMPONENTES . "Questionario/fazerQuestionario.php";
require_once BASE_COMPONENTES . "Questao/adicionarQuestao.php";
require_once BASE_COMPONENTES . "modalInativar.php";
require_once BASE_COMPONENTES . "paginacao.php";
require_once BASE_COMPONENTES . "modalSalvo.php";
modalInativar("inativar");
abrirModal('modalAvisoQuestoesQuestionario', 'Atenção'); ?>
    <p>Atenção o questionário deve conter pelo menos 1 questão.</p>
    <button type="button" class="btnSalvar" onclick="fecharModal('modalAvisoQuestoesQuestionario')">Ok</button>
<?php fecharModal();
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('mostrarModalSalvo') === '1') {
        sessionStorage.removeItem('mostrarModalSalvo');
        window.mostrarModalSalvo();
    }
});
</script>

<div class="menuLateralAreaTrabalho crudParceiro crudQuestionario">

    <button class="botaoAddParceiro abrirModal" data-modal="modalNovoQuestionario">
        <i class="fa-solid fa-plus"></i>
        QUESTIONÁRIO
    </button>

    <div class="containerTabela" id="paginar">

        <table class="tabela tabelaQuestionario">

            <thead class="tabelaCabecalho">

                <tr class="linhaTabelaCabecalho">

                    <th class="itemTabelaCabecalho itemTabelaQuestionario1">
                        Nome do Questionário
                    </th>

                    <th class="itemTabelaCabecalho itemTabelaQuestionario2">
                        Número de Questões
                    </th>

                    <th class="itemTabelaCabecalho itemTabelaQuestionario3">
                        Status
                    </th>

                    <th class="itemTabelaCabecalho itemTabelaQuestionario4">
                        Ações
                    </th>

                </tr>

            </thead>

            <tbody class="tabelaCorpo"></tbody>

        </table>

    </div>

    <?php paginacao() ?>
</div>

<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questao/adicionarQuestao.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questionario/adicionarQuestionario.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questionario/atualizarQuestionario.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questionario/fazerQuestionario.js"></script>

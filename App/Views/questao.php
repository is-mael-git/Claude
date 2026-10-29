<?php
include_once BASE_MENU;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "Questao/adicionarQuestao.php";
require_once BASE_COMPONENTES . "Questao/atualizarQuestao.php";
require_once BASE_COMPONENTES . "modalInativar.php";
require_once BASE_COMPONENTES . "paginacao.php";
require_once BASE_COMPONENTES . "modalSalvo.php";
modalInativar("inativar");
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

    <button class="botaoAddParceiro abrirModal" data-modal="modalCriarQuestoes">
        <i class="fa-solid fa-plus"></i>
        QUESTÃO
    </button>

    <div class="containerTabela" id="paginar">

        <table class="tabela tabelaQuestionario tabelaQuestoes">

            <thead class="tabelaCabecalho">

                <tr class="linhaTabelaCabecalho">

                    <th class="itemTabelaCabecalho itemTabelaQuestionario1">
                        Questão
                    </th>

                    <th class="itemTabelaCabecalho itemTabelaQuestionario2">
                        Eixo Cerne
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

<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questao/adicionarQuestao.js"></script>
<script src="<?= BASE_JS ?>Componentes/Questao/atualizarQuestao.js"></script>

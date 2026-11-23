<?php
include_once BASE_MENU;
require_once BASE_MODAL;
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

    <button class="botaoAddParceiro abrirModal" data-modal="modalCriarMetodologia">
        <i class="fa-solid fa-plus"></i>
        METODOLOGIA
    </button>

    <div class="containerTabela" id="paginar">

        <table class="tabela tabelaQuestionario tabelaQuestoes">

            <thead class="tabelaCabecalho">

                <tr class="linhaTabelaCabecalho">

                    <th class="itemTabelaCabecalho ">
                        Avaliação
                    </th>

                    <th class="itemTabelaCabecalho ">
                        Escala
                    </th>

                    <th class="itemTabelaCabecalho ">
                        Categoria
                    </th>

                    <th class="itemTabelaCabecalho ">
                        Status
                    </th>

                    <th class="itemTabelaCabecalho ">
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
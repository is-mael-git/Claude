<?php

include_once BASE_MENU;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "/adicionarParceiro.php";
require_once BASE_COMPONENTES . "/modalInativar.php";
require_once BASE_COMPONENTES . "/paginacao.php";
modalInativar("inativar");

if (!empty($salvo)) {
    require BASE_COMPONENTES . "/modalSalvo.php";
}
?>

<div class="menuLateralAreaTrabalho crudParceiro">
    <button class="botaoAddParceiro abrirModal" data-modal="modalCadastroParceiro">
        <i class="fa-solid fa-plus"></i>
        PARCEIRO
    </button>
    <div class="containerTabela" id="paginar">
        <table class="tabela tabelaParceiros">
            <thead class="tabelaCabecalho tabelaCabecalhoParceiros">
                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoParceiros">
                    <th class="itemTabelaCabecalho itemTabelaParceiros1">Parceiro</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros2">Área de Atuação</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros3">CNPJ</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros4">E-mail</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros5">Telefone</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros6">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros7">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoParceiros">


            </tbody>
        </table>
    </div>
    <?php paginacao() ?>

    <button class="botaoVerCardParceiro" onclick="window.location.href = '<?= BASE_URL ?>parceiros/cards'"><i class="fa-solid fa-arrow-right"></i> VISUALIZAÇÃO EM CARDS</button>
</div>
<script src="<?= BASE_JS ?>Componentes/mascaras.js"></script>
<script src="<?= BASE_JS ?>Componentes/selectCustomizado.js"></script>
<script src="<?= BASE_JS ?>Componentes/previewImagem.js"></script>
<script src="<?= BASE_JS ?>Componentes/validadorCampos.js"></script>
<script src="<?= BASE_JS ?>crudParceiro.js"></script>
<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
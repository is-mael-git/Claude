<?php
require_once BASE_MODAL;
include_once BASE_MENU;
require_once BASE_COMPONENTES . "/adicionarMentor.php";
require_once BASE_COMPONENTES . "/modalInativar.php";

modalInativar("inativar");

if (!empty($salvo)) {
    require BASE_COMPONENTES . "/modalSalvo.php";
}
?>

<div class="menuLateralAreaTrabalho crudParceiro">
    <?php if (!empty($erro)): ?>
        <p class="mensagemErro" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <button class="abrirModal botaoAddMentor btnDefault" data-modal="modalCadastroMentor"><i class="fa-solid fa-plus"></i> MENTOR</button>

    <div class="containerTabela">
        <table class="tabela tabelaMentores">
            <thead class="tabelaCabecalho tabelaCabecalhoMentores">

                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoMentores">
                    <th class="itemTabelaCabecalho itemTabelaMentores1">Mentor</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores2">Área de Atuação</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores4">E-mail</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores5">Telefone</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores6">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores7">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoMentores">


            </tbody>
        </table>
    </div>
        <div class="juntarPaginacaoPagina">
        <div class="paginacao">
            <div class="primeiro setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.5 3.5 6 8l4.5 4.5"></path>
                    <path d="M5.5 3.5v9"></path>
                </svg></div>
            <div class="anterior setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 3.5 5.5 8l4.5 4.5"></path>
                </svg></div>
            <div class="numeros">
            </div>
            <div class="proximo setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 3.5 10.5 8 6 12.5"></path>
                </svg></div>
            <div class="ultimo setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5.5 3.5 10 8l-4.5 4.5"></path>
                    <path d="M10.5 3.5v9"></path>
                </svg></div>
        </div>
        <div class="wrapperQuantidadePagina">
            <select name="quantidadePagina" id="quantidadePagina"></select>
        </div>
    </div> 


</div>

<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
<script src="<?= BASE_JS ?>Componentes/mascaras.js"></script>
<script src="<?= BASE_JS ?>Componentes/selectCustomizado.js"></script>
<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
<script src="<?= BASE_JS ?>crudMentores.js"></script>
<script src="<?= BASE_JS ?>Componentes/validadorCampos.js"></script>

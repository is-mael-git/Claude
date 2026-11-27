<?php
require_once BASE_MODAL;
include_once BASE_MENU;
require_once BASE_COMPONENTES . "/adicionarMentor.php";
require_once BASE_COMPONENTES . "/modalInativar.php";
require_once BASE_COMPONENTES . "/visualizarMentor.php";
require_once BASE_COMPONENTES . "/paginacao.php";
require_once BASE_COMPONENTES . "/modalSalvo.php";

modalInativar("inativar");
?>

<?php if (!empty($salvo)): ?>
<script>
document.addEventListener('DOMContentLoaded', () => window.mostrarModalSalvo?.());
</script>
<?php endif; ?>

<div class="menuLateralAreaTrabalho crudParceiro">
    <?php if (!empty($erro)): ?>
        <p class="mensagemErro" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <button class="abrirModal botaoAddMentor btnDefault" data-modal="modalCadastroMentor"><i class="fa-solid fa-plus"></i> MENTOR</button>

    <div class="containerTabela" id="paginar">
        <table class="tabela tabelaMentores">
            <thead class="tabelaCabecalho tabelaCabecalhoMentores">

                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoMentores">
                    <th class="itemTabelaCabecalho itemTabelaMentores1">Mentor</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores2">Área de Atuação</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores3">Telefone</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores4">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaMentores5">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoMentores">
            </tbody>
        </table>
    </div>
    <?php paginacao() ?>

</div>

<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
<script src="<?= BASE_JS ?>Componentes/mascaras.js"></script>
<script src="<?= BASE_JS ?>Componentes/selectCustomizado.js"></script>
<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
<script src="<?= BASE_JS ?>Componentes/visualizarMentor.js"></script>
<script src="<?= BASE_JS ?>crudMentores.js"></script>
<script src="<?= BASE_JS ?>Componentes/validadorCampos.js"></script>

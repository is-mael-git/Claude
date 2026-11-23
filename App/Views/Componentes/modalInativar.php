
<?php
function modalInativar(string $textoConfirmacao, string $textoBotaoNegacao = "Cancelar", string $textoBotaoConfirmacao = "Salvar"){
abrirModal("modalInativar", "" );?>
            <p>Tem certeza que <br> deseja <span class="textoConfirmacao"><?= htmlspecialchars($textoConfirmacao, ENT_QUOTES, 'UTF-8') ?></span>?</p>
            <span>
                <button type="button" class="btnCancelar"><?= htmlspecialchars($textoBotaoNegacao, ENT_QUOTES, 'UTF-8') ?></button>
                <button type="button" class="btnSalvar"><?= htmlspecialchars($textoBotaoConfirmacao, ENT_QUOTES, 'UTF-8') ?></button>
            </span>


<?php fecharModal();
}
?>
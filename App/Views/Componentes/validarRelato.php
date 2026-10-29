<?php abrirModal('validarRelato', 'Validar Relato', 'fa-solid fa-file-circle-check', "", "", true); ?>

<div
    class="areaValidarRelato"
    data-endpoint="<?= htmlspecialchars(BASE_URL . 'relato/buscar', ENT_QUOTES, 'UTF-8') ?>"
    data-comentario-endpoint="<?= htmlspecialchars(BASE_URL . 'relato/comentario', ENT_QUOTES, 'UTF-8') ?>"
    data-icone-avaliacao="<?= htmlspecialchars(BASE_ASSETS . 'Icones/Avaliacao/', ENT_QUOTES, 'UTF-8') ?>"
>
    <div class="validarRelatoCabecalho">
        <div class="validarRelatoAbas" role="group" aria-label="Tipo de avaliação">
            <button type="button" class="btnDefault validarRelatoAba" data-emissor="startup">Startup</button>
            <button type="button" class="btnDefault validarRelatoAba" data-emissor="mentor">Mentor</button>
        </div>

        <button class="btnDefault" type="button">
            <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
            DOWNLOAD
        </button>
    </div>

    <article class="validarRelatoRelatorio" aria-labelledby="validarRelatoTitulo">
        <img
            class="validarRelatoLogo"
            src="<?= BASE_IMGSIS ?>logo8.png"
            alt="ParkTec CG"
        >

        <header class="validarRelatoIdentificacao">
            <h2 id="validarRelatoTitulo">Relato da reunião</h2>
            <p id="validarRelatoDetalhes" aria-live="polite">Selecione um relato para visualizar as perguntas e respostas.</p>
        </header>

        <p id="validarRelatoEstado" role="status" aria-live="polite"></p>
        <div id="validarRelatoConteudo"></div>
    </article>

    <div class="validarRelatoObservacao">
        <label for="validarRelatoObservacao">Adicionar Observação:</label>
        <textarea
            id="validarRelatoObservacao"
            name="observacao"
            placeholder="Descreva a sua avaliação sobre a reunião realizada pelo Mentor!"
            maxlength="500"
            aria-describedby="validarRelatoContador"
        ></textarea>
        <output id="validarRelatoContador" for="validarRelatoObservacao">0/500 caracteres</output>
    </div>

    <footer class="validarRelatoAcoes">
        <button class="btnCancelar" onclick="fecharModal('validarRelato')"  type="button" data-modal="validarRelato">
            CANCELAR
        </button>
        <button class="btnSalvar" type="button">
            SALVAR
        </button>
    </footer>
</div>

<?php fecharModal(true); ?>

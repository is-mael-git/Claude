<?php
$eixosAtualizarQuestao =
    $eixos ?? [];

abrirModal(
    'modalAtualizarQuestao',
    'Editar Questão',
    '',
    '',
    ''
);
?>


<div class="crudQuestao">

    <div class="blocoQuestao">

        <form
            class="formAtualizarQuestao"
            method="POST"
            action="/ParkTec/questao/atualizar"
        >

            <input
                type="hidden"
                name="id"
                id="idAtualizarQuestao"
            >

            <div class="campoQuestao">

                <label for="perguntaAtualizarQuestao">
                    Pergunta
                </label>

                <div class="campoEditavelQuestao">

                    <textarea
                        id="perguntaAtualizarQuestao"
                        name="pergunta"
                        maxlength="255"
                        placeholder="Digite a pergunta..."
                        rows="4"
                        required
                        readonly
                    ></textarea>

                    <button
                        type="button"
                        class="botaoEditarCampoQuestao"
                        aria-label="Editar pergunta">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill="#2F3C54"
                                d="M19.424 4.576a2 2 0 0 0-2.828 0l-9.9 9.9a2 2 0 0 0-.526.92l-.92 4.043a1 1 0 0 0 1.205 1.205l4.043-.92a2 2 0 0 0 .92-.526l9.9-9.9a2 2 0 0 0 0-2.828l-2.894-2.894ZM15.182 6l2.818 2.818-1.061 1.06-2.818-2.817L15.182 6Zm-2.475 2.475 2.818 2.818-5.334 5.334-2.273.517.518-2.273 4.271-4.396Z"
                            />
                        </svg>
                    </button>

                    <span class="contadorAtualizarQuestao">
                        0 / 255
                    </span>

                </div>

            </div>


            <div class="campoQuestao">

                <label>
                    Eixo
                </label>


                <div class="campoEditavelQuestao">

                    <div class="selectCustomizadoAtualizarQuestao bloqueadoQuestao">

                        <button
                            type="button"
                            class="selecionadoSelectAtualizarQuestao"
                        >

                            <span class="textoSelectAtualizarQuestao">
                                Selecione um eixo
                            </span>

                            <span class="setaSelectQuestao" aria-hidden="true"></span>

                        </button>


                        <div class="opcoesSelectAtualizarQuestao">

                            <?php foreach ($eixosAtualizarQuestao as $eixoAtualizarQuestao): ?>

                                <div
                                    class="opcaoSelectAtualizarQuestao"
                                    data-value="<?= htmlspecialchars((string) $eixoAtualizarQuestao['id_eixo'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-nome="<?= htmlspecialchars($eixoAtualizarQuestao['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <span><?= htmlspecialchars($eixoAtualizarQuestao['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="competenciasDoEixoQuestao"> — <?= htmlspecialchars($eixoAtualizarQuestao['competencias'] ?: 'Nenhuma competência cadastrada', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                            <?php endforeach; ?>

                        </div>


                        <input
                            type="hidden"
                            name="id_eixo"
                            id="idEixoAtualizarQuestao"
                        >

                    </div>

                </div>

            </div>


            <div class="acoesFormQuestao">

                <button
                    type="button"
                    class="btnCancelarAtualizarQuestao"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="btnSalvarAtualizarQuestao"
                >
                    Salvar
                </button>

            </div>

        </form>

    </div>

</div>


<?php fecharModal(); ?>

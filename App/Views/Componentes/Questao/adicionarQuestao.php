<?php
$categoriasQuestao =
    $categorias ?? [];

abrirModal(
    'modalCriarQuestoes',
    'Criar Questão',
    '',
    '',
    ''
);
?>


<div class="crudQuestao">

    <div class="blocoQuestao">

        <form
            class="formQuestao"
            method="POST"
            action="/ParkTec/questao/adicionar"
            novalidate
        >

            <!-- PERGUNTA -->

            <div class="campoQuestao">

                <label for="perguntaQuestao">
                    Pergunta
                    <span class="erroCampoQuestao" id="erroPerguntaQuestao" hidden>Campo obrigatório.</span>
                </label>

                <textarea
                    id="perguntaQuestao"
                    name="pergunta"
                    maxlength="255"
                    placeholder="Digite a pergunta..."
                    rows="4"
                    required
                ></textarea>

                <span class="contadorQuestao">
                    0 / 255
                </span>

            </div>


            <!-- EIXO -->

            <div class="campoQuestao">

                <label>
                    Categoria
                    <span class="erroCampoQuestao" id="erroCategoriaQuestao" hidden>Campo obrigatório.</span>
                </label>


                <div class="selectCustomizadoQuestao">

                    <!-- SELECT VISÍVEL -->

                    <button
                        type="button"
                        class="selecionadoSelectQuestao"
                    >

                        <span class="textoSelectQuestao">
                            Selecione uma categoria
                        </span>

                        <span class="setaSelectQuestao" aria-hidden="true"></span>

                    </button>


                    <!-- OPÇÕES -->

                    <div class="opcoesSelectQuestao">

                        <?php foreach ($categoriasQuestao as $categoriaQuestao): ?>

                            <div
                                class="opcaoSelectQuestao"
                                data-value="<?= htmlspecialchars((string) $categoriaQuestao['id_categoria'], ENT_QUOTES, 'UTF-8') ?>"
                                data-nome="<?= htmlspecialchars($categoriaQuestao['nome'], ENT_QUOTES, 'UTF-8') ?>"
                            >
                                <span><?= htmlspecialchars($categoriaQuestao['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="competenciasDoEixoQuestao"> — <?= htmlspecialchars($categoriaQuestao['subcategorias'] ?: 'Nenhuma subcategoria cadastrada', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>

                        <?php endforeach; ?>

                    </div>


                    <!-- VALOR QUE VAI PARA O PHP -->

                    <input
                        type="hidden"
                        id="idCategoriaQuestao"
                    >

                </div>

            </div>


            <div class="campoQuestao">
                <label for="idSubcategoriaQuestao">Subcategoria</label>
                <select id="idSubcategoriaQuestao" name="id_subcategoria" required disabled>
                    <option value="">Selecione uma categoria primeiro</option>
                    <?php foreach (($subcategorias ?? []) as $subcategoria): ?>
                        <option value="<?= htmlspecialchars((string) $subcategoria['id_subcategoria'], ENT_QUOTES, 'UTF-8') ?>"
                                data-categoria="<?= htmlspecialchars((string) $subcategoria['id_categoria'], ENT_QUOTES, 'UTF-8') ?>" hidden disabled>
                            <?= htmlspecialchars($subcategoria['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- BOTÕES -->

            <div class="acoesFormQuestao">

                <button
                    type="button"
                    class="btnCancelarQuestao"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="btnSalvarQuestao"
                >
                    Salvar
                </button>

            </div>

        </form>

    </div>

</div>


<?php fecharModal(); ?>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const selectQuestao = document.querySelector(
        '.selectCustomizadoQuestao'
    );

    const botaoSelectQuestao = document.querySelector(
        '.selecionadoSelectQuestao'
    );

    const textoSelectQuestao = document.querySelector(
        '.textoSelectQuestao'
    );

    const opcoesQuestao = document.querySelectorAll(
        '.opcaoSelectQuestao'
    );

    const inputCategoria = document.querySelector(
        '#idCategoriaQuestao'
    );


    /* ABRIR / FECHAR SELECT */

    botaoSelectQuestao.addEventListener('click', function () {

        selectQuestao.classList.toggle('aberto');

    });


    /* SELECIONAR EIXO */

    opcoesQuestao.forEach(function (opcao) {

        opcao.addEventListener('click', function () {

            textoSelectQuestao.textContent =
                opcao.dataset.nome;


            inputCategoria.value =
                opcao.dataset.value;
            inputCategoria.dispatchEvent(new Event('change'));


            opcoesQuestao.forEach(function (item) {

                item.classList.remove(
                    'opcaoAtualQuestao'
                );

            });


            opcao.classList.add(
                'opcaoAtualQuestao'
            );


            selectQuestao.classList.remove(
                'aberto'
            );

        });

    });


    /* FECHAR AO CLICAR FORA */

    document.addEventListener('click', function (event) {

        if (!selectQuestao.contains(event.target)) {

            selectQuestao.classList.remove(
                'aberto'
            );

        }

    });


    /* CONTADOR DA PERGUNTA */

    const perguntaQuestao = document.querySelector(
        '#perguntaQuestao'
    );

    const contadorQuestao = document.querySelector(
        '.contadorQuestao'
    );


    perguntaQuestao.addEventListener('input', function () {

        contadorQuestao.textContent =
            perguntaQuestao.value.length + ' / 255';

    });

});

</script>

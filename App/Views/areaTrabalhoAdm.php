<?php
include_once BASE_MENU;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "modalAdm.php";
require_once BASE_COMPONENTES . "modalMatch.php";
require_once BASE_COMPONENTES . "modalInativar.php";
require_once BASE_COMPONENTES . "validarRelato.php";
require_once BASE_COMPONENTES . "modalSalvo.php";

modalInativar("enviar sem observação", "Cancelar", "Enviar");

use Core\auth;
?>



<div class="menuLateralAreaTrabalho areaTrabalhoAdm">
    <div class="visualizacoesMiniDest">

        <!-- STARTUPS -->
        <div class="cardIndicador">
            <div class="iconeCard Startups">
                <i class="fa-solid fa-rocket"></i>
            </div>

            <div class="informacaoCardAdm">
                <strong id="quantidadeStartups">0</strong>
                <span>Startups Ativas</span>
            </div>
        </div>


        <!-- MENTORES -->
        <div class="cardIndicador">
            <div class="iconeCard Mentores">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="informacaoCardAdm">
                <strong id="quantidadeMentores">0</strong>
                <span>Mentores Ativos</span>
            </div>
        </div>


        <!-- REUNIÕES -->
        <div class="cardIndicador">
            <div class="iconeCard Reunioes">
                <i class="fa-solid fa-calendar-check"></i>
            </div>

            <div class="informacaoCardAdm">
                <strong id="quantidadeReuniao">3</strong>
                <span>Reuniões Este Mês</span>
            </div>
        </div>


        <!-- QUESTIONÁRIOS -->
        <div class="cardIndicador">
            <div class="iconeCard Questionarios">
                <i class="fa-solid fa-clipboard-list"></i>

            </div>

            <div class="informacaoCardAdm">
                <strong id="quantidadeQuestionario">0</strong>
                <span>Questionários Ativos</span>
            </div>
        </div>


    </div>

    <div class="caixaMaiorAdm">

        <div class="containerTabela">


            <table class="admDecisoesTabela">

                <thead class="tabelaCabecalho ">
                    <tr class="linhaTabelaCabecalho ">
                        <th class="itemTabelaCabecalho Adm1">Data</th>
                        <th class="itemTabelaCabecalho Adm2">Tipo</th>
                        <th class="itemTabelaCabecalho Adm3">Registro</th>
                        <th class="itemTabelaCabecalho Adm4">Situação</th>
                        <th class="itemTabelaCabecalho Adm5">Ação</th>
                    </tr>
                </thead>


                <tbody class="tabelaCorpo ">

                    <!-- MENTOR -->

                    <tr class="linhaTabelaCorpo">

                        <td class="itemTabelaCorpo Adm1">30/08/2026</td>

                        <td class="itemTabelaCorpo Adm2">
                            <span class="admDecisaoTipo">
                                <i class="fa-solid fa-user"></i>
                                Mentor
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm3">Carlos Silva</td>

                        <td class="itemTabelaCorpo Adm4">
                            <span class="admDecisaoSituacao admSituacaoPendente">
                                <i class="fa-solid fa-circle "></i>
                                Cadastro pendente
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm5">
                            <button
                                type="button"
                                class="admDecisaoBotao abrirModal"
                                data-modal="modalAdm"
                                title="Validar mentor">
                                <i class="fa-solid fa-user-check"></i>
                            </button>
                        </td>

                    </tr>

                    <!-- RELATO -->

                    <tr class="linhaTabelaCorpo">

                        <td class="itemTabelaCorpo Adm1">29/08/2026</td>

                        <td class="itemTabelaCorpo Adm2">
                            <span class="admDecisaoTipo">
                                <i class="fa-solid fa-file-lines"></i>
                                Reunião
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm3">Nu Pagamentos × Deyvid</td>

                        <td class="itemTabelaCorpo Adm4">
                            <span class="admDecisaoSituacao admSituacaoPendente">
                                <i class="fa-solid fa-circle "></i>
                                Relato pendente
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm5">
                            <button
                                type="button"
                                class="admDecisaoBotao abrirModal"
                                data-modal="validarRelato"
                                data-relato-id="1"
                                title="Validar relato">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </button>
                        </td>

                    </tr>


                    <!-- ATRASO -->

                    <tr class="linhaTabelaCorpo">

                        <td class="itemTabelaCorpo Adm1">28/08/2026</td>

                        <td class="itemTabelaCorpo Adm2">
                            <span class="admDecisaoTipo">
                                <i class="fa-solid fa-handshake"></i>
                                Mentoria
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm3">Startup Alpha × Carlos</td>

                        <td class="itemTabelaCorpo Adm4">
                            <span class="admDecisaoSituacao admSituacaoAtraso">
                                <i class="fa-solid fa-circle-exclamation "></i>
                                Aguardando aprovação
                            </span>
                        </td>

                        <td class="itemTabelaCorpo Adm5">
                            <button
                                type="button"
                                class="admDecisaoBotao abrirModal"
                                data-modal="modalMatch"
                                data-mentoria-id="<?= (int) ($idMentoriaDemo ?? 0) ?>"
                                title="Ver Mentoria">
                                <i class="fa-solid fa-clock"></i>
                            </button>
                        </td>

                    </tr>
                </tbody>

            </table>





        </div>


        <!-- CALENDARIO ADM  -->
        <div class="calendarioAdm">
            <div class="agenda">
                <div class="calendario">
                    <div class="evento-text">Eventos</div>
                    <div class="navegacao">
                        <span id="anterior"><i class="fa-solid fa-angle-left"></i></span>
                        <h2 id="mesAno"></h2>
                        <span id="proximo"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
                <div id="calendario"></div>

                <script src="<?php echo BASE_URL ?>/Public/Js/calendarioAdm.js"></script>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_JS ?>quantidadeQtnAdm.js"></script>
<script src="<?= BASE_JS ?>validarRelato.js"></script>
<script src="<?= BASE_JS ?>Componentes/modalMatch.js"></script>
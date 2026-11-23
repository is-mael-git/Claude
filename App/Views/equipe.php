<?php

include_once BASE_MENU;

include_once BASE_MODAL;

if (!empty($salvo)) {
    require BASE_COMPONENTES . '/modalSalvo.php';
}

require_once BASE_COMPONENTES .'/adicionarEquipe.php';
require_once BASE_COMPONENTES .'/modalInativar.php';
modalInativar('inativar');

?>

<div id="overlay" class="equipeOverlay" aria-hidden="true" data-save-url="<?= htmlspecialchars(BASE_URL . 'equipe/perfil/cadastrar', ENT_QUOTES, 'UTF-8') ?>">

    <div class="equipeModalPerfilStaff">
        <div class="equipeTitleStaff">
            CADASTRAR PERFIL
            <button class="equipeCloseBtnStaff" type="button" onclick="closeModalNovoperfil()" aria-label="Fechar modal de perfil">
                ✕
            </button>
        </div>

        <div class="equipeModalHeaderStaff">

            <div class="equipeModalStaff">

                <div class="equipeTopRow">
                    <div class="equipeField">
                        <label for="profileName">Nome do perfil</label>
                        <input type="text" id="profileName" placeholder="Digite o nome do perfil">
                    </div>
                </div>

                <h2 class="equipeTitlePrimary">Permissões de acesso</h2>

                <div class="equipePermissionBox">

                <label class="equipeSelectAll equipeCheckbox container">
                        <input type="checkbox" id="selectAll"><span class="equipeCheckmark checkmark"></span>
                        Selecionar todos
                    </label>

                    <hr>

                    <div class="equipeSection">

                        <h3>Visualizadores</h3>
                        <div class="equipeGridPermissoes">
                            <div class="visualizarIndicadores">
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="dashboard_adm"><span class="equipeCheckmark checkmark"></span> Visualizar dashboard ADM</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="dashboard_startup"><span class="equipeCheckmark checkmark"></span> Visualizar dashboard startup</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="dashboard_mentores"><span class="equipeCheckmark checkmark"></span> Visualizar dashboard mentores</label>
                            </div>
                            <div class="visualizarIndicadores">
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="area_startup"><span class="equipeCheckmark checkmark"></span> Visualizar área da startup</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="area_mentores"><span class="equipeCheckmark checkmark"></span> Visualizar área mentores</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="dashboard_adm"><span class="equipeCheckmark checkmark"></span> Visualizar dashboard ADM</label>
                            </div>
                            <div class="visualizarIndicadores">
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="relatos"><span class="equipeCheckmark checkmark"></span> Visualizar relatos</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="area_parceiros"><span class="equipeCheckmark checkmark"></span> Visualizar área parceiros</label><br>
                                <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="area_staff"><span class="equipeCheckmark checkmark"></span> Visualizar área staff</label>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Gerenciadores</h3>
                        <div class="equipeGridPermissoes">
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="gerenciar_documentos"><span class="equipeCheckmark checkmark"></span> Gerenciar documentos</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="gerenciar_parceiros"><span class="equipeCheckmark checkmark"></span> Gerenciar parceiros</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="gerenciar_questionarios"><span class="equipeCheckmark checkmark"></span> Gerenciar questionários</label>
                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Validadores</h3>
                        <div class="equipeGridPermissoes">
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="validar_juncao_startup_mentor"><span class="equipeCheckmark checkmark"></span> Validar junção startup x mentor</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="validar_reunioes"><span class="equipeCheckmark checkmark"></span> Validar reuniões</label>
                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Operações</h3>
                        <div class="equipeGridPermissoes">
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="relatar_reunioes"><span class="equipeCheckmark checkmark"></span> Relatar reuniões</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="agendar_reunioes"><span class="equipeCheckmark checkmark"></span> Agendar reuniões</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="cadastrar_perfis"><span class="equipeCheckmark checkmark"></span> Cadastrar perfis</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="realizar_questionario"><span class="equipeCheckmark checkmark"></span> Realizar questionário</label>
                            <label class="equipeCheckbox container"><input type="checkbox" class="equipePermission" value="receber_notificacoes"><span class="equipeCheckmark checkmark"></span> Receber notificações</label>
                        </div>

                    </div>

                    <div class="equipeFooterButtons">
                        <button class="equipeCancelBtn" type="button" onclick="closeModalNovoperfil()">CANCELAR</button>
                        <button class="btnSalvar equipeSaveBtn" type="button">SALVAR</button>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>



<div class="menuLateralAreaTrabalho equipeAreaTrabalhoCrud">

<div class="btnCadastrarEquipe">
    <button class="btnDefault abrirModal abrirModalEquipe" data-modal="cadastroEquipe">
       <i class="fa-solid fa-plus"></i> EQUIPE
    </button>
</div>
    

     <div class="containerTabela" id="paginar">
        <table class="tabela tabelaEquipe">
            <thead class="tabelaCabecalho tabelaCabecalhoEquipe">
                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoEquipe">
                    <th class="itemTabelaCabecalho itemTabelaEquipe1">Nome</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe2">Email</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe3">Telefone</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe4">CPF</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe5">Cargo</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe6">Perfil de Acesso</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipeStatus">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaEquipe7">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoEquipe">
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
            <div class="numeros"></div>
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



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="<?= BASE_JS ?>novoperfil.js"></script>
<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
</div>

<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>

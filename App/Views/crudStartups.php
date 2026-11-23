<?php

include_once BASE_MENU;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "modalInativar.php";
require_once BASE_COMPONENTES . "modalSalvo.php";
require_once BASE_COMPONENTES . "paginacao.php";
modalInativar("inativar");

?>

<?php // Modal único de cadastro/edição de Startup em três etapas. ?>
<?php
abrirModal(
    'modalCadastroStartup',
    'Cadastrar Startups',
    '',
    '<svg width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <circle cx="12" cy="7" r="4" fill="white" />
        <path d="M4 20c0-4.4 3.6-8 8-8s8 3.6 8 8" fill="white" />
    </svg>'
);
?>

    <div class="areaCadastroStartup">

        <nav class="cs-stepper" aria-label="Etapas do cadastro">
            <div class="cs-step" data-etapa="1">
                <span class="cs-stepCircle">1</span>
                <span class="cs-stepLabel">Dados da Startup</span>
            </div>
            <div class="cs-stepLine"></div>
            <div class="cs-step" data-etapa="2">
                <span class="cs-stepCircle">2</span>
                <span class="cs-stepLabel">Localização e Programa</span>
            </div>
            <div class="cs-stepLine"></div>
            <div class="cs-step" data-etapa="3">
                <span class="cs-stepCircle">3</span>
                <span class="cs-stepLabel">Fundadores</span>
            </div>
        </nav>

        <form id="startupForm" enctype="multipart/form-data">
            <div class="cs-columns">

                <!-- ETAPA 1 — Dados da Startup -->
                <section class="cs-card" data-etapa="1" aria-labelledby="col1-title">
                    <div class="cs-cardTitle">
                        <svg width="26" height="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="7" r="4" fill="#2F3C54" />
                            <path d="M4 20c0-4.4 3.6-8 8-8s8 3.6 8 8" fill="#2F3C54" />
                        </svg>
                        <h2 id="col1-title">Dados da Startup</h2>
                    </div>
                    <hr class="cs-cardDivider" />

                    <div class="cs-photoRow">
                        <div class="cs-photoUpload">
                            <input type="file" id="startup-photo" name="foto_startup" class="cs-fileInput" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" required>
                            <label for="startup-photo" class="cs-avatarCircle" aria-label="Adicionar foto da startup">
                                <img id="previewFotoStartup" src="<?= BASE_ASSETS ?>Icones/Vector.svg" alt="Prévia da foto da startup">
                                <span class="cs-avatarPlus">+</span>
                            </label>
                            <span class="cs-photoLabel campoObrigatorio">Adicionar Foto</span>
                            <span class="cs-photoError" role="alert" hidden></span>
                        </div>
                    </div>

                    <div class="cs-formGroup cs-campoLargo">
                        <label for="startupName" class="campoObrigatorio">Nome Da Startup</label>
                        <input type="text" id="startupName" name="startupName" maxlength="100" placeholder="Digite o nome da Startup" autocomplete="organization" required />
                    </div>

                    <div class="cs-formGroup">
                        <label for="cnpj" class="campoObrigatorio">CNPJ</label>
                        <input type="text" inputmode="numeric" id="cnpj" name="cnpj" data-mascara="cnpj" placeholder="00.000.000/0000-00" required />
                    </div>

                    <div class="cs-formGroup"><label for="site">Site</label><input type="url" id="site" name="site" placeholder="https://startup.com.br" /></div>
                    <div class="cs-formGroup"><label for="instagram">Instagram</label><input type="url" id="instagram" name="instagram" placeholder="https://instagram.com/..." /></div>
                    <div class="cs-formGroup"><label for="linkedin">LinkedIn</label><input type="url" id="linkedin" name="linkedin" placeholder="https://linkedin.com/company/..." /></div>
                </section>

                <!-- ETAPA 2 — Localização e Programa -->
                <section class="cs-card" data-etapa="2" aria-labelledby="col2-title">
                    <div class="cs-cardTitle">
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 2a8 8 0 0 1 8 8c0 5.5-8 14-8 14S5 15.5 5 10a8 8 0 0 1 8-8z" stroke="#2F3C54" stroke-width="2" />
                            <circle fill="#2F3C54" cx="13" cy="10" r="3" />
                        </svg>
                        <h2 id="col2-title">Localização e Programa</h2>
                    </div>
                    <hr class="cs-cardDivider" />

                    <div class="cs-formGroup"><label for="cep">CEP</label><input type="text" id="cep" name="cep" inputmode="numeric" data-mascara="cep" placeholder="00000-000" /></div>
                    <div class="cs-formGroup"><label for="logradouro" class="campoObrigatorio">Logradouro</label><input type="text" id="logradouro" name="logradouro" maxlength="255" autocomplete="address-line1" required /></div>
                    <div class="cs-formGroup"><label for="numero" class="campoObrigatorio">Número</label><input type="number" id="numero" name="numero" min="0" value="0" required /></div>
                    <div class="cs-formGroup"><label for="complemento">Complemento</label><input type="text" id="complemento" name="complemento" autocomplete="address-line2" /></div>
                    <div class="cs-formGroup"><label for="bairro" class="campoObrigatorio">Bairro</label><input type="text" id="bairro" name="bairro" maxlength="255" required /></div>
                    <div class="cs-formGroup"><label for="cidade" class="campoObrigatorio">Cidade</label><input type="text" id="cidade" name="cidade" maxlength="255" autocomplete="address-level2" required /></div>
                    <div class="cs-formGroup"><label for="uf" class="campoObrigatorio">UF</label><input type="text" id="uf" name="uf" maxlength="2" minlength="2" autocomplete="address-level1" required /></div>

                    <div class="cs-formGroup">
                        <label for="programs">Participação em Outros Programas</label>
                        <input type="text" id="programs" name="programs" maxlength="50" placeholder="Ex.: SEBRAE, Inova MS…" />
                    </div>

                    <div class="cs-formGroup">
                        <label for="sector" class="campoObrigatorio">Setor De Atuação</label>
                        <div class="cs-selectWrapper cs-sectorWrapper">
                            <select id="sector" name="sector" required>
                                <option value="">Selecione o setor</option>
                                <option value="tecnologia">Tecnologia</option>
                                <option value="saúde">Saúde</option>
                                <option value="educação">Educação</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="agronegócio">Agronegócio</option>
                                <option value="varejo">Varejo</option>
                                <option value="logística">Logística</option>
                                <option value="outros">Outros</option>
                            </select>
                            <input type="text" id="sectorOther" name="sectorOther" maxlength="150" placeholder="Digite a área de atuação" aria-label="Outra área de atuação" hidden>
                            <button type="button" id="sectorOtherBack" class="cs-sectorBack" aria-label="Escolher outro setor" title="Escolher outro setor" hidden>×</button>
                        </div>
                    </div>

                    <div class="cs-formGroup">
                        <label class="campoObrigatorio">Data De Fundação</label>
                        <div class="cs-dateRow">
                            <div class="cs-selectWrapper">
                                <select id="foundDay" name="foundDay" aria-label="Dia de fundação" required></select>
                            </div>
                            <div class="cs-selectWrapper">
                                <select id="foundMonth" name="foundMonth" aria-label="Mês de fundação" required></select>
                            </div>
                            <div class="cs-selectWrapper">
                                <select id="foundYear" name="foundYear" aria-label="Ano de fundação" required></select>
                            </div>
                        </div>
                    </div>

                    <div class="cs-formGroup"><label for="trl" class="campoObrigatorio">TRL</label><select id="trl" name="trl" required><?php for ($nivel = 1; $nivel <= 9; $nivel++): ?><option value="<?= $nivel ?>"><?= $nivel ?></option><?php endfor; ?></select></div>
                </section>

                <!-- COLUNA 3 - FUNDADORES -->
                <section class="cs-card" data-etapa="3" aria-labelledby="col3-title">
                    <div class="cs-cardTitle">
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="2" width="16" height="20" rx="2" stroke="#2F3C54" stroke-width="2" />
                            <path d="M7 7h8M7 11h8M7 15h5" stroke="#2F3C54" stroke-width="1.6" stroke-linecap="round" />
                            <circle fill="#2F3C54" cx="20" cy="20" r="5" />
                            <path d="M18 20h4M20 18v4" stroke="white" stroke-width="1.6" stroke-linecap="round" />
                        </svg>
                        <h2 id="col3-title">Fundadores</h2>
                    </div>
                    <hr class="cs-cardDivider" />

                    <fieldset>
                        <legend class="cs-sectionTitle campoObrigatorio">Fundadores</legend>
                        <div class="cs-foundersList" id="foundersList" aria-live="polite"></div>
                        <button type="button" class="cs-btnAddFounder" id="addFounderBtn">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="6" stroke="#2F3C54" stroke-width="1.5" />
                                <path d="M7 4v6M4 7h6" stroke="#2F3C54" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            Adicionar Fundador
                        </button>
                        <span class="cs-founderError erroCampoParceiro" id="founderRequiredError" role="alert" hidden>Adicione pelo menos um fundador.</span>
                    </fieldset>
                </section>

            </div>

            <footer class="cs-modalFooter">
                <button type="button" class="cs-btnVoltar" hidden onclick="voltarStartup()" aria-label="Voltar à etapa anterior" title="Voltar">
                    <i class="fa-solid fa-angle-left" aria-hidden="true"></i>
                </button>
                <div class="cs-footerAcoes">
                    <button type="button" class="btnCancelar" onclick="fecharModal('modalCadastroStartup')">CANCELAR</button>
                    <button type="button" class="btnSalvar cs-btnAvancar" onclick="avancarStartup()">AVANÇAR</button>
                    <button type="submit" class="btnSalvar" id="btnSalvarStartup" hidden>SALVAR</button>
                </div>
            </footer>
        </form>

    </div>

<?php fecharModal(false); ?>

<?php
// ════════════════════════════════════════════════════════════════════
// MODAL 2 — Cadastro/edição do fundador na lista do formulário
// ════════════════════════════════════════════════════════════════════
abrirModal(
    'modalDetalheFundador',
    'Cadastrar Fundador',
    '',
    '<svg width="26" height="26" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <circle cx="12" cy="7" r="4" fill="white" />
        <path d="M4 20c0-4.4 3.6-8 8-8s8 3.6 8 8" fill="white" />
    </svg>'
);
?>

    <div class="areaDetalheFundador">
        <p class="cs-subtitle">Preencha os dados do fundador para vinculá-lo à startup.</p>
        <form id="founderForm" class="cs-founderForm">
            <div class="cs-cardTitle"><h2>Dados do Fundador</h2></div>
            <hr class="cs-cardDivider" />
            <div class="cs-formGroup cs-founderNomeGroup">
                <label for="founderNome" class="campoObrigatorio">Nome</label>
                <input type="text" id="founderNome" class="cs-founderNomeInput" maxlength="100" placeholder="Nome do fundador" required>
            </div>

            <div class="cs-formGroup">
                <label for="founderCargo" class="campoObrigatorio">Função</label>
                <input type="text" id="founderCargo" class="cs-founderCargoInput" maxlength="100" placeholder="Ex.: CEO, CTO, Head de Produto..." required>
            </div>

            <div class="cs-formGroup">
                <label for="founderInicio" class="campoObrigatorio">Data de entrada na startup</label>
                <input type="date" id="founderInicio" class="cs-founderInicioInput" max="<?= (new \DateTimeImmutable('today', new \DateTimeZone('America/Cuiaba')))->format('Y-m-d') ?>" required>
            </div>

            <div class="cs-founderFormRow">
                <div class="cs-formGroup">
                    <label for="founderEmail" class="campoObrigatorio">E-mail</label>
                    <input type="email" id="founderEmail" class="cs-founderEmailInput" maxlength="320" placeholder="email@exemplo.com" required>
                </div>

                <div class="cs-formGroup">
                    <label for="founderTelefone">Telefone</label>
                    <input type="text" inputmode="numeric" id="founderTelefone" class="cs-founderTelefoneInput" data-mascara="telefone" placeholder="(xx) xxxxx-xxxx">
                </div>
            </div>

            <footer class="cs-secondaryModalFooter">
                <button type="button" class="btnCancelar" onclick="fecharModal('modalDetalheFundador')">CANCELAR</button>
                <button type="submit" class="btnSalvar">SALVAR</button>
            </footer>
        </form>

    </div>

<?php fecharModal(false); ?>

<div class="menuLateralAreaTrabalho crudStartups">
    <button class="botaoAddStartup abrirModal" data-modal="modalCadastroStartup">
        <i class="fa-solid fa-plus"></i>
        STARTUP
    </button>
    <div class="containerTabela" id="paginar">
        <table class="tabela tabelaStartups">
            <thead class="tabelaCabecalho tabelaCabecalhoStartups">
                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoStartups">
                    <th class="itemTabelaCabecalho itemTabelaStartups1">Startup</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups2">Setor de Atuação</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups3">CNPJ</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups4">Localização</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups5">TRL</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups6">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaStartups7">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoStartups">


            </tbody>
        </table>
    </div>
    <?php paginacao() ?>
</div>

<script src="<?= BASE_JS ?>Componentes/mascaras.js"></script>
<script src="<?= BASE_JS ?>Componentes/mascaraInputs.js"></script>
<script src="<?= BASE_JS ?>Componentes/validador.js"></script>
<script src="<?= BASE_JS ?>Componentes/previewImagem.js"></script>
<script src="<?= BASE_JS ?>crudStartups.js"></script>
<script src="<?= BASE_JS ?>Componentes/btnAtivoParceiro.js"></script>
<script src="<?= BASE_JS ?>Componentes/tabela.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('mostrarModalSalvo') === '1') {
        sessionStorage.removeItem('mostrarModalSalvo');
        window.mostrarModalSalvo();
    }
});
</script>

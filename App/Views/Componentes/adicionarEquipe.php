<?php
abrirModal('cadastroEquipe', 'Cadastrar Equipe', 'fa-solid fa-users', '', 'equipe/cadastrar', true);
?>
<div class="equipeFormulario" data-step="1">
    <input type="hidden" name="id" id="idEquipe" value="">
    <nav class="equipeEtapas" aria-label="Etapas do cadastro">
        <div class="equipeEtapa ativa" data-step-indicator="1"><span>1</span> Dados pessoais</div>
        <hr data-step-line="1">
        <div class="equipeEtapa" data-step-indicator="2"><span>2</span> Disponibilidade</div>
        <hr data-step-line="2">
        <div class="equipeEtapa" data-step-indicator="3"><span>3</span> Perfil de acesso</div>
    </nav>
    <p class="equipeInstrucao">Preencha as informações para cadastrar um membro da equipe.</p>
    <p class="equipeErroEnvio" role="alert" aria-live="polite" hidden></p>

    <div class="equipePainelCadastro">
        <section class="equipePasso" data-step="1">
            <div class="equipeCadastroCard">
                <h2>Dados Pessoais</h2>
                <div class="equipeLinhaCard"></div>
                <div class="equipeFotoEdicao">
                    <div class="equipeFotoContainer divEquipeFoto">
                        <div class="equipeFotoCard">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <label
                            class="equipeAddCard" for="inputFoto" aria-label="Anexar foto" aria-required="true" tabindex="0">
                            <i class="fa-solid fa-plus"></i>
                            <span class="tooltipAnexarFoto">Anexar Foto</span>
                        </label>
                        <input
                            type="file" id="inputFoto" name="foto" accept="image/*" hidden>
                    </div>
                    <div class="equipeModalRecorte" hidden role="dialog" aria-modal="true" aria-labelledby="tituloRecorteFoto">
                      <div class="equipeEditorRecorte">
                        <h2 id="tituloRecorteFoto">Ajustar foto</h2>
                        <p>Arraste a foto para ajustar o enquadramento</p>
                        <div class="equipeAreaRecorte">
                            <canvas
                                class="equipeCanvasRecorte"
                                aria-label="Prévia do recorte da foto"></canvas>
                        </div>
                        <label>
                            Zoom
                            <input
                                class="equipeZoomRecorte"
                                type="range"
                                min="1"
                                max="3"
                                step="0.01"
                                value="1">
                        </label>
                        <div class="equipeAcoesRecorte">
                            <button type="button" class="equipeCancelarRecorte">Cancelar</button>
                            <button type="button" class="equipeAplicarRecorte">Salvar foto</button>
                        </div>
                      </div>
                    </div>
                </div>
                <label class="equipeCampoCard"><span class="equipeRotuloCampo">Nome<span class="equipeMarcacaoObrigatoria">*</span></span><input class="inputAddParceiros" type="text" name="nome" autocomplete="name" placeholder="Nome completo" required></label>
                <label class="equipeCampoCard"><span class="equipeRotuloCampo">Email<span class="equipeMarcacaoObrigatoria">*</span></span><input class="inputAddParceiros" type="email" name="email" autocomplete="email" placeholder="nome@exemplo.com" required></label>
                <div class="equipeCampoTelefoneCpf">
                    <label class="equipeCampoCard"><span class="equipeRotuloCampo">Telefone<span class="equipeMarcacaoObrigatoria">*</span></span><input class="inputAddParceiros" type="text" name="telefone" data-mascara="telefone" inputmode="numeric" autocomplete="tel" placeholder="(00) 00000-0000" required></label>
                    <label class="equipeCampoCard"><span class="equipeRotuloCampo">CPF<span class="equipeMarcacaoObrigatoria">*</span></span><input class="inputAddParceiros" type="text" name="cpf" data-mascara="cpf" data-cpf-url="<?= htmlspecialchars(BASE_URL . 'equipe/cpf-existe', ENT_QUOTES, 'UTF-8') ?>" inputmode="numeric" autocomplete="off" placeholder="000.000.000-00" required></label>
                </div>
            </div>
        </section>

        <section class="equipePasso" data-step="2" hidden>
            <div class="equipeCadastroCard">
                <h2>Disponibilidade</h2>
                <div class="equipeLinhaCard"></div>
                <div class="equipeGrupoCampo" data-required-group="dias">
                    <span class="equipeGrupoTitulo" id="equipeTituloDias">Dias da semana<span>*</span></span>
                    <fieldset class="equipeGrupoObrigatorio" aria-labelledby="equipeTituloDias">
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="segunda"><span class="equipeCheckmark checkmark"></span>  Segunda-feira</label>
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="terca"><span class="equipeCheckmark checkmark"></span>  Terça-feira</label>
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="quarta"><span class="equipeCheckmark checkmark"></span>  Quarta-feira</label>
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="quinta"><span class="equipeCheckmark checkmark"></span>  Quinta-feira</label>
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="sexta"><span class="equipeCheckmark checkmark"></span>  Sexta-feira</label>
                        <label class="equipeCheckbox container"><input type="checkbox" name="diaSemana[]" value="sabado"><span class="equipeCheckmark checkmark"></span>  Sábado</label>
                    </fieldset>
                </div>
                <div class="equipeDisponibilidadeLinha">
                    <div class="equipeGrupoCampo" data-required-group="horarios">
                        <span class="equipeGrupoTitulo" id="equipeTituloHorarios">Horários<span>*</span></span>
                        <fieldset class="equipeGrupoObrigatorio" aria-labelledby="equipeTituloHorarios">
                            <label class="equipeCheckbox container"><input type="checkbox" name="horario[]" value="matutino"><span class="equipeCheckmark checkmark"></span>  Matutino</label>
                            <label class="equipeCheckbox container"><input type="checkbox" name="horario[]" value="vespertino"><span class="equipeCheckmark checkmark"></span>  Vespertino</label>
                            <label class="equipeCheckbox container"><input type="checkbox" name="horario[]" value="noturno"><span class="equipeCheckmark checkmark"></span>  Noturno</label>
                        </fieldset>
                    </div>
                    <label class="equipeCampoCard equipeCampoModalidade">
                        <span class="equipeRotuloCampo">Modalidade<span class="equipeMarcacaoObrigatoria">*</span></span>
                        <select class="equipeSelecionarModalidade inputAddParceiros" name="modalidade" required>
                            <option value="">Selecione uma modalidade</option>
                            <option value="presencial">Presencial</option>
                            <option value="remoto">Remoto</option>
                            <option value="hibrido">Híbrido</option>
                        </select>
                    </label>
                </div>
            </div>
        </section>

        <section class="equipePasso" data-step="3" hidden>
            <div class="equipeCadastroCard">
                <h2>Perfil de Acesso</h2>
                <div class="equipeLinhaCard"></div>
                <div class="equipePerfilCargoLinha">
                    <label class="equipeCampoCard"><span class="equipeRotuloCampo">Perfil<span class="equipeMarcacaoObrigatoria">*</span></span>
                        <select class="inputAddParceiros" name="perfil" required>
                            <option value="">Selecione um perfil</option>
                            <?php foreach (($perfis ?? []) as $perfil): ?>
                                <option value="<?= (int) $perfil['id'] ?>"><?= htmlspecialchars(ucfirst($perfil['nome']), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="equipeCampoCard"><span class="equipeRotuloCampo">Cargo<span class="equipeMarcacaoObrigatoria">*</span></span>
                        <select class="inputAddParceiros" name="cargo" required>
                            <option value="">Selecione um cargo</option>
                            <option value="Diretoria">Diretoria</option>
                            <option value="Superintendente">Superintendente</option>
                            <option value="Analista Tecnico">Analista Técnico</option>
                            <option value="Assistente">Assistente</option>
                            <option value="Estagiário">Estagiário</option>
                        </select>
                    </label>
                    <button class="equipeAdicionarPerfil" type="button" onclick="openModalNovoperfil()" aria-label="Adicionar Perfil" title="Adicionar Perfil" data-tooltip="Adicionar Perfil"><span>+</span></button>
                </div>
            </div>
        </section>

        <div class="equipeBtnAcoesFormularios">
            <button class="btnVoltarParceiro equipeVoltar" type="button" aria-label="Voltar" title="Voltar" data-tooltip="Voltar" hidden>
                <i class="fa-solid fa-angle-left"></i>
            </button>
            <div class="equipeAcoesEtapa">
                <button class="btnCancelar equipeCancelarCadastro" type="button">CANCELAR</button>
                <button class="btnSalvar equipeAvancar" type="button">
                    AVANÇAR <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button class="btnSalvar equipeSalvarBtn" type="submit" hidden>SALVAR</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_JS ?>Componentes/mascaraInputs.js"></script>
<script src="<?= BASE_JS ?>Componentes/validador.js"></script>
<script src="<?= BASE_JS ?>equipeCadastro.js"></script>
<?php fecharModal(true); ?>

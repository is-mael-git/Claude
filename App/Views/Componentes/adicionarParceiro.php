<?php abrirModal('modalCadastroParceiro', 'Adicionar Parceiros', '', '<svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10.0315 4.19933H13.5387L17.3849 0.345446C17.4936 0.235986 17.6229 0.149105 17.7653 0.0898152C17.9078 0.0305253 18.0606 0 18.2149 0C18.3692 0 18.522 0.0305253 18.6645 0.0898152C18.807 0.149105 18.9363 0.235986 19.0449 0.345446L22.0611 3.37016C22.2789 3.58897 22.4011 3.88496 22.4011 4.19349C22.4011 4.50201 22.2789 4.79801 22.0611 5.01682L19.384 7.70285H10.0315V10.0385C10.0315 10.3483 9.90831 10.6453 9.68907 10.8643C9.46983 11.0833 9.17247 11.2064 8.86242 11.2064C8.55236 11.2064 8.25501 11.0833 8.03576 10.8643C7.81652 10.6453 7.69335 10.3483 7.69335 10.0385V6.53501C7.69335 5.91555 7.93969 5.32146 8.37817 4.88343C8.81666 4.44541 9.41137 4.19933 10.0315 4.19933ZM3.01711 10.0385V14.7099L0.339955 17.3843C0.122216 17.6031 0 17.8991 0 18.2076C0 18.5161 0.122216 18.8121 0.339955 19.0309L3.35613 22.0556C3.46481 22.1651 3.59411 22.252 3.73657 22.3113C3.87904 22.3706 4.03184 22.4011 4.18617 22.4011C4.3405 22.4011 4.4933 22.3706 4.63576 22.3113C4.77822 22.252 4.90752 22.1651 5.0162 22.0556L10.0315 17.0456H14.7077C15.0178 17.0456 15.3151 16.9226 15.5344 16.7035C15.7536 16.4845 15.8768 16.1875 15.8768 15.8777V14.7099H17.0458C17.3559 14.7099 17.6533 14.5869 17.8725 14.3679C18.0917 14.1488 18.2149 13.8518 18.2149 13.5421V12.3742H19.384C19.694 12.3742 19.9914 12.2512 20.2106 12.0322C20.4299 11.8132 20.553 11.5161 20.553 11.2064V10.0385H12.3696V11.2064C12.3696 11.8258 12.1233 12.4199 11.6848 12.858C11.2463 13.296 10.6516 13.5421 10.0315 13.5421H7.69335C7.07324 13.5421 6.47853 13.296 6.04005 12.858C5.60157 12.4199 5.35523 11.8258 5.35523 11.2064V7.70285L3.01711 10.0385Z"
                    fill="#E6EFFF" />
                </svg>', 'parceiros/adicionar', true); ?>
<div class="areaCadastroParceiro">
    <input type="hidden" name="id" id="idParceiro" value="">

    <div class="etapasParceiro">

        <div class="etapaParceiro etapaAtivaParceiro" data-etapa="1">
            <span class="numeroEtapaParceiro">1</span>
            <p>Dados da Empresa Parceira</p>
        </div>

        <div class="linhaEtapaParceiro"></div>

        <div class="etapaParceiro" data-etapa="2">
            <span class="numeroEtapaParceiro">2</span>
            <p>Dados do Representante Legal</p>
        </div>

    </div>

    <div class="scrollCadastroParceiro">
        <div class="caixaPrincipalAdicionarParceiros">
            <section class="secaoCadastroParceiro secaoDadosEmpresa active">
                <div class="areaInputsParceiros">
                    <div class="wrapperDescLogoParceiro">
                        <div class="wrapperFotoParceiro">
                            <img src="<?= BASE_ASSETS ?>Icones/Vector.svg" alt="Foto do parceiro" id="previewFotoParceiro">
                            <label for="foto"><svg width="36" height="35" viewBox="0 0 36 35" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="18" cy="17.5" rx="18" ry="17.5" fill="#2F3C54" />
                                    <path
                                        d="M16.7143 18.2857H10.2857C9.92143 18.2857 9.61629 18.1623 9.37029 17.9154C9.12429 17.6686 9.00086 17.3634 9 17C8.99915 16.6366 9.12258 16.3314 9.37029 16.0846C9.618 15.8377 9.92315 15.7143 10.2857 15.7143H16.7143V9.28572C16.7143 8.92143 16.8377 8.61629 17.0846 8.37029C17.3314 8.12429 17.6366 8.00086 18 8C18.3634 7.99915 18.669 8.12258 18.9167 8.37029C19.1644 8.618 19.2874 8.92315 19.2857 9.28572V15.7143H25.7143C26.0786 15.7143 26.3841 15.8377 26.631 16.0846C26.8779 16.3314 27.0009 16.6366 27 17C26.9991 17.3634 26.8757 17.669 26.6297 17.9167C26.3837 18.1644 26.0786 18.2874 25.7143 18.2857H19.2857V24.7143C19.2857 25.0786 19.1623 25.3841 18.9154 25.631C18.6686 25.8779 18.3634 26.0009 18 26C17.6366 25.9991 17.3314 25.8757 17.0846 25.6297C16.8377 25.3837 16.7143 25.0786 16.7143 24.7143V18.2857Z"
                                        fill="#E6EFFF" />
                                </svg>
                            </label>
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" data-validar-campo required>
                        </div>
                        <p class="labelAddParceiros labelLogoEmpresaParceira campoObrigatorio">Logo da Empresa Parceira</p>
                    </div>

                    <div class="camposEmpresaParceiro">
                        <div class="inputWrapperParceiros">
                            <label for="nome" class="labelAddParceiros campoObrigatorio">Nome da Instituicao</label>
                            <input type="text" name="nome" id="nome" class="inputAddParceiros" placeholder="Ex.: Empresa Exemplo" data-validar-campo required>
                        </div>

                        <div class="juncaoInputsParceiro">
                            <div class="inputWrapperParceiros">
                                <label for="cnpj" class="labelAddParceiros campoObrigatorio">CNPJ</label>
                                <input type="text" data-mascara="cnpj" data-validar-campo name="cnpj" id="cnpj" class="inputAddParceiros" placeholder="00.000.000/0000-00" required>
                            </div>
                            <div class="inputWrapperParceiros">
                                <label for="telefone" class="labelAddParceiros campoObrigatorio">Telefone</label>
                                <input type="tel" name="telefone" inputmode="numeric" data-mascara="telefone" data-validar-campo id="telefone" class="inputAddParceiros" placeholder="(00) 00000-0000" required>
                            </div>
                        </div>

                        <div class="juncaoInputsParceiro">
                            <div class="inputWrapperParceiros">
                                <label for="site" class="labelAddParceiros">Site</label>
                                <input type="text" name="site" id="site" class="inputAddParceiros" placeholder="https://www.empresa.com.br" data-validar-campo data-validar-url>
                            </div>
                            <div class="inputWrapperParceiros">
                                <label for="idAreaAtuacao" class="labelAddParceiros campoObrigatorio">Area de Atuação</label>
                                <!-- o selectCustomizado.js troca este select pela lista estilizada -->
                                <div class="wrapperAreaAtuacao">
                                    <select name="idAreaAtuacao" id="idAreaAtuacao" class="selectCustomizavel" data-validar-campo required>
                                        <option value="" selected disabled>Selecione</option>
                                        <?php foreach (($areas ?? []) as $area): ?>
                                            <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="novaAreaAtuacao" id="novaAreaAtuacao" class="inputAddParceiros campoNovaArea" placeholder="Ex.: Tecnologia" data-validar-campo hidden>
                                    <button class="botaoAreaAtuacao" type="button" title="Cadastrar nova área"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="juncaoInputsParceiro">
                            <div class="inputWrapperParceiros">
                                <label for="instagram" class="labelAddParceiros">Instagram</label>
                                <input type="text" name="instagram" id="instagram" class="inputAddParceiros" placeholder="https://www.instagram.com/empresa" data-validar-campo data-validar-url>
                            </div>
                            <div class="inputWrapperParceiros">
                                <label for="linkedin" class="labelAddParceiros">Linkedin</label>
                                <input type="text" name="linkedin" id="linkedin" class="inputAddParceiros" placeholder="https://www.linkedin.com/company/empresa" data-validar-campo data-validar-url>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="areaBotaoEmpresaParceira">
                    <button type="button" onclick="fecharModal('modalCadastroParceiro')" class="btnCancelar">CANCELAR</button>
                    <button type="button" class="btnSalvar" onclick="avancarParceiro()">AVANÇAR</button>
                </div>
            </section>
            <section class="secaoCadastroParceiro secaoRepresentanteLegal">
                <div class="areaInputsParceiros">
                    <div class="inputWrapperParceiros">
                        <label for="nomeRepresentante" class="labelAddParceiros campoObrigatorio">Nome Completo</label>
                        <input type="text" name="nomeRepresentante" id="nomeRepresentante" class="inputAddParceiros"
                            placeholder="Nome completo do representante" data-validar-campo required>
                    </div>

                    <div class="inputWrapperParceiros">
                        <label for="telefoneRepresentante" class="labelAddParceiros campoObrigatorio">Telefone</label>
                        <input type="tel" name="telefoneRepresentante" id="telefoneRepresentante" inputmode="numeric" class="inputAddParceiros" placeholder="(00) 00000-0000" data-mascara="telefone" data-validar-campo required>
                    </div>

                    <div class="inputWrapperParceiros">
                        <label for="cargoRepresentante" class="labelAddParceiros campoObrigatorio">Cargo</label>
                        <input type="text" name="cargoRepresentante" id="cargoRepresentante" class="inputAddParceiros"
                            placeholder="Ex.: Diretor(a) Comercial" data-validar-campo required>
                    </div>
                    <div class="inputWrapperParceiros">
                        <label for="emailRepresentante" class="labelAddParceiros campoObrigatorio">E-Mail</label>                        <input type="email" name="emailRepresentante" id="emailRepresentante" class="inputAddParceiros"
                            placeholder="representante@empresa.com.br" data-validar-campo required>
                    </div>
                </div>
                <div class="areaBotaoRepresentanteParceiro">
                    <button type="button" class="btnVoltarParceiro" onclick="voltarParceiro()"><i class="fa-solid fa-angle-left"></i></button>
                    <div class="wrapperBtnSalvarParceiro">
                        <button type="button" onclick="fecharModal('modalCadastroParceiro')" class="btnCancelar">CANCELAR</button>
                        <button type="submit" class="btnSalvar">SALVAR</button>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

<?php fecharModal(true); ?>

<script src="<?= BASE_JS ?>Componentes/mascaraInputs.js"></script>
<script src="<?= BASE_JS ?>Componentes/validador.js"></script>
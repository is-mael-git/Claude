<?php abrirModal('modalCadastroMentor', 'Cadastro de Mentores', 'fa-solid fa-user-pen', '', 'mentores/adicionar', true); ?>


<div class="areaModalCentral" data-step="1">

    <section class="areaStatusCard">
        <div class="statusCard1" data-card="1">
            <p class="circuloStatus1">1</p>
            <p class="nomeStatusCard">Dados Pessoais</p>
        </div>

        <hr class="statusPronto" data-linha="1">

        <div class="statusCard2" data-card="2">
            <p class="circuloStatus2">2</p>
            <p class="nomeStatusCard">Disponibilidade</p>
        </div>

        <hr class="statusPronto" data-linha="2">

        <div class="statusCard3" data-card="3">
            <p class="circuloStatus3">3</p>
            <p class="nomeStatusCard">Competências</p>
        </div>
    </section>

    <input type="hidden" name="id" id="mentorId" value="">

    <p class="subtituloSuperiorInfo">Preencha as informações do mentor.</p>
    <div class="areaCentralCadastro">

        <section class="cardDadosPessoais" data-card="1">
            <h2 class="tituloCardMentor">Dados Pessoais</h2>

            <div class="areaFotoBannerMentor">
                <div class="perfilContainer">
                    <div class="areaFoto">
                        <label class="btnAdicionarFoto">
                            <i class="fa-solid fa-plus"></i>
                            <input type="file" id="mentorFoto" name="mentorFoto" accept="image/jpeg,image/png,image/webp" hidden>
                        </label>
                        <img class="fotoPerfil" src="/ParkTec/Public/Assets/Icones/Vector.svg" alt="Foto de perfil do usuário">
                    </div>
                    <p class="legendaMidia">Perfil</p>
                    <span class="msgErro" data-erro-para="mentorFoto"></span>
                </div>

                <div class="bannerContainer">
                    <div class="areaBanner">
                        <label class="btnAdicionarBanner">
                            <i class="fa-solid fa-plus"></i>
                            <input type="file" id="bannerMentor" name="banner" accept="image/jpeg,image/png,image/webp" hidden>
                        </label>
                        <img class="fotoBanner" src="<?= BASE_ASSETS ?>Icones/exBanner.svg" alt="Banner do mentor">
                    </div>
                    <p class="legendaMidia">Banner</p>
                    <span class="msgErro" data-erro-para="banner"></span>
                </div>
            </div>

                <div class="socialInputs">
                    <div class="dadoTelefoneMentor">
                        <label class="inputMentorTitulo" for="nome">Nome<span class="asteristico">*</span><span class="msgErro" data-erro-para="nome"></span></label>
                        <input class="inputMentorCadastro" type="text" name="nome" id="nome" placeholder="Nome do Mentor" required>
                    </div>
                    <div class="dadoInstagramMentor">
                        <label class="inputMentorTitulo" for="email">Email<span class="asteristico">*</span><span class="msgErro" data-erro-para="email"></span></label>
                        <input class="inputMentorCadastro" type="email" name="email" id="email" placeholder="email@exemplo.com" required>
                    </div>
                </div>


            <!-- id parceiros são as empresas cadastradas no sistema, e o mentor pode estar vinculado a uma ou mais empresas. O select abaixo permite selecionar a empresa à qual o mentor está vinculado. (não que esquecer disso) -->
            <div class="inputWrapperParceiros">
                <label for="idAreaAtuacao" class="labelAddParceiros inputMentorTituloParceiro">Empresa Parceira<span class="asteristico">*</span><span class="msgErro" data-erro-para="id_parceiro"></span></label>
                <!-- o selectCustomizado.js troca este select pela lista estilizada -->
                <div class="wrapperAreaAtuacao">
                    <!-- sem data-validar-campo: a validação deste campo é feita pelo crudMentores.js -->
                    <select name="id_parceiro" id="idAreaAtuacao" class="selectCustomizavel" required>
                        <option value="" selected disabled>Selecione</option>
                        <?php foreach (($parceiros ?? []) as $parceiro): ?>
                            <option value="<?= $parceiro['id'] ?>"><?= htmlspecialchars($parceiro['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="novaAreaAtuacao" id="novaAreaAtuacao" class="inputAddParceiros campoNovaArea" placeholder="Ex.: Tecnologia" data-validar-campo hidden>
                    <button class="botaoAreaAtuacao" type="button" title="Cadastrar novo parceiro"><i class="fa-solid fa-plus"></i></button>
                </div>
            </div>


            <div class="socialInputs">
                <div class="dadoTelefoneMentor">
                    <label class="inputMentorTitulo" for="telefone">Telefone<span class="asteristico">*</span><span class="msgErro" data-erro-para="telefone"></span></label>
                    <input class="inputMentorCadastroSocial" type="text" inputmode="numeric" autocomplete="tel" data-mascara="telefone" name="telefone" id="telefone" placeholder="(67) 99999-9999" maxlength="15" required>
                </div>

                <div class="dadoInstagramMentor">
                    <label class="inputMentorTitulo" for="instagram">Instagram<span class="msgErro" data-erro-para="instagram"></span></label>
                    <input class="inputMentorCadastroSocial" type="text" name="instagram" id="instagram" placeholder="https://www.instagram.com/seu-perfil" maxlength="255">
                </div>
            </div>

            <div class="socialInputs">

                <div class="dadoLinkedin">
                    <label class="inputMentorTitulo" for="linkedin">Linkedin<span class="msgErro" data-erro-para="linkedin"></span></label>
                    <input class="inputMentorCadastroSocial" type="text" name="linkedin" id="linkedin" placeholder="https://www.linkedin.com/in/seu-perfil" maxlength="255">
                </div>


            </div>

            <div class="btnAreaMentor">
                <div class="btnSetas btnSetasMentor1">
                    <button type="button" class="btnVoltarCard btnDefault" aria-label="Voltar" title="Voltar"><i class="fa-solid fa-chevron-left"></i></button>
                    <div class="mentorWrapperButton">
                        <button type="button" onclick="fecharModal('modalCadastroMentor')" class="btnCancelar btnCancelarMentor">CANCELAR</button>
                        <button type="button" class="btnAvancarCard btnSalvar">AVANÇAR</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="cardDisponibilidade" data-card="2">
            <h2 class="tituloCardMentor">Disponibilidade</h2>

            <p class="inputMentorTitulo">Semana<span class="asteristico">*</span>
                <span class="msgErro" data-erro-para="semana[]"></span>
            </p>
            <div class="selecaoDisponibilidade">

                <div class="diasSemana1">


                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="segunda" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Segunda-feira</span>
                    </label>

                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="terca" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Terça-feira</span>
                    </label>

                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="quarta" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Quarta-feira</span>
                    </label>
                </div>

                <div class="diasSemana2">

                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="quinta" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Quinta-feira</span>
                    </label>

                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="sexta" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Sexta-feira</span>
                    </label>
                    <label class="selecaoDia">
                        <div class="container">
                            <input name="semana[]" value="sabado" type="checkbox">
                            <div class="checkmark"></div>
                        </div>
                        <span class="selecaoItemNome">Sábado</span>
                    </label>
                </div>

            </div>

            <div class="inputsDropdown">
                <div class="dropHorarios">
                    <p class="inputMentorTitulo">Horários<span class="asteristico">*</span><span class="msgErro" data-erro-para="horario[]"></span></p>

                    <div class="areaDrop">
                        <button class="dropDisponibilidade" type="button" aria-expanded="false">
                            <span class="textoDropDisponibilidade">Selecione</span>
                            <i class="fa-solid fa-chevron-down setaDropDisponibilidade"></i>
                        </button>
                        <div class="dropConteudo">

                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="horario[]" value="matutino" type="checkbox">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Matutino</span>
                            </label>
                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="horario[]" value="vespertino" type="checkbox">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Vespertino</span>
                            </label>
                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="horario[]" value="noturno" type="checkbox">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Noturno</span>
                            </label>

                        </div>
                    </div>

                </div>

                <div class="dropModalidade">
                    <p class="inputMentorTitulo">Modalidade<span class="asteristico">*</span>
                        <span class="msgErro" data-erro-para="modalidade"></span>
                    </p>
                    <div class="areaDrop">
                        <button class="dropDisponibilidade" type="button" aria-expanded="false">
                            <span class="textoDropDisponibilidade">Selecione</span>
                            <i class="fa-solid fa-chevron-down setaDropDisponibilidade"></i>
                        </button>
                        <div class="dropConteudo">
                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="modalidade" value="online" type="radio">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Online</span>
                            </label>
                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="modalidade" value="hibrido" type="radio">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Híbrido</span>
                            </label>
                            <label class="itemDadoDrop">
                                <div class="container">
                                    <input name="modalidade" value="presencial" type="radio">
                                    <div class="checkmark"></div>
                                </div>
                                <span class="selecaoItemNome">Presencial</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="biografiaInput">
                <label class="inputMentorTitulo" for="biografia">Biografia<span class="asteristico">*</span>
                    <span class="msgErro" data-erro-para="biografia"></span>
                </label>
                <textarea class="caixaBiografia" name="biografia" maxlength="500" id="biografia" placeholder="Conte sua experiência como mentor..." required></textarea>
                <span class="qtdCaracter">0/500</span>
            </div>

            <div class="btnAreaMentor">
                <div class="btnSetas">
                    <button type="button" class="btnVoltarCard btnDefault" aria-label="Voltar" title="Voltar"><i class="fa-solid fa-chevron-left"></i></button>
                    <div class="mentorWrapperButton">
                        <button type="button" onclick="fecharModal('modalCadastroMentor')" class="btnCancelar btnCancelarMentor">CANCELAR</button>
                        <button type="button" class="btnAvancarCard btnSalvar">AVANÇAR</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="cardCernes" data-card="3">
            <h2 class="tituloCardMentor">Eixos Cernes & Competências<span class="asteristico">*</span><span class="msgErro" data-erro-para="competencia[]"></span></h2>

            <p class="inputMentorTitulo">Tecnologia</p>
            <div class="tecnologiaCerne">
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="produto&amp;tecnologia">
                    <span class="btnEstiloVisual">Produto e Tecnologia</span>
                </label>
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="operacoes">
                    <span class="btnEstiloVisual">Operações</span>
                </label>
            </div>

            <p class="inputMentorTitulo">Capital</p>
            <div class="capitalCerne">
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="capital&amp;investimentos">
                    <span class="btnEstiloVisual">Capital & Investimentos</span>
                </label>
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="financas">
                    <span class="btnEstiloVisual">Finanças</span>
                </label>
            </div>

            <p class="inputMentorTitulo">Empreendedor</p>
            <div class="empreendedorCerne">
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="estrategia">
                    <span class="btnEstiloVisual">Estratégia</span>
                </label>
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="gestao">
                    <span class="btnEstiloVisual">Gestão</span>
                </label>
            </div>

            <p class="inputMentorTitulo">Mercado</p>
            <div class="mercadoCerne">
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="networking&amp;conexoes">
                    <span class="btnEstiloVisual">Networking & Conexões</span>
                </label>
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="marketing&amp;vendas">
                    <span class="btnEstiloVisual">Marketing & Vendas</span>
                </label>
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="mercados&amp;clientes">
                    <span class="btnEstiloVisual">Mercados & Clientes</span>
                </label>
            </div>

            <p class="inputMentorTitulo">Gestão</p>
            <div class="gestaoCerne">
                <label class="btnSelecaoEixo">
                    <input type="checkbox" name="competencia[]" value="juridico&amp;pi">
                    <span class="btnEstiloVisual">Jurídico & PI</span>
                </label>
            </div>
            <div class="btnAreaMentor">
                <div class="btnSetas">
                    <button type="button" class="btnVoltarCard btnDefault" aria-label="Voltar" title="Voltar"><i class="fa-solid fa-chevron-left"></i></button>
                    <div class="mentorWrapperButton">
                        <button type="button" onclick="fecharModal('modalCadastroMentor')" class="btnCancelar btnCancelarMentor">CANCELAR</button>
                        <button type="submit" class="btnSalvar btnSalvarMentor">SALVAR</button>
                    </div>
                </div>
            </div>
        </section>

    </div>



</div>


<?php fecharModal(true); ?>


<script src="<?= BASE_JS ?>Componentes/mascaraInputs.js"></script>
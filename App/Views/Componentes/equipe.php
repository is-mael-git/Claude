<?php


include_once $_SERVER['DOCUMENT_ROOT'] . "/ParkTechCG/View/Pages/menu_lateral.php";

?>

<section class="modal-principalStaff" id="cadastrarStaff">


        <div class="cadastrar-modal">

        
            <!-- TOPO -->
            <header class="informacao-modal">

                <div class="icone-modal">

                    <div class="icone-trabalho">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <p class="title-modal">CADASTRAR STAFF</p>

                </div>

                <button class="fechar-modal" onclick="closeStaff()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>

            <!-- FUNDO -->
            <main class="fundo-modal">

                <!-- STEPS -->
                <section class="container-modal">

                    <div class="step-modal">
                        <div class="equipe-circulo ativo">1</div>
                        <span class="texto-modal texto-ativo">
                            Dados Pessoais
                        </span>
                    </div>

                    <div class="linha-container"></div>

                    <div class="step-modal">
                        <div class="equipe-circulo inativo">2</div>
                        <span class="texto texto-inativo">
                            Disponibilidade
                        </span>
                    </div>

                    <div class="linha-container"></div>

                    <div class="step-modal">
                        <div class="equipe-circulo inativo">3</div>
                        <span class="texto texto-inativo">
                            Perfil de Acesso
                        </span>
                    </div>

                </section>


                <!-- TEXTO -->
                <div class="texto-informacao-modal">
                    <p>
                        Preencha as informações para cadastrar um staff no sistema.
                    </p>
                </div>

                <!-- CARDS -->

                <div class="cadastrarStaff">


                    <!-- DADOS PESSOAIS -->
                    <div class="espacamento1">
                        <div class="dadosPessoaisCards">
                            <section class="dados-pessoais-cards">

                                <div class="titulo-card">
                                    <i class="fa-solid fa-user icone-titulo"></i>
                                    <h2>Dados Pessoais</h2>
                                </div>

                                <div class="linha-card"></div>

                                <div class="foto-container">

                                    <div class="foto-card">
                                        <i class="fa-solid fa-user"></i>
                                    </div>

                                    <button class="add-card">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>

                                </div>

                                <div class="campo-card">
                                    <label for="nome">Nome</label>

                                    <div class="input-icon">
                                        <i class="fa-solid fa-user"></i>
                                        <input type="text" id="nome" placeholder="Digite seu Nome Completo">
                                    </div>
                                </div>

                                <div class="campo-card">
                                    <label for="email">Email</label>

                                    <div class="input-icon">
                                        <i class="fa-solid fa-envelope"></i>

                                        <input type="text" id="email" placeholder="Digite seu Email Completo">
                                    </div>
                                </div>

                                <div class="campoTelCpf">
                                    <div class="campo-card">
                                        <label for="telefone">Telefone</label>

                                        <div class="input-icon">
                                            <i class="fa-solid fa-phone"></i>
                                            <input type="tel" id="telefone" placeholder="Digite seu telefone">
                                        </div>
                                    </div>

                                    <div class="campo-card">
                                        <label for="cpf">CPF</label>

                                        <div class="input-icon">
                                            <i class="fa-solid fa-id-card"></i>
                                            <input type="cpf" id="cpf" placeholder="Digite seu CPF">
                                        </div>
                                    </div>
                                </div>



                            </section>

                        </div>

                    </div>

                    <div class="disponibilidadeCards">
                        <div class="espacamento2">
                            <section class="disponibilidade card">

                                <div class="titulo-card">
                                    <i class="fa-solid fa-calendar icone-titulo"></i>
                                    <h2>Disponibilidade</h2>
                                </div>

                                <div class="linha-card"></div>

                                <div class="grupo-disponibilidade">
                                    <h3>Dias da Semana</h3>

                                    <div class="disponobilidadeSemana">
                                        <div class="grupo-disponibilidade2">

                                            <label><input type="checkbox"> Segunda-feira</label>
                                            <label><input type="checkbox"> Terça-feira</label>
                                            <label><input type="checkbox"> Quarta-feira</label>
                                        </div>

                                        <div class="grupo-disponibilidade2">

                                            <label><input type="checkbox"> Quinta-feira</label>
                                            <label><input type="checkbox"> Sexta-feira</label>

                                        </div>
                                    </div>
                                </div>

                                <div class="linha-card"></div>

                                <div class="grupo-disponibilidade">
                                    <h3>Horários</h3>

                                    <label><input type="checkbox"> Matutino</label>
                                    <label><input type="checkbox"> Vespertino</label>
                                    <label><input type="checkbox"> Noturno</label>
                                </div>

                                <div class="linha-card"></div>

                                <div class="grupo-disponibilidade">
                                    <h3>Modalidade</h3>

                                    <label><input type="checkbox"> Presencial</label>
                                    <label><input type="checkbox"> Remoto</label>
                                    <label><input type="checkbox"> Híbrido</label>
                                </div>

                            </section>

                        </div>
                    </div>
                    <div class="perfilsCard">
                        <div class="espacamento3">

                            <section class="perfis card">

                                <div class="titulo-card">
                                    <i class="fa-solid fa-user-shield icone-titulo"></i>
                                    <h2>Perfis de Acesso</h2>
                                </div>

                                <div class="linha-card"></div>

                                <div class="perfil-itens">
                                    <span>Administrador</span>
                                    <div class="cor perfil-admin"></div>
                                </div>

                                <div class="perfil-itens">
                                    <span>Estagiário</span>
                                    <div class="cor perfil-estagiario"></div>
                                </div>

                                <div class="perfil-itens">
                                    <span>Mentor</span>
                                    <div class="cor perfil-mentor"></div>
                                </div>

                                <div class="perfil-itens">
                                    <span>Startup</span>
                                    <div class="cor perfil-startup"></div>
                                </div>

                                <button class="adicionar-perfil" onclick="openModalNovoperfil()">
                                    + Novo Perfil
                                </button>



                            </section>

                        </div>
                        <div class="btnAcoesFormularios">
                            <button class="btnCancelar">Cancelar</button>
                            <button class="btnSalvar salvarBtn">Salvar</button>
                        </div>



                    </div>


                </div>

            </main>
        </div>

</section>



</main>

</div>

</section>


<div id="overlay" class="equipeOverlay">

    <div class="equipeModalPerfilStaff">
        <div class="equipeTitleStaff">
            CADASTRAR PERFIL
            <button class="equipeCloseBtnStaff" onclick="closeModalNovoperfil()">
                ✕
            </button>
        </div>

        <div class="equipeModalHeaderStaff">

            <div class="equipeModalStaff">

                <div class="equipeTopRow">

                    <div class="equipeField">
                        <label>Nome do perfil</label>
                        <input
                            type="text"
                            id="profileName"
                            placeholder="Digite o nome do perfil">
                    </div>

                    <div class="equipeField color-field">

                        <label>Cor do perfil</label>

                        <div class="color-selector">

                            <select id="profileColor">

                                <option value="#F4D000">
                                    Amarelo</option>

                                <option value="#35B44A">
                                    Verde
                                </option>

                                <option value="#D62939">
                                    Vermelho
                                </option>

                                <option value="#450451">
                                    Roxo
                                </option>

                            </select>

                            <span id="colorPreview"></span>

                        </div>

                    </div>

                </div>

                <h2>Permissões de acesso</h2>

                <div class="equipePermissionBox">

                    <label class="equipeSelectAll">
                        <input
                            type="checkbox"
                            id="selectAll">
                        Selecionar todos
                    </label>

                    <hr>

                    <div class="equipeSection">

                        <h3>Visualizadores</h3>

                        <div class="equipeGridPermissoes">

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Visualizar dashboard ADM</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Visualizar dashboard startup</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Visualizar dashboard mentores</label>

                            </div>

                            <div>
                                <label><input type="checkbox" class="equipePermission"> Visualizar área da startup</label>

                                <br>


                                <label><input type="checkbox" class="equipePermission"> Visualizar área mentores</label>


                                <br>

                                <label><input type="checkbox" class="equipePermission"> Visualizar dashboard ADM</label>
                            </div>

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Visualizar relatos</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Visualizar área parceiros</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Visualizar área staff</label>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Gerenciadores</h3>

                        <div class="equipeGridPermissoes">

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Gerenciar documentos</label>

                                <label><input type="checkbox" class="equipePermission"> Gerenciar parceiros</label>

                            </div>

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Gerenciar questionários</label>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Validadores</h3>

                        <div class="equipeGridPermissoes">

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Validar junção startup x mentor</label>

                            </div>

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Validar reuniões</label>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="equipeSection">

                        <h3>Operações</h3>

                        <div class="equipeGridPermissoes">

                            <div>

                                <label><input type="checkbox" class="equipePermission"> Relatar reuniões</label>

                                <label><input type="checkbox" class="equipePermission"> Agendar reuniões</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Cadastrar perfis</label>

                                <label><input type="checkbox" class="equipePermission"> Realizar questionário</label>

                                <br>

                                <label><input type="checkbox" class="equipePermission"> Receber notificações</label>

                            </div>

                        </div>

                    </div>

                    <div class="equipeFooterButtons">
                        <button
                            type="button"
                            class="equipeCancelBtn"
                            onclick="closeModalNovoperfil()"
                        >
                            CANCELAR
                        </button>

                        <button type="button" class="equipeSaveBtn">
                            SALVAR
                        </button>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<div class="menuLateralAreaTrabalho area-trabalho-crud-equipe">

    <button class="btn-cadastrar-staff" onclick="openStaff()">
        Cadastrar STAFF
    </button>

    <div class="containerTabela">
        <table class="tabela tabelaParceiros">
            <thead class="tabelaCabecalho tabelaCabecalhoParceiros">
                <tr class="linhaTabelaCabecalho linhaTabelaCabecalhoParceiros">
                    <th class="itemTabelaCabecalho itemTabelaParceiros1">Parceiro</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros2">Área de Atuação</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros3">CNPJ</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros4">E-mail</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros5">Telefone</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros6">Status</th>
                    <th class="itemTabelaCabecalho itemTabelaParceiros7">Ações</th>
                </tr>
            </thead>
            <tbody class="tabelaCorpo tabelaCorpoParceiros">
                <tr class="linhaTabelaCorpo linhaTabelaCorpoParceiros">
                    <td class="itemTabelaCorpo itemTabelaParceiros1">
                        <div class="imgWrapper">

                            <img
                                src="https://yt3.googleusercontent.com/Yvl6yndRtGAfTmEd7_27messwiyzF9caEkTb18bziBS14fmJ-OVFwKJt6PKjPzJ8jLEGhLdZ=s900-c-k-c0x00ffffff-no-rj"
                                alt="Nubank">
                            <p>Nubank</p>
                        </div>
                    </td>
                    <td class="itemTabelaCorpo itemTabelaParceiros2">Capital & Investimento | Produto & Tecnologia
                        | Estratégia | Mercado & Clientes | Finanças
                        Marketing & Vendas</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros3">12.345.678/0001-90</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros4">Contato@Tech_assist.com</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros5"><i class="fa-brands fa-whatsapp"></i>(67) 11
                        89563-4321</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros6"></td>
                    <td class="itemTabelaCorpo itemTabelaParceiros7">

                    </td>
                </tr>
            </tbody>
        </table>
    </div>



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="/ParkTechCG/Public/Js/novoperfil.js"></script>
</div>

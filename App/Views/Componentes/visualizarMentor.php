<?php abrirModal('visualizarMentor', 'Visualizar Mentor', 'fa-solid fa-eye', '', '', false); ?>

<section class="areaVisualizacaoMentor">
    <div class="areaImagensMentor">
        <div class="visPerfil">
            <img class="visFoto" data-campo="foto" src="<?= BASE_ASSETS ?>Icones/Vector.svg" alt="Foto do mentor">
        </div>
        
        <div class="visBanner">
            <img data-campo="banner" src="<?= BASE_ASSETS ?>Icones/exBanner.svg" alt="Banner do mentor">
        </div>

    </div>
        <div class="areaComumTeste">
            <div class="visPerfilInfo">
                <h2 class="visNome" data-campo="nome"></h2>
                <span class="visStatus status" data-campo="status"></span>
            </div>
            <button class="btnVisualizarCurriculo" data-campo="curriculo" onclick="abrirCurriculo(this)">Baixar Currículo<i class="fa-solid fa-file-arrow-down"></i></button>

        </div>



    <div class="agruparDadosMentor">

        <div class="areaInfoDadoMentor">
            <div class="blocoInfoDados">
                <div class="visItem">
                    <span class="visRotulo">E-mail</span>
                    <span class="visValor" data-campo="email"></span>
                </div>
                <div class="visItem">
                    <span class="visRotulo">Telefone</span>
                    <span class="visValor" data-campo="telefone"></span>
                </div>
                <div class="visItem">
                    <span class="visRotulo">Empresa parceira</span>
                    <span class="visValor" data-campo="parceiro"></span>
                </div>
            </div>



            <hr class="linhaMentorHorizontal"/>
            
            <div class="blocoInfoRedes">
                <div class="visItem">
                    <span class="visRotulo">LinkedIn</span>
                    <span class="visValor" data-campo="linkedin"></span>
                </div>
                
                <div class="visItem">
                    <span class="visRotulo">Instagram</span>
                    <span class="visValor" data-campo="instagram"></span>
                </div>
                <!-- <div class="visItem">
                    <span class="visRotulo">Curriculum</span>
                    <button class="btnVisualizarCurriculo" data-campo="curriculo" onclick="abrirCurriculo(this)">Baixar</button>
                </div> -->
            </div>

            <hr class="linhaMentorHorizontal"/>
            
            <div class="BlocoInfoBio">
                <div class="visBloco">
                    <span class="visRotulo">Biografia</span>
                    <p class="visBio" data-campo="biografia"></p>
                </div>
            </div>
        </div>

        <div class="linhaMentorVertical"></div>
        
        <div class="areaInfoDadoMentor">
            <div class="blocoInfoDisponibilidade">
                <div class="visBloco">
                    <span class="visRotulo">Dias disponíveis</span>
                    <div class="visTags" data-campo="dias"></div>
                </div>
            </div>
            
            <div class="blocoInfoModalidade">
                <div class="visBloco">
                    <span class="visRotulo">Modalidade</span>
                    <div class="visTags" data-campo="modalidade"></div>
                </div>
                <div class="visBloco">
                    <span class="visRotulo">Horários</span>
                    <div class="visTags" data-campo="horarios"></div>
                </div>
            </div>
            
            <div class="blocoInfoCompetencias">
                <div class="visBloco">
                    <span class="visRotulo">Eixos e competências</span>
                    <div class="visTags" data-campo="competencias"></div>
                </div>
            </div>
        </div>
    </div>
    

    <div class="visRodape">
        <button type="button" onclick="fecharModal('visualizarMentor')" class="btnCancelar btnCancelarMentor">Fechar</button>

    </div>
</section>

<?php fecharModal(false); ?>
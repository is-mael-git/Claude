<?php abrirModal('visualizarMentor', 'Visualizar Mentor', 'fa-solid fa-eye', '', '', false); ?>

<section class="areaVisualizacaoMentor">
    <div class="areaImagensMentor">
            <div class="visBanner">
            <img data-campo="banner" src="<?= BASE_ASSETS ?>Icones/exBanner.svg" alt="Banner do mentor">
        </div>

        <div class="visPerfil">
            <img class="visFoto" data-campo="foto" src="<?= BASE_ASSETS ?>Icones/Vector.svg" alt="Foto do mentor">
            <div class="visPerfilInfo">
                <h2 class="visNome" data-campo="nome"></h2>
                <span class="visStatus status" data-campo="status"></span>
            </div>
        </div>
    </div>
   
    <div class="cardsVisualizar">
        <div class="visaoCard1">
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
            <div class="visItem">
                <span class="visRotulo">LinkedIn</span>
                <span class="visValor" data-campo="linkedin"></span>
            </div>
            <div class="visItem">
                <span class="visRotulo">Instagram</span>
                <span class="visValor" data-campo="instagram"></span>
            </div>
            <div class="visBloco">
                <span class="visRotulo">Biografia</span>
                <p class="visBio" data-campo="biografia"></p>
            </div>
        </div>
        
        <div class="visaoCard2">
            <div class="visBloco">
                <span class="visRotulo">Dias disponíveis</span>
                <div class="visTags" data-campo="dias"></div>
            </div>

            <div class="visBloco">
                <span class="visRotulo">Modalidade</span>
                <div class="visTags" data-campo="modalidade"></div>
            </div>
            <div class="visBloco">
                <span class="visRotulo">Horários</span>
                <div class="visTags" data-campo="horarios"></div>
            </div>

            <div class="visBloco">
                <span class="visRotulo">Eixos e competências</span>
                <div class="visTags" data-campo="competencias"></div>
            </div>
        </div>

    </div>

    <div class="visRodape">
        <button type="button" onclick="fecharModal('visualizarMentor')" class="btnCancelar btnCancelarMentor">Fechar</button>

    </div>
</section>

<?php fecharModal(false); ?>
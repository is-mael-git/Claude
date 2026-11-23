<?php abrirModal('modalMatch', 'Validar Mentoria Selecionada', 'fa-solid fa-clock', '', "", true); ?>

<div class="areaCardsMatch">
    <div class="cardStartup">
        <div class="iconStartup"><i class="fa-solid fa-building"></i></div>
        <div class="informacoesStartup">
            <div class="classStartup">Startup</div>
            <div class="nomeStartup" id="modalMatchStartup"></div>
        </div>
    </div>
    <div class="cardMentor">
        <div class="iconMentor" > <i class="fa-solid fa-user"></i> </a> </div>
        <div class="informacoesMentor">
            <div class="classMentor">Mentor</div>
            <div class="nomeMentor" id="modalMatchMentor"></div>
        </div>
    </div>
</div>
<div class="areaCompetenciasFragilidades">
    
    <div class="FragilidadesStartup">
        <div class="nomeFragilidades"><i class="fa-solid fa-circle-exclamation"></i> Fragilidades identificadas pela startup</div>
        
        <div class="boxCardInformativoFragilidades" id="modalMatchFragilidades"></div>
        
    </div>
    <div class="competenciasMentor">
        <div class="nomeCompetenciasMentor"><i class="fa-solid fa-user-group"></i> Competências do mentor</div>
        <div class="areaCardInformativoCompetencias">
            <div class="cardInformativoCompetencias" id="modal"  ><i class="fa-solid fa-signal"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-regular fa-lightbulb"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-solid fa-gear"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-solid fa-user-group"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-solid fa-microchip"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-solid fa-coins"></i> </div>
            <div class="cardInformativoCompetencias" id="modal" ><i class="fa-solid fa-share-nodes"></i> </div>
        </div>
    </div>

</div>


<div class="areaBotoesAprovarNegar">
    <button type="button" class="btnCancelar" onclick="fecharModal('modalMatch')">NEGAR</button>
    <button type="button" class="btnSalvar" id="modalMatchAprovar">APROVAR</button>
</div>

        
        <?php fecharModal(true) ?>

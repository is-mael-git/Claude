<?php
use Controller\controllerMenuLateral;
?>
<dialog id="modalPerfil" class="menuLateralModalTrocaPerfil">
    <button class="menuLateralEscolhaPerfil">
        <div class="menuLateralContainerPerfil">
            <!-- <img class="menuLateralPerfilFoto" src="/ParkTec/Public/Assets/ImgSistema/perfil.jpg" alt="Foto de Perfil"> -->
            <div class="menuLateralContainerFoto">
                <i class="fa-solid fa-circle-user"></i>
            </div>
            <div class="menuLateralCaixaPerfil">
                <h2 class="menuLateralTituloPerfil">Mentor</h2>
                <span>mentorfulano@gmail.com</span>
            </div>
        </div>
    </button>
    <button class="menuLateralEscolhaPerfil">
        <div class="menuLateralContainerPerfil">
            <!-- <img class="menuLateralPerfilFoto" src="/ParkTechCG/Public/Assets/icons/perfil.jpg" alt="Foto de Perfil"> -->
            <div class="menuLateralContainerFoto">
                <i class="fa-solid fa-circle-user"></i>
            </div>
            <div class="menuLateralCaixaPerfil">
                <h2 class="menuLateralTituloPerfil">Mentor</h2>
                <span>mentorfulano@gmail.com</span>
            </div>
        </div>
    </button>
    <div class="menuLateralLinhaPerfil"></div>
    <!-- <div class="menuLateralConfiguracaoPerfil">
        <button class="menuLateralBotaoConfigPerfil" type="button">
            <i id="menuLateralImgConfig" class="fa-solid fa-gear"></i>
            Configurações</button>
    </div>
    <div class="menuLateralLinhaPerfil"></div> -->
    <form class="menuLateralConfiguracaoPerfil" action="<?= BASE_URL ?>logout" method="POST">
        <button class="menuLateralBotaoConfigPerfil" type="submit">
            <i class="menuLateralIconeMenuSair">
                <?= ControllerMenuLateral::icone('sair') ?>
            </i> Sair
            </button>
    </form>
</dialog>
<?php

use Controller\controllerMenuLateral;
?>

<aside class="menuLateral">
    <div class="menuLateralImageLogoMenu">
        <img src="/ParkTec/Public/Assets/ImgSistema/logo-footer-sem-texto.png" alt="Logo-parktecg" class="menuLateralLogoMenu">
        <img src="/ParkTec/Public/Assets/ImgSistema/logo-footer-texto.png" alt="Logo-parktecg" class="menuLateralLogoMenuFechado">
    </div>
    <nav class="menuLateralNav">
        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('areaTrabalho/') ?>"
            onclick="window.location.href='<?= BASE_URL ?>areaTrabalho/adm'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('areaTrabalho') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Area de Trabalho</a>
        </div>

        <!-- <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('areaTrabalhoMentor/') ?>"
            onclick="window.location.href='<?= BASE_URL ?>areaTrabalhoMentor/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('dashboard') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Dashboard</a>
        </div> -->

        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('startups/') ?>"
            onclick="window.location.href='<?= BASE_URL ?>startups/index'">

            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('startups') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Startups</a>
        </div>
        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('mentores/') ?>"
            onclick="window.location.href='<?= BASE_URL ?>mentores/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('mentores') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Mentores</a>

        </div>

        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('equipe/') ?>"
            onclick="window.location.href='<?= BASE_URL ?>equipe/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('equipe') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Equipe</a>
        </div>

        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('parceiros/') ?>"

            onclick=" window.location.href='<?= BASE_URL ?>parceiros/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('parceiros') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Parceiros</a>
        </div>

        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('questao/') ?>"

            onclick=" window.location.href='<?= BASE_URL ?>questao/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('questao') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Questão</a>
        </div>

        <div class="menuLateralCaixaOpcaoMenu <?= controllerMenuLateral::verificarUrl('questionario/') ?>"

            onclick=" window.location.href='<?= BASE_URL ?>questionario/index'">
            <div class="menuLateralAreaIconeMenu">
                <i class="menuLateralIconeMenu">
                    <?= ControllerMenuLateral::icone('questionario') ?>
                </i>
            </div>
            <a class="menuLateralOpcaoMenu">Questionário</a>
        </div>

        <button id="openBtnMenuLateral">
            <i id="menuLateralOpenBtnIcon">
                <?= ControllerMenuLateral::icone('btnMenu') ?>
            </i>
        </button>
    </nav>
    <form class="sairRodapeMenuLateral" action="<?= BASE_URL ?>logout" method="POST">
        <div class="barrinhaMenuLateral"></div>
        <button class="sairMenuLateral" type="submit">
            <i class="menuLateralIconeMenuSair">
                <?= ControllerMenuLateral::icone('sair') ?>
            </i>
            <span class="sairMenuLateralTexto">Sair</span>
        </button>
    </form>
</aside>

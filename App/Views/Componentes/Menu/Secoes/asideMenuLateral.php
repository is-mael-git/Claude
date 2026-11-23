<?php

use Controller\controllerMenuLateral;
$mentoresAtivo = controllerMenuLateral::verificarUrl('mentores/') === 'active';
$avaliacaoAtiva = controllerMenuLateral::verificarUrl('questao/') === 'active'
    || controllerMenuLateral::verificarUrl('questionario/') === 'active'
    || controllerMenuLateral::verificarUrl('metodologia/') === 'active';
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
        <div class="menuLateralGrupoMenu">
            <button class="menuLateralCaixaOpcaoMenu menuLateralSubmenuToggle" type="button"
                aria-expanded="<?= $mentoresAtivo ? 'true' : 'false' ?>" aria-controls="menuLateralSubmenuMentores">
                <span class="menuLateralAreaIconeMenu">
                    <i class="menuLateralIconeMenu">
                        <?= ControllerMenuLateral::icone('mentores') ?>
                    </i>
                </span>
                <span class="menuLateralOpcaoMenu">Mentorias</span>
                <span class="menuLateralSetaSubmenu" aria-hidden="true">
                    <svg width="16" height="10" viewBox="0 0 22 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.77797 12.3544L0 2.47054L2.44406 0L11 8.64862L19.5559 0L22 2.47054L12.222 12.3544C11.8979 12.682 11.4583 12.866 11 12.866C10.5417 12.866 10.1021 12.682 9.77797 12.3544Z" fill="currentColor" />
                    </svg>
                </span>
            </button>
            <div class="menuLateralSubmenu" id="menuLateralSubmenuMentores" <?= $mentoresAtivo ? '' : 'inert' ?>>
                <a class="menuLateralCaixaOpcaoMenu menuLateralSubmenuItem <?= controllerMenuLateral::verificarUrl('mentores/index') ?>"
                    href="<?= BASE_URL ?>mentores/index">
                    <span class="menuLateralIconeSubmenu" aria-hidden="true">
                        <?= ControllerMenuLateral::icone('listaMentores') ?>
                    </span>
                    <span class="menuLateralOpcaoMenu">Mentor</span>
                </a>
                <button class="menuLateralCaixaOpcaoMenu menuLateralSubmenuItem" type="button">
                    <span class="menuLateralIconeSubmenu" aria-hidden="true">
                        <?= ControllerMenuLateral::icone('mentorias') ?>
                    </span>
                    <span class="menuLateralOpcaoMenu">Acompanhamento</span>
                </button>
            </div>
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

        <div class="menuLateralGrupoMenu">
            <button class="menuLateralCaixaOpcaoMenu menuLateralSubmenuToggle" type="button"
                aria-controls="menuLateralSubmenuAvaliacao" aria-expanded="<?= $avaliacaoAtiva ? 'true' : 'false' ?>">
                <span class="menuLateralAreaIconeMenu">
                    <i class="menuLateralIconeMenu">
                        <?= ControllerMenuLateral::icone('questionario') ?>
                    </i>
                </span>
                <span class="menuLateralOpcaoMenu">Avaliação</span>
                <span class="menuLateralSetaSubmenu" aria-hidden="true">
                    <svg width="16" height="10" viewBox="0 0 22 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.77797 12.3544L0 2.47054L2.44406 0L11 8.64862L19.5559 0L22 2.47054L12.222 12.3544C11.8979 12.682 11.4583 12.866 11 12.866C10.5417 12.866 10.1021 12.682 9.77797 12.3544Z" fill="currentColor" />
                    </svg>
                </span>
            </button>
            <div class="menuLateralSubmenu" id="menuLateralSubmenuAvaliacao" <?= $avaliacaoAtiva ? '' : 'inert' ?>>
                <a class="menuLateralCaixaOpcaoMenu menuLateralSubmenuItem <?= controllerMenuLateral::verificarUrl('questao/index') ?>"
                    href="<?= BASE_URL ?>questao/index">
                    <span class="menuLateralIconeSubmenu" aria-hidden="true">
                        <?= ControllerMenuLateral::icone('questao') ?>
                    </span>
                    <span class="menuLateralOpcaoMenu">Questão</span>
                </a>
                <a class="menuLateralCaixaOpcaoMenu menuLateralSubmenuItem <?= controllerMenuLateral::verificarUrl('questionario/index') ?>"
                    href="<?= BASE_URL ?>questionario/index">
                    <span class="menuLateralIconeSubmenu" aria-hidden="true">
                        <?= ControllerMenuLateral::icone('questionario') ?>
                    </span>
                    <span class="menuLateralOpcaoMenu">Questionário</span>
                </a>
                <a class="menuLateralCaixaOpcaoMenu menuLateralSubmenuItem <?= controllerMenuLateral::verificarUrl('metodologia/index') ?>"  href="<?= BASE_URL ?>metodologia/index">
                    <span class="menuLateralIconeSubmenu" aria-hidden="true">
                        <?= ControllerMenuLateral::icone('mentorias') ?>
                    </span>
                    <span class="menuLateralOpcaoMenu">Metodologia</span>
                </a>
            </div>
        </div>

    </nav>
    <button id="openBtnMenuLateral">
        <i id="menuLateralOpenBtnIcon">
            <?= ControllerMenuLateral::icone('btnMenu') ?>
        </i>
    </button>
    <form class="sairRodapeMenuLateral" action="<?= BASE_URL ?>logout" method="POST">
        <div class="barrinhaMenuLateral"></div>
        <button class="sairMenuLateral" type="submit evento.preventDefault();" >
            <i class="menuLateralIconeMenuSair">
                <?= ControllerMenuLateral::icone('sair') ?>
            </i>
            <span class="sairMenuLateralTexto">Sair</span>
        </button>
    </form>
</aside>

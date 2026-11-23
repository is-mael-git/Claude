<?php

use Controller\controllerMenuLateral;

$paginaAtual = trim($_SERVER['REQUEST_URI'], '/');
$dadosMenu = controllerMenuLateral::getDadosMenu($paginaAtual);
$usuarioLogado = controllerMenuLateral::buscarUsuarioLogado();

$notificacoesHeader = controllerMenuLateral::buscarNotificacoes();

$itemNaoVisualizada = false;

foreach ($notificacoesHeader as $notificacao) {
    if ($notificacao['status'] === 'entrada' && (int) $notificacao['visualizado'] === 0) {
        $itemNaoVisualizada = true;
        break;
    }
}


?>

<header class="menuLateralSuperiorMenu" id="headerMenuAncora">
    <button class="menuLateralBotaoNotificacao" id="btnHamburgerMenuLateral" aria-label="Opções do perfil">
        <label class="menuLateralBurger" for="burger">
            <input type="checkbox" id="burger">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </button>
    <div class="menuLateralTitulo">
        <i class="menuLateralIconeTopo">
            <?= $dadosMenu['svg'] ?>
        </i>
        <span class="menuLateralTituloPage"><?= $dadosMenu['titulo'] ?></span>
    </div>
    <form class="menuLateralContainerPesquisar" method="POST">
        <button class="menuLateralBotaoNotificacao" aria-label="Pesquisar">
            <i class="menuLateralIconePesquisa">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.2167 16.5L9.44167 10.725C8.98333 11.0917 8.45625 11.3819 7.86042 11.5958C7.26458 11.8097 6.63056 11.9167 5.95833 11.9167C4.29306 11.9167 2.88383 11.3398 1.73067 10.186C0.5775 9.03222 0.000611596 7.623 4.85009e-07 5.95833C-0.000610626 4.29367 0.576278 2.88444 1.73067 1.73067C2.88506 0.576889 4.29428 0 5.95833 0C7.62239 0 9.03192 0.576889 10.1869 1.73067C11.3419 2.88444 11.9185 4.29367 11.9167 5.95833C11.9167 6.63056 11.8097 7.26458 11.5958 7.86042C11.3819 8.45625 11.0917 8.98333 10.725 9.44167L16.5 15.2167L15.2167 16.5ZM5.95833 10.0833C7.10417 10.0833 8.07828 9.68244 8.88067 8.88067C9.68306 8.07889 10.0839 7.10478 10.0833 5.95833C10.0827 4.81189 9.68183 3.83808 8.88067 3.03692C8.0795 2.23575 7.10539 1.83456 5.95833 1.83333C4.81128 1.83211 3.83747 2.23331 3.03692 3.03692C2.23636 3.84053 1.83517 4.81433 1.83333 5.95833C1.8315 7.10233 2.2327 8.07644 3.03692 8.88067C3.84114 9.68489 4.81495 10.0858 5.95833 10.0833Z"
                        fill="#2F3C54" />
                </svg>
            </i>
        </button>

        <input type="text" placeholder="Pesquisar" name="Pesquisar" id="pesquisar" class="menuLateralInputPesquisar">

        <div class="menuLateralPesquisaResultados" id="pesquisaResultados">
            <ul id="listaResultadosPesquisa"></ul>
            <p class="menuLateralPesquisaSemResultado" id="pesquisaSemResultado">Busca não encontrada</p>
        </div>
    </form>
    <div class="menuLateralCaixaPesquisar">
        <div class="menuLateralContainerPesquisarMobile">
            <button class="menuLateralBotaoNotificacao" aria-label="Pesquisar">
                <i class="menuLateralIconePesquisa">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.2167 16.5L9.44167 10.725C8.98333 11.0917 8.45625 11.3819 7.86042 11.5958C7.26458 11.8097 6.63056 11.9167 5.95833 11.9167C4.29306 11.9167 2.88383 11.3398 1.73067 10.186C0.5775 9.03222 0.000611596 7.623 4.85009e-07 5.95833C-0.000610626 4.29367 0.576278 2.88444 1.73067 1.73067C2.88506 0.576889 4.29428 0 5.95833 0C7.62239 0 9.03192 0.576889 10.1869 1.73067C11.3419 2.88444 11.9185 4.29367 11.9167 5.95833C11.9167 6.63056 11.8097 7.26458 11.5958 7.86042C11.3819 8.45625 11.0917 8.98333 10.725 9.44167L16.5 15.2167L15.2167 16.5ZM5.95833 10.0833C7.10417 10.0833 8.07828 9.68244 8.88067 8.88067C9.68306 8.07889 10.0839 7.10478 10.0833 5.95833C10.0827 4.81189 9.68183 3.83808 8.88067 3.03692C8.0795 2.23575 7.10539 1.83456 5.95833 1.83333C4.81128 1.83211 3.83747 2.23331 3.03692 3.03692C2.23636 3.84053 1.83517 4.81433 1.83333 5.95833C1.8315 7.10233 2.2327 8.07644 3.03692 8.88067C3.84114 9.68489 4.81495 10.0858 5.95833 10.0833Z"
                            fill="#2F3C54" />
                    </svg>
                </i>
            </button>
            <input type="text" placeholder="Pesquisar" name="Pesquisar" id="pesquisar" class="menuLateralInputPesquisar">
        </div>
    </div>
    <div class="menuLateralPerfilMenu">

        <button
            class="menuLateralBotaoNotificacao abrirModal"
            aria-label="Notificações"
            type="button"
            data-modal="modalNotificacao">

            <i class="menuLateralFecharModalenuLateralNotificacao">

                <svg
                    width="22"
                    height="26"
                    viewBox="0 0 22 26"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_6168_7804)">

                        <path
                            d="M10.7339 2.90039C8.49058 2.90039 6.33917 3.79153 4.75293 5.37778C3.16668 6.96402 2.27554 9.11543 2.27554 11.3587V15.6217C2.27572 15.8092 2.23228 15.9941 2.14867 16.1618L0.0739571 20.3101C-0.0273901 20.5127 -0.0752454 20.7379 -0.0650634 20.9643C-0.0548815 21.1906 0.0129995 21.4106 0.132132 21.6034C0.251265 21.7961 0.417695 21.9552 0.615613 22.0655C0.813532 22.1759 1.03637 22.2338 1.26296 22.2337H20.2048C20.4314 22.2338 20.6542 22.1759 20.8521 22.0655C21.0501 21.9552 21.2165 21.7961 21.3356 21.6034C21.4548 21.4106 21.5226 21.1906 21.5328 20.9643C21.543 20.7379 21.4951 20.5127 21.3938 20.3101L19.3203 16.1618C19.2363 15.9942 19.1924 15.8093 19.1922 15.6217V11.3587C19.1922 9.11543 18.3011 6.96402 16.7148 5.37778C15.1286 3.79153 12.9772 2.90039 10.7339 2.90039ZM10.7339 25.8587C9.98392 25.8591 9.2523 25.6269 8.63985 25.1941C8.02741 24.7612 7.56428 24.1491 7.31429 23.4421H14.1535C13.9035 24.1491 13.4403 24.7612 12.8279 25.1941C12.2154 25.6269 11.4838 25.8591 10.7339 25.8587Z"
                            fill="#E6EFFF" />

                        <g class="menuLateralIndicadorNaoLidas" <?= $itemNaoVisualizada ? '' : 'hidden' ?>>
                            <circle cx="17" cy="6" r="6" fill="#2F3C54" />
                            <circle cx="17" cy="6" r="4" fill="#DA0000" />
                        </g>

                    </g>

                    <defs>
                        <clipPath id="clip0_6168_7804">
                            <rect width="22" height="26" fill="white" />
                        </clipPath>
                    </defs>

                </svg>

            </i>
        </button>

        <button class="menuLateralBotaoNotificacao abrirModal" aria-label="Perfil do usuário" data-modal="modalPerfil" type="button">
            <div class="menuLateralContainerFoto">
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </button>
        <div class="menuLateralTextoPerfil">
            <h2 class="menuLateralTexto"><?= htmlspecialchars($usuarioLogado['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <span class="menuLateralSubTextoPerfil"><?= htmlspecialchars($usuarioLogado['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <button class="menuLateralBotaoNotificacaoSeta abrirModal" aria-label="Opções do perfil" data-modal="modalPerfil" type="button">
            <i class="menuLateralSetaPerfil">
                <svg width="22" height="13" viewBox="0 0 22 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.77797 12.3544L0 2.47054L2.44406 0L11 8.64862L19.5559 0L22 2.47054L12.222 12.3544C11.8979 12.682 11.4583 12.866 11 12.866C10.5417 12.866 10.1021 12.682 9.77797 12.3544Z"
                        fill="#E6EFFF" />
                </svg>
            </i>
        </button>
    </div>
</header>

<?php

use Controller\controllerMenuLateral;

$notificacoes = controllerMenuLateral::buscarNotificacoes();

?>

<dialog id="modalNotificacao" class="menuLateralModalNotificacao">
    <div class="menuLateralHeaderNotificacao">
        <h1 class="menuLateralModalTitulo">
            <i class="menuLateralIconeModal">
                <svg width="15" height="20" viewBox="0 0 20 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 25C11.2857 25 12.4286 24.0625 12.7143 22.6562C12.8571 22.1875 12.5714 21.875 12.1429 21.875H7.85714C7.42857 21.875 7.14286 22.1875 7.28571 22.6562C7.57143 24.0625 8.71429 25 10 25ZM2.85714 7.8125C2.85714 3.4375 6 0 10 0C14 0 17.1429 3.4375 17.1429 7.8125V12.5L19.5714 16.5625C19.8571 16.875 20 17.3438 20 17.8125C20 19.0625 19 20.1562 17.8571 20.1562H2.14286C1 20.3125 0 19.2188 0 17.9688C0 17.5 0.142857 17.0312 0.428571 16.7188L2.85714 12.6563V7.8125Z" fill="#E6EFFF" />
                </svg>
            </i>
            Notificações
        </h1>
        <button class="menuLateralFecharModal fecharModal" data-modal="modalNotificacao" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form class="menuLateralAreaModal" method="post">
        <div class="menuLateralBotoesNotifi">
            <button class="menuLateralBotoesNotificacao" type="button" data-status="entrada" aria-pressed="false">Caixa de Entrada</button>
            <button class="menuLateralBotoesNotificacao" type="button" data-status="arquivado" aria-pressed="false">Arquivados</button>
            <button class="menuLateralBotoesNotificacao" type="button" data-status="lixeira" aria-pressed="false">Lixeira</button>
        </div>

        <div class="menuLateralListaNotificacoes">
            <?php foreach ($notificacoes as $notificacao): ?>
                <?php require __DIR__ . '/itemNotificacaoMenuLateral.php'; ?>
            <?php endforeach; ?>
        </div>

        <p class="menuLateralSemNotificacoes" hidden></p>

    </form>
</dialog>

<script src="<?= BASE_JS ?>Componentes/notificacao.js" defer></script>


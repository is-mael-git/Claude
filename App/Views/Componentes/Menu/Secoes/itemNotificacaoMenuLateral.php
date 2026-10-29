<div class="menuLateralNotificacao"
    data-id="<?= (int) $notificacao['id'] ?>"
    data-status="<?= htmlspecialchars($notificacao['status'], ENT_QUOTES, 'UTF-8') ?>"
    data-visualizado="<?= (int) $notificacao['visualizado'] ?>">

    <div class="menuLateralAcoesTopoNotificacao">
        <button class="menuLateralRestaurarNotificacao" type="button" data-acao-notificacao="entrada" aria-label="Devolver à caixa de entrada" title="Desfazer" <?= $notificacao['status'] === 'entrada' ? 'hidden' : '' ?>>
            <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
        </button>
        <button class="menuLateralDispensarNotificacao" type="button" data-acao-notificacao="lixeira" aria-label="Dispensar notificação" title="Dispensar" <?= $notificacao['status'] === 'lixeira' ? 'hidden' : '' ?>>&times;</button>
    </div>

    <div class="menuLateralContainerNotificacao">
        <div class="menuLateralInfosNotificacao">
            <p class="menuLateralDescriNotifi">
                <?= htmlspecialchars($notificacao['mensagem'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <span class="menuLateralHorarioNotifi">
                <?= date('d/m/Y \à\s H:i', strtotime($notificacao['enviado_em'])) ?>
            </span>
        </div>

        <div class="caixaOpcaolerArquivar" <?= $notificacao['status'] !== 'entrada' ? 'hidden' : '' ?>>
            <button class="menuLateralBotaoMarcarNotifi" type="button" data-acao-notificacao="lixeira">Dispensar</button>
            <button class="menuLateralBotaoMarcarNotifi" type="button" data-acao-notificacao="arquivado">Arquivar</button>
        </div>
    </div>
</div>

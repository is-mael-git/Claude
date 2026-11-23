<?php abrirModal('modalSessaoExpirada', 'Sessão expirada', 'fa-solid fa-clock-rotate-left'); ?>

<i class="fa-solid fa-circle-exclamation modalSessaoExpiradaIcone" aria-hidden="true"></i>
<p class="modalSessaoExpiradaMensagem">
    Seu acesso foi encerrado por inatividade. Entre novamente para continuar.
</p>
<?php fecharModal(); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.abrirModal('modalSessaoExpirada');

        const botaoFechar = document.querySelector('#modalSessaoExpirada .fecharModal');
        botaoFechar?.addEventListener('click', () => {
            window.location.assign('<?= BASE_URL ?>login');
        });
    });
</script>

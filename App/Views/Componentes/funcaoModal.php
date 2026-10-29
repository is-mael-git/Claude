<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function abrirModal(string $idModal, string $titulo, ?string $icone = "",?string $svg = "", ?string $acao = "", ?bool $form = false): void
{
?>
    <dialog id="<?php echo htmlspecialchars($idModal); ?>" class="templateModal" >
        <div class="modalHeader">
            <h1 class="modalTitulo">
                <i class="<?php echo htmlspecialchars($icone); ?>"><?php echo $svg?></i>
                <?php echo htmlspecialchars($titulo); ?>
            </h1>
            <button class="fecharModal" data-modal="<?php echo htmlspecialchars($idModal); ?>" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <?php if ($form): ?>
            <form method="POST" action="<?= BASE_URL . $acao ?>" class="areaModal" enctype="multipart/form-data">
        <?php else: ?>
            <div class="areaModal">
        <?php endif; ?>
<?php
}

function fecharModal(bool $form = false): void
{
    if ($form) {
        echo "</form>";
    } else {
        echo "</div>";
    }
?>
    </dialog>
<?php
}

function modal(string $idModal, string $icone, string $titulo, string $arquivo): void
{
    abrirModal($idModal, $icone, $titulo);

    if (file_exists($arquivo)) {
        include $arquivo;
    } else {
        echo '<p class="modalErro">Conteúdo não encontrado: ' . ($arquivo) . '</p>';
    }

    fecharModal();
}
?>

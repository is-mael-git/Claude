const TEMPO_MODAL_SALVO = 2000;

document.addEventListener('DOMContentLoaded', () => {

    const modalSalvo = document.getElementById('modalSalvo');

    if (!(modalSalvo instanceof HTMLDialogElement)) {
        return;
    }

    let temporizador;

    function fecharModalSalvo() {
        clearTimeout(temporizador);
        if (modalSalvo.open) {
            fecharModal('modalSalvo');
        }
    }

    function mostrarModalSalvo() {
        clearTimeout(temporizador);
        if (!modalSalvo.open) {
            abrirModal('modalSalvo');
        }
        temporizador = setTimeout(fecharModalSalvo, TEMPO_MODAL_SALVO);
    }

    modalSalvo.querySelector('.btnXClaro')?.addEventListener('click', fecharModalSalvo);

    window.mostrarModalSalvo = mostrarModalSalvo;
});

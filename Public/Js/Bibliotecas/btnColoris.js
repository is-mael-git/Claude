
let paiDoPicker = document.body;

function moverPickerPara(novoPai) {
    if (novoPai === paiDoPicker) {
        return;
    }

    Coloris.set({ parent: novoPai });
    paiDoPicker = novoPai;
}

document.addEventListener('click', (e) => {
    const gatilho = e.target.closest('[data-coloris], .selecionarCor');

    if (!gatilho) {
        return;
    }

    moverPickerPara(gatilho.closest('dialog[open]') || document.body);
}, true);


document.addEventListener('close', (e) => {
    if (e.target === paiDoPicker) {
        Coloris.close();
        moverPickerPara(document.body);
    }
}, true);


const campo = document.querySelector("#nomeSetor");
campo.addEventListener('input', (e) => {
    var valor = e.target.value;
    const el = document.querySelector(".selecionarCor > button");
    el.innerHTML = `<span style="color: black;"> ${valor} </span>`;

});

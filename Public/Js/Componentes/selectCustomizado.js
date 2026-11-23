

const SETA_SELECT = `<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" class="setaSelectParceiros">
    <path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>
</svg>`;

function montarSelectCustomizado(select) {
    const wrapper = document.createElement('div');
    wrapper.className = 'selectCustomizadoParceiros';

    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    select.hidden = true;

    const selecionado = document.createElement('button');
    selecionado.type = 'button';
    selecionado.className = 'inputAddParceiros selecionadoSelectParceiros';
    selecionado.innerHTML = `<span class="textoSelecionado"></span>${SETA_SELECT}`;

    wrapper.appendChild(selecionado);

    const lista = document.createElement('div');
    lista.className = 'opcoesSelectParceiros';

    Array.from(select.options).forEach(opcao => {
        // a opção vazia serve só de rótulo inicial, não vira item da lista
        if (opcao.value === '') {
            return;
        }

        const item = document.createElement('div');
        item.className = 'opcaoSelectParceiros';
        item.textContent = opcao.textContent;
        item.dataset.valor = opcao.value;

        lista.appendChild(item);
    });

    wrapper.appendChild(lista);

    function sincronizar() {
        const opcao = select.options[select.selectedIndex];

        selecionado.querySelector('.textoSelecionado').textContent = opcao ? opcao.textContent : '';

        lista.querySelectorAll('.opcaoSelectParceiros').forEach(item => {
            item.classList.toggle('opcaoAtualParceiros', item.dataset.valor === select.value);
        });
    }

    function fechar() {
        wrapper.classList.remove('aberto');
    }

    selecionado.addEventListener('click', () => {
        wrapper.classList.toggle('aberto');
    });

    lista.addEventListener('click', (evento) => {
        const item = evento.target.closest('.opcaoSelectParceiros');

        if (!item) {
            return;
        }

        select.value = item.dataset.valor;

        // avisa quem escuta o select, como a lista nativa faria
        select.dispatchEvent(new Event('change', { bubbles: true }));

        fechar();
    });

    // o change também chega quando outro script preenche o select (edição)
    select.addEventListener('change', sincronizar);

    // form.reset() não dispara change, e o valor só volta depois do evento
    select.form?.addEventListener('reset', () => setTimeout(sincronizar));

    document.addEventListener('click', (evento) => {
        if (!wrapper.contains(evento.target)) {
            fechar();
        }
    });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape') {
            fechar();
        }
    });

    sincronizar();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select.selectCustomizavel').forEach(montarSelectCustomizado);
});

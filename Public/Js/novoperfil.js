const overlayPerfil = document.getElementById('overlay');
const dialogCadastroEquipe = document.getElementById('cadastroEquipe');
const urlCadastroPerfil = overlayPerfil?.dataset.saveUrl;
const botaoSalvarPerfil = overlayPerfil?.querySelector('.equipeSaveBtn');
const campoNomePerfil = overlayPerfil?.querySelector('#profileName');
const checkboxSelecionarTodos = overlayPerfil?.querySelector('#selectAll');
const checkboxesPermissao = [...(overlayPerfil?.querySelectorAll('.equipePermission') ?? [])];

function openModalNovoperfil() {
    if (!overlayPerfil || !dialogCadastroEquipe) return;

    // O dialog nativo fica na camada superior do navegador. Manter o overlay
    // dentro dele permite exibir o perfil acima do cadastro sem fechar o form.
    if (overlayPerfil.parentElement !== dialogCadastroEquipe) {
        dialogCadastroEquipe.appendChild(overlayPerfil);
    }

    dialogCadastroEquipe.classList.add('equipeComPerfilAberto');
    overlayPerfil.style.display = 'flex';
    overlayPerfil.setAttribute('aria-hidden', 'false');
    overlayPerfil.querySelector('#profileName')?.focus();
}

function closeModalNovoperfil() {
    if (!overlayPerfil) return;

    overlayPerfil.style.display = 'none';
    overlayPerfil.setAttribute('aria-hidden', 'true');
    dialogCadastroEquipe?.classList.remove('equipeComPerfilAberto');
}

dialogCadastroEquipe?.addEventListener('close', closeModalNovoperfil);

overlayPerfil?.addEventListener('click', (evento) => {
    if (evento.target === overlayPerfil) closeModalNovoperfil();
});

checkboxSelecionarTodos?.addEventListener('change', () => {
    checkboxesPermissao.forEach((permissao) => {
        permissao.checked = checkboxSelecionarTodos.checked;
    });
    checkboxSelecionarTodos.indeterminate = false;
});

checkboxesPermissao.forEach((permissao) => {
    permissao.addEventListener('change', () => {
        checkboxSelecionarTodos.checked = checkboxesPermissao.every((item) => item.checked);
        checkboxSelecionarTodos.indeterminate = checkboxesPermissao.some((item) => item.checked)
            && !checkboxSelecionarTodos.checked;
    });
});

botaoSalvarPerfil?.addEventListener('click', async () => {
    const nome = campoNomePerfil?.value.trim() ?? '';

    if (!nome) {
        campoNomePerfil?.focus();
        return;
    }

    botaoSalvarPerfil.disabled = true;
    const textoOriginal = botaoSalvarPerfil.textContent;
    botaoSalvarPerfil.textContent = 'SALVANDO...';

    try {
        const resposta = await fetch(urlCadastroPerfil, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                nome,
                permissoes: checkboxesPermissao
                    .filter((permissao) => permissao.checked)
                    .map((permissao) => permissao.value)
            })
        });
        const resultado = await resposta.json();

        if (!resposta.ok || !resultado.sucesso || !resultado.perfil) {
            throw new Error(resultado.erro || 'Não foi possível salvar o perfil.');
        }

        const selectPerfil = dialogCadastroEquipe?.querySelector('select[name="perfil"]');
        if (selectPerfil) {
            selectPerfil.add(new Option(resultado.perfil.nome, resultado.perfil.id, true, true));
            selectPerfil.dispatchEvent(new Event('change', { bubbles: true }));
        }

        campoNomePerfil.value = '';
        checkboxesPermissao.forEach((permissao) => {
            permissao.checked = false;
        });
        checkboxSelecionarTodos.checked = false;
        checkboxSelecionarTodos.indeterminate = false;
        closeModalNovoperfil();
    } catch (erro) {
        console.error('Não foi possível salvar o perfil:', erro);
    } finally {
        botaoSalvarPerfil.disabled = false;
        botaoSalvarPerfil.textContent = textoOriginal;
    }
});

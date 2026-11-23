/*
 * COMO USAR
 *
 * Inclua este arquivo na página que contém o formulário e marque com
 * `data-validar-campo` cada campo que deve ser validado:
 *
 * <form>
 *   <input name="nome" data-validar-campo required>
 *   <input name="email" type="email" data-validar-campo required>
 *   <input name="site" data-validar-campo data-validar-url>
 *   <input name="cnpj" data-validar-campo data-mascara="cnpj" required>
 *   <button type="submit">Salvar</button>
 * </form>
 *
 * Validações disponíveis:
 * - `required`: impede campo obrigatório vazio.
 * - `type="email"`: verifica o formato do e-mail.
 * - `data-mascara="cpf"`, `"cnpj"` ou `"telefone"`: valida comprimento;
 *   CPF e CNPJ também são conferidos quando `window.Validador` está disponível.
 * - `data-validar-url`: exige que o valor comece com `http://` ou `https://`.
 *   Campos opcionais vazios continuam válidos.
 * - Campos `type="file"` obrigatórios aceitam uma imagem já exibida com a
 *   classe `comFoto` como preenchimento.
 *
 * A validação é conectada automaticamente ao envio do formulário. Para
 * formulários em etapas, o botão de avanço deve estar dentro de uma section
 * do formulário e chamar uma função cujo nome contenha "avancar", "avançar",
 * "next" ou "proximo", por exemplo:
 *
 * <button type="button" onclick="avancarCadastro()">Avançar</button>
 *
 * Os erros são exibidos junto ao campo. Ao editar o campo, o erro é removido;
 * também são limpos quando o formulário é resetado ou o modal é fechado.
 */

(function () {
    'use strict';

    const SELETOR_CAMPO = '[data-validar-campo]';
    const SELETOR_WRAPPER = '.inputWrapperParceiros, .inputWrapper, .wrapperDescLogoParceiro, .dadoLinkedin, .dadoInstagramMentor';
    const CLASSE_ERRO = 'erroCampo';
    const formsInicializados = new WeakSet();

    const MENSAGENS = {
        obrigatorio: 'Campo obrigatório.',
        incompleto: 'Preencha o campo por completo.',
        email: 'Informe um e-mail válido.',
        url: 'O site deve começar com http:// ou https://.',
        cpf: 'CPF inválido.',
        cnpj: 'CNPJ inválido.'
    };

    function obterWrapper(campo) {
        return campo.closest(SELETOR_WRAPPER);
    }

    function obterErro(campo) {
        const wrapper = obterWrapper(campo);

        if (wrapper) {
            return wrapper.querySelector(`.${CLASSE_ERRO}`);
        }

        return campo.nextElementSibling?.classList.contains(CLASSE_ERRO) 
            ? campo.nextElementSibling
            : null;
    }

    function limparErro(campo) {
        obterErro(campo)?.remove();
    }

    function limparErros(formulario) {
        formulario.querySelectorAll(`.${CLASSE_ERRO}`).forEach(erro => erro.remove());
    }

    function mensagemDoCampo(campo) {
        if (campo.validity.valueMissing) {
            const imagemExistente = campo.type === 'file'
                && campo.closest(SELETOR_WRAPPER)?.querySelector('img.comFoto');

            if (campo.files?.length || imagemExistente) {
                return '';
            }

            return MENSAGENS.obrigatorio;
        }

        if (campo.value === '') return '';

        if (
            campo.dataset.validarUrl !== undefined
            && !/^https?:\/\//i.test(campo.value.trim())
        ) {
            return MENSAGENS.url;
        }

        if (campo.validity.typeMismatch && campo.type === 'email') {
            return MENSAGENS.email;
        }

        if (!campo.validity.valid) return MENSAGENS.incompleto;

        const tamanhos = {
            cpf: [11],
            cnpj: [14],
            telefone: [10, 11]
        }[campo.dataset.mascara];

        if (tamanhos) {
            const valor = window.Mascara?.remover(campo)
                ?? campo.value.replace(/[^A-Za-z0-9]/g, '');

            if (!tamanhos.includes(valor.length)) return MENSAGENS.incompleto;
        }

        if (campo.dataset.mascara === 'cpf' && window.Validador?.cpf(campo.value) === false) {
            return MENSAGENS.cpf;
        }

        if (campo.dataset.mascara === 'cnpj' && window.Validador?.cnpj(campo.value) === false) {
            return MENSAGENS.cnpj;
        }

        return '';
    }

    function campoVisivel(campo) {
        const selectCustomizado = campo.matches('select')
            ? campo.closest('.selectCustomizadoParceiros')
            : null;

        if (campo.hidden && !selectCustomizado) return false;

        for (let elemento = campo.parentElement; elemento; elemento = elemento.parentElement) {
            if (elemento.hidden) return false;

            const estilo = window.getComputedStyle(elemento);
            if (estilo.display === 'none' || estilo.visibility === 'hidden') return false;
        }

        return true;
    }

    function focarCampo(campo) {
        const botaoSelect = campo.matches('select')
            ? campo.closest('.selectCustomizadoParceiros')?.querySelector('.selecionadoSelectParceiros')
            : null;

        (botaoSelect || campo).focus();
    }

    function mostrarErro(campo, mensagem) {
        const wrapper = obterWrapper(campo);
        let erro = obterErro(campo);

        if (!erro) {
            erro = document.createElement('span');
            erro.className = `${CLASSE_ERRO} erroCampoParceiro`;

            if (wrapper) {
                const label = wrapper.querySelector('.labelAddParceiros')
                    || wrapper.querySelector('label')
                    || wrapper;
                label.appendChild(erro);
            } else {
                campo.insertAdjacentElement('afterend', erro);
            }
        }

        erro.textContent = mensagem;
    }

    function validarCampos(raiz) {
        const campos = raiz.querySelectorAll(SELETOR_CAMPO);
        let primeiroPendente = null;

        for (const campo of campos) {
            if (!campoVisivel(campo)) continue;

            const mensagem = mensagemDoCampo(campo);

            if (!mensagem) {
                limparErro(campo);
                continue;
            }

            mostrarErro(campo, mensagem);
            primeiroPendente = primeiroPendente ?? campo;
        }

        if (primeiroPendente) focarCampo(primeiroPendente);
        return primeiroPendente === null;
    }

    function inicializar() {
        document.querySelectorAll(SELETOR_CAMPO).forEach(campo => {
            const formulario = campo.closest('form');
            if (!formulario || formsInicializados.has(formulario)) return;

            formsInicializados.add(formulario);
            formulario.noValidate = true;
            formulario.addEventListener('reset', () => limparErros(formulario));
        });
    }

    document.addEventListener('click', evento => {
        const botao = evento.target.closest?.('button[onclick], input[onclick], button.btnAvancarCard');
        const acao = botao?.getAttribute('onclick') || '';
        const ehBotaoAvancar = botao?.matches('button.btnAvancarCard');
        if (!botao || (!ehBotaoAvancar && !/(avanc|avanç|next|proxim|próxim)/i.test(acao))) return;

        const secao = botao.closest('section');
        const formulario = botao.closest('form');

        if (secao && formulario && !validarCampos(secao)) {
            evento.preventDefault();
            evento.stopPropagation();
        }
    }, true);

    document.addEventListener('submit', evento => {
        const formulario = evento.target.closest?.('form');
        if (!formulario || !formulario.querySelector(SELETOR_CAMPO)) return;

        if (!validarCampos(formulario)) {
            evento.preventDefault();
            evento.stopPropagation();
        }
    }, true);

    document.addEventListener('input', evento => {
        if (evento.target.matches?.(SELETOR_CAMPO)) limparErro(evento.target);
    });

    document.addEventListener('change', evento => {
        if (evento.target.matches?.(SELETOR_CAMPO)) limparErro(evento.target);
    });

    document.addEventListener('close', evento => {
        evento.target.querySelectorAll?.('form').forEach(limparErros);
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar, { once: true });
    } else {
        inicializar();
    }

})();
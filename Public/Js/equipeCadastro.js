document.addEventListener('DOMContentLoaded', () => window.mostrarModalSalvo?.());

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('#cadastroEquipe');
    const formulario = modal?.querySelector('form');
    const fluxo = modal?.querySelector('.equipeFormulario');
    if (!modal || !formulario || !fluxo) {
        return;
    }
    const passos = [...fluxo.querySelectorAll('.equipePasso')];
    const voltar = fluxo.querySelector('.equipeVoltar');
    const avancar = fluxo.querySelector('.equipeAvancar');
    const salvar = fluxo.querySelector('.equipeSalvarBtn');
    const idEquipe = fluxo.querySelector('#idEquipe');
    const acaoCadastro = formulario.action;
    const acaoAtualizacao = acaoCadastro.replace(/\/cadastrar$/, '/atualizar');
    const endpointBuscaEquipe = acaoCadastro.replace(/\/cadastrar$/, '/buscar');
    const areaFotoEquipe = fluxo.querySelector('.equipeFotoEdicao');
    formulario.noValidate = true;

    const mensagens = {
        obrigatorio: 'Campo obrigatório.',
        incompleto: 'Preencha o campo por completo.',
        email: 'Informe um e-mail válido.',
        cpf: 'Informe um CPF válido.',
        cpfDuplicado: 'Este CPF já está cadastrado.'
    };

    function padronizarTexto(valor) {
        return valor.replace(/\p{L}[\p{L}\p{M}]*/gu, (palavra) =>
            palavra.charAt(0).toLocaleUpperCase('pt-BR')
            + palavra.slice(1).toLocaleLowerCase('pt-BR')
        );
    }

    function campoDeTextoPadronizavel(campo) {
        return campo instanceof HTMLInputElement
            && campo.type === 'text'
            && !campo.dataset.mascara
            && campo.name !== 'email';
    }

    function padronizarCampo(campo, manterCursor = false) {
        if (!campoDeTextoPadronizavel(campo)) return;

        const inicio = manterCursor ? campo.selectionStart : null;
        const prefixo = inicio === null ? '' : padronizarTexto(campo.value.slice(0, inicio));
        campo.value = padronizarTexto(campo.value);
        if (inicio !== null) campo.setSelectionRange(prefixo.length, prefixo.length);
    }

    function erroDoCampo(campo) {
        if (campo.validity.valueMissing) {
            return mensagens.obrigatorio;
        }

        if (campo.type === 'email' && campo.validity.typeMismatch) {
            return mensagens.email;
        }

        if (campo.dataset.mascara) {
            const quantidadeDigitos = campo.value.replace(/\D/g, '').length;
            const tamanhoEsperado = campo.dataset.mascara === 'cpf'
                ? 11
                : (quantidadeDigitos >= 11 ? 11 : 10);

            if (quantidadeDigitos !== tamanhoEsperado) {
                return mensagens.incompleto;
            }
            const cpfOriginal = formulario.dataset.cpfOriginal ?? '';
            const cpfSemAlteracao = campo.dataset.mascara === 'cpf'
                && idEquipe.value
                && campo.value.replace(/\D/g, '') === cpfOriginal;
            if (campo.dataset.mascara === 'cpf' && !cpfSemAlteracao && window.Validador && !window.Validador.cpf(campo.value)) {
                return mensagens.cpf;
            }
            if (campo.dataset.mascara === 'cpf' && !cpfSemAlteracao && campo.dataset.cpfExiste === 'true') {
                return mensagens.cpfDuplicado;
            }
        }

        return campo.validity.valid ? '' : mensagens.incompleto;
    }

    function mostrarErro(alvo, mensagem, seletor = '.equipeRotuloCampo') {
        if (!alvo) {
            return;
        }

        let erro = alvo.querySelector('.equipeErroCampo');
        if (!erro) {
            erro = document.createElement('span');
            erro.className = 'equipeErroCampo';
            (alvo.querySelector(seletor) ?? alvo).appendChild(erro);
        }

        erro.textContent = mensagem;
    }

    function limparErro(alvo) {
        alvo?.querySelector('.equipeErroCampo')?.remove();
    }

    function limparErros(passo) {
        passo.querySelectorAll('.equipeErroCampo').forEach((erro) => erro.remove());
    }

    function validarPasso(indice) {
        const passo = passos[indice - 1];
        limparErros(passo);
        let primeiroCampoInvalido = null;

        if (indice === 1 && !idEquipe.value && !entradaFoto.files?.length) {
            mostrarErro(
                fluxo.querySelector('.equipeFotoContainer'),
                mensagens.obrigatorio
            );
            primeiroCampoInvalido = fluxo.querySelector('.equipeAddCard');
        }

        for (const campo of passo.querySelectorAll('[required]')) {
            const mensagem = erroDoCampo(campo);
            if (mensagem) {
                mostrarErro(campo.closest('.equipeCampoCard'), mensagem);
                primeiroCampoInvalido = primeiroCampoInvalido ?? campo;
            }
        }

        for (const grupo of passo.querySelectorAll('[data-required-group]')) {
            if (!grupo.querySelector('input:checked')) {
                mostrarErro(grupo, mensagens.obrigatorio, '.equipeGrupoTitulo');
                primeiroCampoInvalido = primeiroCampoInvalido ?? grupo.querySelector('input');
            }
        }

        primeiroCampoInvalido?.focus();
        return primeiroCampoInvalido === null;
    }

    function mostrarPasso(indice) {
        fluxo.dataset.step = String(indice);
        passos.forEach((passo, i) => {
            passo.hidden = i + 1 !== indice;
        });
        fluxo.querySelectorAll('[data-step-indicator]').forEach((etapa) => {
            const numeroEtapa = Number(etapa.dataset.stepIndicator);
            const ativa = numeroEtapa === indice;
            etapa.classList.toggle('ativa', ativa);
            etapa.classList.toggle(
                'concluida',
                numeroEtapa < indice
            );
        });
        fluxo.querySelectorAll('[data-step-line]').forEach((linha) => {
            linha.classList.toggle(
                'concluida',
                Number(linha.dataset.stepLine) < indice
            );
        });
        voltar.hidden = indice === 1;
        avancar.hidden = indice === passos.length;
        salvar.hidden = indice !== passos.length;
    }

    avancar.addEventListener('click', () => {
        const atual = Number(fluxo.dataset.step);
        if (validarPasso(atual)) mostrarPasso(atual + 1);
    });
    voltar.addEventListener('click', () => {
        mostrarPasso(Number(fluxo.dataset.step) - 1);
    });
    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        formulario.querySelectorAll('input[type="text"]').forEach((campo) => padronizarCampo(campo));
        for (let indice = 1; indice <= passos.length; indice++) {
            mostrarPasso(indice);
            if (!validarPasso(indice)) {
                return;
            }
        }

        const avisoEnvio = fluxo.querySelector('.equipeErroEnvio');
        avisoEnvio.hidden = true;
        avisoEnvio.textContent = '';
        salvar.disabled = true;
        const textoOriginal = salvar.textContent;
        salvar.textContent = 'SALVANDO...';

        try {
            const resposta = await fetch(formulario.action, {
                method: 'POST',
                body: new FormData(formulario),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const corpoResposta = (await resposta.text()).trim();
            if (resposta.ok) {
                try {
                    const resultado = JSON.parse(corpoResposta);
                    if (resultado.sucesso && resultado.redirect) {
                        window.location.assign(resultado.redirect);
                        return;
                    }
                } catch (erro) {
                    // Mantém a resposta do servidor como mensagem de erro abaixo.
                }
            }

            const mensagem = corpoResposta;
            avisoEnvio.textContent = mensagem || 'Não foi possível salvar o cadastro. Confira os dados e tente novamente.';
            avisoEnvio.hidden = false;
            avisoEnvio.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } catch (erro) {
            avisoEnvio.textContent = 'Não foi possível conectar ao servidor. Tente salvar novamente.';
            avisoEnvio.hidden = false;
        } finally {
            salvar.disabled = false;
            salvar.textContent = textoOriginal;
        }
    });

    formulario.addEventListener('focusout', (evento) => {
        const campo = evento.target;
        padronizarCampo(campo);
        if (!campo.matches('input[required], select[required], textarea[required]')) {
            return;
        }

        const mensagem = erroDoCampo(campo);
        const wrapper = campo.closest('.equipeCampoCard');
        if (mensagem) mostrarErro(wrapper, mensagem);
        else limparErro(wrapper);

        if (campo.name === 'cpf' && !mensagem && campo.dataset.cpfUrl) {
            const cpfConsultado = campo.value.replace(/\D/g, '');
            if (idEquipe.value && cpfConsultado === (formulario.dataset.cpfOriginal ?? '')) return;
            const ignorarId = idEquipe.value ? `&ignorar=${encodeURIComponent(idEquipe.value)}` : '';
            fetch(`${campo.dataset.cpfUrl}?cpf=${encodeURIComponent(cpfConsultado)}${ignorarId}`)
                .then((resposta) => resposta.json())
                .then((resultado) => {
                    if (campo.value.replace(/\D/g, '') !== cpfConsultado) return;
                    campo.dataset.cpfExiste = resultado.existe ? 'true' : 'false';
                    if (resultado.existe) mostrarErro(wrapper, mensagens.cpfDuplicado);
                    else if (wrapper.querySelector('.equipeErroCampo')?.textContent === mensagens.cpfDuplicado) limparErro(wrapper);
                })
                .catch(() => {});
        }
    });

    formulario.addEventListener('input', (evento) => {
        padronizarCampo(evento.target, true);
        if (evento.target.name === 'cpf') evento.target.dataset.cpfExiste = 'false';
        limparErro(evento.target.closest('.equipeCampoCard'));
    });

    formulario.addEventListener('change', (evento) => {
        const grupo = evento.target.closest('[data-required-group]');
        if (grupo?.querySelector('input:checked')) limparErro(grupo);
        limparErro(evento.target.closest('.equipeCampoCard'));
    });

    const entradaFoto = fluxo.querySelector('#inputFoto');
    const modalRecorte = fluxo.querySelector('.equipeModalRecorte');
    const canvasRecorte = fluxo.querySelector('.equipeCanvasRecorte');
    const contextoRecorte = canvasRecorte.getContext('2d');
    const zoomRecorte = fluxo.querySelector('.equipeZoomRecorte');
    const imagemRecorte = new Image();
    const tamanhoRecorte = 280;
    canvasRecorte.width = tamanhoRecorte;
    canvasRecorte.height = tamanhoRecorte;
    let escalaRecorte = 1;
    let deslocamentoX = 0;
    let deslocamentoY = 0;
    let arrasto = null;
    let urlTemporaria = '';
    let urlPrevia = '';
    let arquivoAnterior = null;

    function desenharRecorte() {
        if (!imagemRecorte.naturalWidth) return;
        const escalaBase = Math.max(tamanhoRecorte / imagemRecorte.naturalWidth, tamanhoRecorte / imagemRecorte.naturalHeight);
        const escala = escalaBase * escalaRecorte;
        const largura = imagemRecorte.naturalWidth * escala;
        const altura = imagemRecorte.naturalHeight * escala;
        const limiteX = Math.max(0, (largura - tamanhoRecorte) / 2);
        const limiteY = Math.max(0, (altura - tamanhoRecorte) / 2);
        deslocamentoX = Math.max(-limiteX, Math.min(limiteX, deslocamentoX));
        deslocamentoY = Math.max(-limiteY, Math.min(limiteY, deslocamentoY));
        contextoRecorte.clearRect(0, 0, tamanhoRecorte, tamanhoRecorte);
        contextoRecorte.drawImage(imagemRecorte, (tamanhoRecorte - largura) / 2 + deslocamentoX, (tamanhoRecorte - altura) / 2 + deslocamentoY, largura, altura);
    }

    entradaFoto.addEventListener('change', () => {
        const arquivo = entradaFoto.files?.[0];
        if (!arquivo) return;
        arquivoAnterior = formulario.querySelector('input[name="foto"]')?._arquivoConfirmado ?? null;
        limparErro(fluxo.querySelector('.equipeFotoContainer'));
        if (!arquivo.type.startsWith('image/')) {
            alert('Selecione um arquivo de imagem.');
            if (arquivoAnterior) {
                const arquivos = new DataTransfer();
                arquivos.items.add(arquivoAnterior);
                entradaFoto.files = arquivos.files;
            } else {
                entradaFoto.value = '';
            }
            return;
        }
        if (urlTemporaria) URL.revokeObjectURL(urlTemporaria);
        urlTemporaria = URL.createObjectURL(arquivo);
        imagemRecorte.onload = () => {
            escalaRecorte = 1;
            deslocamentoX = 0;
            deslocamentoY = 0;
            zoomRecorte.value = '1';
            desenharRecorte();
            modalRecorte.hidden = false;
            zoomRecorte.focus();
        };
        imagemRecorte.src = urlTemporaria;
    });

    zoomRecorte.addEventListener('input', () => {
        escalaRecorte = Number(zoomRecorte.value);
        desenharRecorte();
    });
    canvasRecorte.addEventListener('pointerdown', (evento) => {
        arrasto = { x: evento.clientX, y: evento.clientY, deslocamentoX, deslocamentoY };
        canvasRecorte.setPointerCapture(evento.pointerId);
    });
    canvasRecorte.addEventListener('pointermove', (evento) => {
        if (!arrasto) return;
        const escalaVisual = tamanhoRecorte / canvasRecorte.getBoundingClientRect().width;
        deslocamentoX = arrasto.deslocamentoX + (evento.clientX - arrasto.x) * escalaVisual;
        deslocamentoY = arrasto.deslocamentoY + (evento.clientY - arrasto.y) * escalaVisual;
        desenharRecorte();
    });
    canvasRecorte.addEventListener('pointerup', () => {
        arrasto = null;
    });
    canvasRecorte.addEventListener('pointercancel', () => {
        arrasto = null;
    });

    fluxo.querySelector('.equipeCancelarRecorte').addEventListener('click', () => {
        modalRecorte.hidden = true;
        if (arquivoAnterior) {
            const arquivos = new DataTransfer();
            arquivos.items.add(arquivoAnterior);
            entradaFoto.files = arquivos.files;
        } else {
            entradaFoto.value = '';
        }
    });
    fluxo.querySelector('.equipeAplicarRecorte').addEventListener('click', () => {
        const escalaBase = Math.max(tamanhoRecorte / imagemRecorte.naturalWidth, tamanhoRecorte / imagemRecorte.naturalHeight);
        const escala = escalaBase * escalaRecorte;
        const larguraCorte = tamanhoRecorte / escala;
        const alturaCorte = tamanhoRecorte / escala;
        const origemX = Math.max(0, Math.min(imagemRecorte.naturalWidth - larguraCorte, (imagemRecorte.naturalWidth - larguraCorte) / 2 - deslocamentoX / escala));
        const origemY = Math.max(0, Math.min(imagemRecorte.naturalHeight - alturaCorte, (imagemRecorte.naturalHeight - alturaCorte) / 2 - deslocamentoY / escala));
        const resolucaoSaida = Math.max(1, Math.floor(Math.min(larguraCorte, alturaCorte)));
        const canvasFinal = document.createElement('canvas');
        canvasFinal.width = resolucaoSaida;
        canvasFinal.height = resolucaoSaida;
        canvasFinal.getContext('2d').drawImage(
            imagemRecorte,
            origemX,
            origemY,
            larguraCorte,
            alturaCorte,
            0,
            0,
            resolucaoSaida,
            resolucaoSaida
        );
        canvasFinal.toBlob((blob) => {
            if (!blob) return;
            const arquivoRecortado = new File([blob], 'foto-perfil.png', { type: 'image/png' });
            const arquivos = new DataTransfer();
            arquivos.items.add(arquivoRecortado);
            entradaFoto.files = arquivos.files;
            entradaFoto._arquivoConfirmado = arquivoRecortado;
            const imagem = document.createElement('img');
            imagem.alt = 'Prévia da foto de perfil recortada';
            if (urlTemporaria) URL.revokeObjectURL(urlTemporaria);
            urlTemporaria = '';
            if (urlPrevia) URL.revokeObjectURL(urlPrevia);
            urlPrevia = URL.createObjectURL(blob);
            imagem.src = urlPrevia;
            const quadro = fluxo.querySelector('.equipeFotoCard');
            quadro.replaceChildren(imagem);
            modalRecorte.hidden = true;
        }, 'image/png');
    });

    modal.addEventListener('close', () => {
        formulario.reset();
        passos.forEach(limparErros);
        const avisoEnvio = fluxo.querySelector('.equipeErroEnvio');
        avisoEnvio.hidden = true;
        avisoEnvio.textContent = '';
        idEquipe.value = '';
        delete formulario.dataset.cpfOriginal;
        formulario.elements.namedItem('cpf').dataset.cpfExiste = 'false';
        formulario.action = acaoCadastro;
        areaFotoEquipe.hidden = false;
        modalRecorte.hidden = true;
        ajustarTituloModal('Cadastrar Equipe');
        entradaFoto._arquivoConfirmado = null;
        if (urlTemporaria) URL.revokeObjectURL(urlTemporaria);
        if (urlPrevia) URL.revokeObjectURL(urlPrevia);
        urlTemporaria = '';
        urlPrevia = '';
        const quadro = fluxo.querySelector('.equipeFotoCard');
        quadro.innerHTML = '<i class="fa-solid fa-user"></i>';
        mostrarPasso(1);
    });
    window.Mascara?.formatar(formulario);

    function ajustarTituloModal(titulo) {
        const heading = modal.querySelector('.modalTitulo');
        const textos = [...heading.childNodes].filter((node) => node.nodeType === Node.TEXT_NODE);
        if (textos.length) textos[textos.length - 1].textContent = ` ${titulo}`;
    }

    document.addEventListener('click', (evento) => {
        if (!evento.target.closest('.abrirModalEquipe')) return;
        formulario.reset();
        passos.forEach(limparErros);
        const avisoEnvio = fluxo.querySelector('.equipeErroEnvio');
        avisoEnvio.hidden = true;
        avisoEnvio.textContent = '';
        idEquipe.value = '';
        delete formulario.dataset.cpfOriginal;
        formulario.elements.namedItem('cpf').dataset.cpfExiste = 'false';
        formulario.action = acaoCadastro;
        areaFotoEquipe.hidden = false;
        ajustarTituloModal('Cadastrar Equipe');
        mostrarPasso(1);
    });

    async function abrirEdicaoEquipe(id) {
        try {
            const resposta = await fetch(`${endpointBuscaEquipe}?id=${encodeURIComponent(id)}`);
            const resultado = await resposta.json();
            if (!resposta.ok || !resultado.sucesso || !resultado.membro) {
                throw new Error(resultado.erro || 'Não foi possível carregar os dados da equipe.');
            }

            const membro = resultado.membro;
            formulario.reset();
            passos.forEach(limparErros);
            idEquipe.value = membro.id;
            formulario.dataset.cpfOriginal = String(membro.cpf ?? '').replace(/\D/g, '');
            formulario.elements.namedItem('cpf').dataset.cpfExiste = 'false';
            formulario.action = acaoAtualizacao;
            areaFotoEquipe.hidden = true;
            const cargoSelect = formulario.elements.namedItem('cargo');
            if (![...cargoSelect.options].some((opcao) => opcao.value === membro.cargo)) {
                cargoSelect.add(new Option(membro.cargo, membro.cargo));
            }
            ['nome', 'email', 'cpf', 'telefone', 'perfil', 'cargo', 'modalidade'].forEach((nome) => {
                const campo = formulario.elements.namedItem(nome);
                if (campo) campo.value = nome === 'modalidade'
                    ? String(membro.modalidade ?? '').toLowerCase()
                    : (membro[nome] ?? '');
            });
            fluxo.querySelectorAll('input[name="diaSemana[]"]').forEach((campo) => {
                campo.checked = Number(membro[campo.value]) === 1;
            });
            fluxo.querySelectorAll('input[name="horario[]"]').forEach((campo) => {
                campo.checked = Number(membro[campo.value]) === 1;
            });
            window.Mascara?.formatar(formulario);
            fluxo.querySelector('.equipeErroEnvio').hidden = true;
            ajustarTituloModal('Editar Equipe');
            mostrarPasso(1);
            abrirModal('cadastroEquipe');
        } catch (erro) {
            alert(erro.message || 'Erro ao carregar os dados da equipe.');
        }
    }

    document.addEventListener('click', (evento) => {
        const botaoEditar = evento.target.closest('.equipeBotaoEditar');
        if (botaoEditar) abrirEdicaoEquipe(botaoEditar.dataset.id);
    });
});


const ENDPOINT_BUSCAR_PARCEIRO = '/ParkTec/parceiros/buscar';
const ACAO_ADICIONAR_PARCEIRO = '/ParkTec/parceiros/adicionar';
const ACAO_ATUALIZAR_PARCEIRO = '/ParkTec/parceiros/atualizar';
const UPLOAD_PARCEIROS = '/ParkTec/Public/Upload/Parceiros/';

const CAMPOS_PARCEIRO = {
    nome: 'nome',
    cnpj: 'cnpj',
    telefone: 'telefone',
    instagram: 'instagram',
    site: 'site',
    linkedin: 'linkedin',
    nomeRepresentante: 'nome_representante',
    telefoneRepresentante: 'telefone_representante',
    cargoRepresentante: 'cargo_representante',
    emailRepresentante: 'email_representante',
    idAreaAtuacao: 'id_area_atuacao'
};

function inicializarCrudParceiro() {

    const modalParceiro = document.getElementById('modalCadastroParceiro');
    const formParceiro = modalParceiro?.querySelector('form');
    const inputIdParceiro = document.getElementById('idParceiro');
    const inputFoto = document.getElementById('foto');
    const previewFoto = window.PreviewImagem.criar(inputFoto,document.getElementById('previewFotoParceiro'));
    const selectArea = document.getElementById('idAreaAtuacao');
    const inputNovaArea = document.getElementById('novaAreaAtuacao');
    const botaoNovaArea = modalParceiro?.querySelector('.botaoAreaAtuacao');

    if (!modalParceiro || !formParceiro) {
        return;
    }

    const secaoEmpresa = modalParceiro.querySelector('.secaoDadosEmpresa');
    const secaoRepresentante = modalParceiro.querySelector('.secaoRepresentanteLegal');
    const etapasParceiro = modalParceiro.querySelectorAll('.etapaParceiro');

    function mostrarErroTipoFoto(mostrar) {
        const legendaFoto = inputFoto?.closest('.wrapperDescLogoParceiro')?.querySelector('.labelLogoEmpresaParceira');
        let erro = legendaFoto?.querySelector('.erroTipoFotoParceiro');

        if (!mostrar) {
            erro?.remove();
            return;
        }

        if (!legendaFoto) {
            return;
        }

        if (!erro) {
            erro = document.createElement('span');
            erro.className = 'erroCampoParceiro erroTipoFotoParceiro';
            erro.setAttribute('role', 'alert');
            legendaFoto.appendChild(erro);
        }

        erro.textContent = 'Selecione uma imagem nos formatos JPG, JPEG, PNG ou WEBP.';
    }

    function validarTipoFoto() {
        const arquivo = inputFoto?.files?.[0];

        if (!arquivo) {
            mostrarErroTipoFoto(false);
            return true;
        }

        const extensao = arquivo.name.split('.').pop()?.toLowerCase();
        const extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        const valido = extensoesPermitidas.includes(extensao)
            && tiposPermitidos.includes(arquivo.type.toLowerCase());

        if (!valido) {
            previewFoto.mostrar('');
            mostrarErroTipoFoto(true);
            return false;
        }

        mostrarErroTipoFoto(false);
        return true;
    }

    inputFoto?.addEventListener('input', validarTipoFoto);
    inputFoto?.addEventListener('change', validarTipoFoto);

    modalParceiro.addEventListener('close', () => {
        if (inputFoto?.files?.[0] && !validarTipoFoto()) {
            inputFoto.value = '';
            previewFoto.mostrar('');
        }

        mostrarErroTipoFoto(false);
    });

    function caixaDaLista() {
        return selectArea?.closest('.selectCustomizadoParceiros') ?? selectArea;
    }

    function mostrarCampoNovaArea(mostrar) {
        const caixa = caixaDaLista();

        if (!caixa || !inputNovaArea) {
            return;
        }

        caixa.hidden = mostrar;
        inputNovaArea.hidden = !mostrar;

        // a área é obrigatória dos dois jeitos: required acompanha quem está na tela
        inputNovaArea.required = mostrar;

        if (selectArea) {
            selectArea.required = !mostrar;
        }

        if (botaoNovaArea) {
            botaoNovaArea.classList.toggle('girado', mostrar);

            botaoNovaArea.title = mostrar ? 'Escolher uma área já cadastrada' : 'Cadastrar nova área';
        }

        if (mostrar) {
            inputNovaArea.focus();

            return;
        }

        inputNovaArea.value = '';
    }

    if (botaoNovaArea && inputNovaArea) {
        botaoNovaArea.addEventListener('click', () => {
            mostrarCampoNovaArea(inputNovaArea.hidden);
        });
    }

    /** Trilha do topo: escuro na etapa da vez, verde nas que já ficaram para tras. */
    function marcarEtapa(numeroAtual) {
        etapasParceiro.forEach(etapa => {
            const numero = Number(etapa.dataset.etapa);

            etapa.classList.toggle('etapaAtivaParceiro', numero === numeroAtual);

            etapa.classList.toggle('etapaConcluidaParceiro', numero < numeroAtual);
        });
    }

    function mostrarEtapa(secao) {
        secaoEmpresa.classList.toggle('active', secao === secaoEmpresa);

        secaoRepresentante.classList.toggle('active', secao === secaoRepresentante);

        marcarEtapa(secao === secaoRepresentante ? 2 : 1);
    }

    function avancarParceiro() {
        if (!validarTipoFoto()) {
            return;
        }

        mostrarEtapa(secaoRepresentante);
    }

    function voltarParceiro() {
        mostrarEtapa(secaoEmpresa);
    }

    formParceiro.addEventListener('keydown', (evento) => {
        if (
            evento.key !== 'Enter' ||
            !secaoEmpresa.classList.contains('active') ||
            !(evento.target instanceof HTMLInputElement)
        ) {
            return;
        }

        evento.preventDefault();
        secaoEmpresa.querySelector('.areaBotaoEmpresaParceira .btnSalvar')?.click();
    });

    formParceiro.addEventListener('submit', (evento) => {
        if (!validarTipoFoto()) {
            evento.preventDefault();
            return;
        }

        if (secaoEmpresa.classList.contains('active')) {
            evento.preventDefault();
        }
    });

    envioFormulario(formParceiro);

    formParceiro.addEventListener('envioSucesso', () => {
        resetarModalParceiro();
        if (typeof window.recarregarTabela === 'function') {
            window.recarregarTabela();
        }
    });

    formParceiro.addEventListener('envioErro', (evento) => {
        const mensagem = evento.detail?.error?.message || 'Não foi possível salvar o parceiro.';
        alert(mensagem);
    });

    // os botões do modal chamam essas duas pelo onclick
    window.avancarParceiro = avancarParceiro;
    window.voltarParceiro = voltarParceiro;

    function trocarTituloModal(texto) {
        const titulo = modalParceiro.querySelector('.modalTitulo, .modal-titulo');

        if (!titulo) {
            return;
        }

        const noTexto = Array.from(titulo.childNodes)
            .reverse()
            .find(no => no.nodeType === Node.TEXT_NODE && no.nodeValue.trim() !== '');

        if (noTexto) {
            noTexto.nodeValue = ` ${texto} `;
        }
    }

    function resetarModalParceiro() {
        formParceiro.reset();

        if (inputIdParceiro) {
            inputIdParceiro.value = '';
        }

        if (inputFoto) {
            inputFoto.value = '';
            inputFoto.required = true;
        }

        formParceiro.action = ACAO_ADICIONAR_PARCEIRO;

        previewFoto.mostrar('');

        mostrarCampoNovaArea(false);

        voltarParceiro();

        trocarTituloModal('Adicionar Parceiros');
    }

    function preencherModalParceiro(parceiro) {
        formParceiro.reset();

        Object.entries(CAMPOS_PARCEIRO).forEach(([idCampo, chave]) => {
            const campo = document.getElementById(idCampo);

            if (campo) {
                campo.value = parceiro[chave] ?? '';

                if (campo.tagName === 'SELECT') {
                    campo.dispatchEvent(new Event('change'));
                }
            }
        });

        window.Mascara?.formatar(formParceiro);

        if (inputIdParceiro) {
            inputIdParceiro.value = parceiro.id;
        }

        if (inputFoto) {
            inputFoto.value = '';
            inputFoto.required = !parceiro.foto;
        }

        mostrarCampoNovaArea(false);

        previewFoto.mostrar(parceiro.foto ? `${UPLOAD_PARCEIROS}${parceiro.foto}` : '');

        voltarParceiro();
    }

    async function abrirEdicaoParceiro(id) {
        try {
            const resposta = await fetch(`${ENDPOINT_BUSCAR_PARCEIRO}?id=${encodeURIComponent(id)}`);
            const dados = await resposta.json();

            if (!dados.sucesso || !dados.parceiro) {
                alert(dados.erro || 'Não foi possível carregar esse parceiro.');

                return;
            }

            preencherModalParceiro(dados.parceiro);

            formParceiro.action = ACAO_ATUALIZAR_PARCEIRO;

            trocarTituloModal('Editar Parceiro');

            abrirModal('modalCadastroParceiro');
        } catch (erro) {
            alert('Erro de conexão ao carregar o parceiro.');
        }
    }

    const imgVisFoto = document.getElementById('visFotoParceiro');
    const iconeVisFoto = document.getElementById('visIconeFotoParceiro');

    function normalizarUrl(url) {
        const limpo = String(url ?? '').trim();
        if (!limpo) {
            return '';
        }
        return /^https?:\/\//i.test(limpo) ? limpo : `https://${limpo}`;
    }

    function extrairHandleInstagram(url) {
        const limpo = String(url ?? '').trim();
        if (!limpo) {
            return '';
        }
        if (limpo.startsWith('@')) {
            return limpo;
        }
        const match = limpo.match(/instagram\.com\/([^/?#]+)/i);
        return match?.[1] ? `@${match[1]}` : '';
    }

    function mostrarFotoVisualizacao(urlFoto) {
        if (!imgVisFoto || !iconeVisFoto) {
            return;
        }

        if (!urlFoto) {
            imgVisFoto.removeAttribute('src');
            imgVisFoto.hidden = true;
            iconeVisFoto.hidden = false;
            return;
        }

        imgVisFoto.onerror = () => {
            imgVisFoto.onerror = null;
            imgVisFoto.hidden = true;
            iconeVisFoto.hidden = false;
        };
        imgVisFoto.src = urlFoto;
        imgVisFoto.hidden = false;
        iconeVisFoto.hidden = true;
    }

    function definirTexto(id, valor) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = valor ?? '';
        }
    }

    function definirLink(id, valor, prefixoHref = '', textoExibicao = '') {
        const el = document.getElementById(id);
        if (!el) {
            return;
        }
        const limpo = String(valor ?? '').trim();
        if (!limpo) {
            el.textContent = '—';
            el.removeAttribute('href');
            return;
        }
        const texto = String(textoExibicao ?? '').trim() || limpo;
        el.textContent = texto;
        el.href = prefixoHref ? `${prefixoHref}${limpo}` : normalizarUrl(limpo);
    }

    function preencherVisualizarParceiro(parceiro) {
        definirTexto('visNomeParceiro', parceiro.nome);
        definirTexto('visCnpjParceiro', mascaraCnpj(parceiro.cnpj));
        definirTexto('visTelefoneParceiro', mascaraTelefone(parceiro.telefone));
        definirTexto('visAreaAtuacaoParceiro', parceiro.area_nome);

        const elStatus = document.getElementById('visStatusParceiro');
        if (elStatus) {
            const ativo = Number(parceiro.ativo ?? 1) === 1;
            elStatus.textContent = ativo ? 'Ativo' : 'Inativo';
            elStatus.classList.toggle('inativo', !ativo);
        }

        definirLink('visSiteParceiro', parceiro.site);
        definirLink(
            'visInstagramParceiro',
            parceiro.instagram,
            '',
            extrairHandleInstagram(parceiro.instagram)
        );
        definirLink(
            'visLinkedinParceiro',
            parceiro.linkedin,
            '',
            parceiro.linkedin ? (parceiro.nome ?? '') : ''
        );

        definirTexto('visNomeRepresentanteParceiro', parceiro.nome_representante);
        definirTexto('visCargoRepresentanteParceiro', parceiro.cargo_representante);
        definirTexto('visTelefoneRepresentanteParceiro', mascaraTelefone(parceiro.telefone_representante));
        definirLink('visEmailRepresentanteParceiro', parceiro.email_representante, 'mailto:');

        mostrarFotoVisualizacao(parceiro.foto ? `${UPLOAD_PARCEIROS}${parceiro.foto}` : '');
    }

    async function abrirVisualizacaoParceiro(id) {
        try {
            const resposta = await fetch(`${ENDPOINT_BUSCAR_PARCEIRO}?id=${encodeURIComponent(id)}`);
            const dados = await resposta.json();

            if (!dados.sucesso || !dados.parceiro) {
                alert(dados.erro || 'Não foi possível carregar esse parceiro.');
                return;
            }

            preencherVisualizarParceiro(dados.parceiro);
            abrirModal('visualizarParceiro');
        } catch (erro) {
            alert('Erro de conexão ao carregar o parceiro.');
        }
    }

    document.addEventListener('click', (evento) => {
        const botaoEditar = evento.target.closest('.botaoEditarParceiro');

        if (botaoEditar) {
            abrirEdicaoParceiro(botaoEditar.dataset.id);

            return;
        }

        const botaoVisualizar = evento.target.closest('.botaoVisualizarParceiro, .botaoVisualizar[data-modal="visualizarParceiro"]');

        if (botaoVisualizar) {
            abrirVisualizacaoParceiro(botaoVisualizar.dataset.id);

            return;
        }

        if (evento.target.closest('.botaoAddParceiro')) {
            resetarModalParceiro();
        }
    });

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarCrudParceiro);
} else {
    inicializarCrudParceiro();
}

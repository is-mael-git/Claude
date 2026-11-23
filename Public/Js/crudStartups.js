// ──────────────────────────────────────────────
// REFERÊNCIAS
// ──────────────────────────────────────────────
const form          = document.getElementById('startupForm');
const foundersList  = document.getElementById('foundersList');
const addFounderBtn = document.getElementById('addFounderBtn');
const biografiaStartup = document.getElementById('biografia');
const contadorBiografia = document.getElementById('biografiaContador');
const fotoStartupInput = document.getElementById('startup-photo');
const previewFotoStartup = window.PreviewImagem?.criar(fotoStartupInput, document.getElementById('previewFotoStartup'));

if (form) form.noValidate = true;

const mainModal = document.getElementById('modalCadastroStartup');

// modal (irmão, empilhado por cima) de cadastro/edição do fundador
const founderDialog = document.getElementById('modalDetalheFundador');
const founderForm   = document.getElementById('founderForm');
if (founderForm) founderForm.noValidate = true;

// item .cs-founderItem sendo editado no modal no momento (null = nenhum)
let founderModalAlvo = null;

// ──────────────────────────────────────────────
// FUNDADORES — cadastro/edição (abre/fecha via abrirModal/fecharModal padrão)
// ──────────────────────────────────────────────
function abrirDetalhesFundador(item = null) {
  if (!founderDialog || !founderForm) return;
  founderModalAlvo = item;
  founderForm.reset();

  const tituloEl = founderDialog.querySelector('.modalTitulo');
  if (tituloEl) tituloEl.lastChild.textContent = item ? 'Editar Fundador' : 'Cadastrar Fundador';
  const salvarEl = founderDialog.querySelector('.btnSalvar');
  if (salvarEl) salvarEl.textContent = 'SALVAR';

  const campos = {
    '.cs-founderNomeInput':         item?.dataset.nome || '',
    '.cs-founderCargoInput':        item?.dataset.cargo || '',
    '.cs-founderEmailInput':        item?.dataset.email || '',
    '.cs-founderTelefoneInput':     item?.dataset.telefone || '',
  };

  Object.entries(campos).forEach(([seletor, valor]) => {
    const campo = founderDialog.querySelector(seletor);
    if (campo) campo.value = valor;
  });
  // o telefone vindo do banco chega só com dígitos; a máscara só age ao digitar
  window.Mascara?.formatar(founderForm);

  abrirModal('modalDetalheFundador');
}

// limpa o alvo quando o modal fecha por QUALQUER via (Salvar, Cancelar, X,
// clique fora ou Esc — todas passam por aqui, já que todas chamam .close())
founderDialog?.addEventListener('close', () => {
  founderModalAlvo = null;
  limparErrosEtapaStartup(founderForm);
});

if (founderForm) {
  founderForm.addEventListener('input', e => {
    limparErroCampoStartup(e.target);
  });
  founderForm.addEventListener('submit', async e => {
    e.preventDefault();
    limparErrosEtapaStartup(founderForm);
    let primeiroPendente = null;
    founderForm.querySelectorAll('input').forEach(campo => {
      const mensagem = erroDoCampoStartup(campo);
      if (!mensagem) return;
      mostrarErroCampoStartup(campo.closest('.cs-formGroup'), mensagem);
      primeiroPendente = primeiroPendente ?? campo;
    });
    if (primeiroPendente) {
      primeiroPendente.focus();
      return;
    }
    const emailFundador = founderDialog.querySelector('.cs-founderEmailInput');
    const emailRepetido = [...foundersList.querySelectorAll('.cs-founderItem')].some(item =>
      item !== founderModalAlvo && item.dataset.email?.toLowerCase() === emailFundador.value.trim().toLowerCase()
    );
    if (emailRepetido) {
      mostrarErroCampoStartup(emailFundador.closest('.cs-formGroup'), 'E-mail de fundador repetido.');
      emailFundador.focus();
      return;
    }
    if (!await validarUnicidadeCampo(emailFundador)) {
      emailFundador.focus();
      return;
    }
    const nomeInput = founderDialog.querySelector('.cs-founderNomeInput');
    const nome = nomeInput?.value.trim() || '';

    const item = founderModalAlvo || criarFounderItem();
    item.dataset.nome = nome;
    item.querySelector('.cs-founderNome').textContent = nome;

    item.dataset.cargo        = founderDialog.querySelector('.cs-founderCargoInput')?.value.trim() || '';
    item.dataset.email        = founderDialog.querySelector('.cs-founderEmailInput')?.value.trim() || '';
    item.dataset.telefone     = founderDialog.querySelector('.cs-founderTelefoneInput')?.value.trim() || '';

    if (!founderModalAlvo) foundersList.appendChild(item);
    document.getElementById('founderRequiredError').hidden = true;
    renumerarFundadores();

    fecharModal('modalDetalheFundador');
  });
}

// ──────────────────────────────────────────────
// ETAPAS — mesmo mecanismo do crudParceiro.js: uma seção .active por vez,
// a trilha no topo marca a etapa atual/concluídas, Avançar/Voltar troca
// qual fica visível. A terceira etapa lista os fundadores; o formulário
// de cada fundador abre em outro modal.
// ──────────────────────────────────────────────
const etapasStartup = mainModal?.querySelectorAll('.cs-step') || [];
const secoesStartup = mainModal?.querySelectorAll('.cs-card[data-etapa]') || [];
const btnVoltarStartup     = mainModal?.querySelector('.cs-btnVoltar');
const btnAvancarStartup    = mainModal?.querySelector('.cs-btnAvancar');
const btnSalvarStartupForm = document.getElementById('btnSalvarStartup');

const TAMANHOS_MASCARA_STARTUP = { cnpj: [14], cep: [8], telefone: [10, 11] };
const MENSAGEM_OBRIGATORIO_STARTUP = 'Campo obrigatório.';
const MENSAGEM_INCOMPLETO_STARTUP  = 'Preencha o campo por completo.';
const MENSAGEM_EMAIL_STARTUP       = 'Informe um e-mail válido.';
const MENSAGEM_CNPJ_STARTUP        = 'CNPJ inválido.';
const MENSAGEM_CEP_STARTUP         = 'CEP inválido.';
const MENSAGEM_DATA_STARTUP        = 'Informe uma data de fundação válida.';
const MENSAGEM_DATA_FUTURA_STARTUP = 'A data de fundação não pode ser futura.';
const TAMANHO_MAX_FOTO_STARTUP     = 2 * 1024 * 1024; // igual a FOTO_MAX_BYTES do controllerStartups.php
const MENSAGEM_FOTO_GRANDE_STARTUP = 'A imagem deve ter no máximo 2 MB. Escolha uma imagem menor.';
const ENDPOINT_VALIDAR_CAMPO_STARTUP = '/ParkTec/startups/validar-campo';
const timersValidacaoStartup = new WeakMap();

// Faz a requisição e separa os jeitos de ela falhar, pra mensagem dizer a verdade:
//  - sem rede (fetch rejeita);
//  - resposta que não é JSON (erro fatal do PHP, página de erro do Apache);
//  - resposta JSON, inclusive os 4xx/5xx que o controller devolve de propósito.
// Devolve { falha } (texto pronto pra tela) ou { dados, status }.
async function requisitarJson(url, opcoes) {
  let resposta;
  try {
    resposta = await fetch(url, opcoes);
  } catch (erro) {
    return { falha: 'Sem conexão com o servidor. Verifique a internet e tente novamente.' };
  }
  try {
    return { dados: await resposta.json(), status: resposta.status };
  } catch (erro) {
    return { falha: `O servidor respondeu com um erro inesperado (código ${resposta.status}). Avise o responsável.` };
  }
}

function mostrarErroCampoStartup(wrapper, mensagem) {
  if (!wrapper) return;
  if (wrapper.classList.contains('cs-photoUpload')) {
    const erroFoto = wrapper.querySelector('.cs-photoError');
    erroFoto.textContent = mensagem;
    erroFoto.hidden = false;
    return;
  }
  let erro = wrapper.querySelector('.cs-erroCampo');
  if (!erro) {
    erro = document.createElement('span');
    erro.className = 'cs-erroCampo erroCampoParceiro';
    const label = wrapper.querySelector('label');
    if (label) {
      label.appendChild(erro);
      label.classList.add('cs-labelComErro');
    }
    else wrapper.appendChild(erro);
  }
  erro.textContent = mensagem;
}

function limparErroCampoStartup(campo) {
  const wrapper = campo?.closest('.cs-formGroup');
  wrapper?.querySelector('.cs-erroCampo')?.remove();
  wrapper?.querySelector('label')?.classList.remove('cs-labelComErro');
}

function limparErrosEtapaStartup(secao) {
  secao?.querySelectorAll('.cs-erroCampo').forEach(e => e.remove());
  secao?.querySelectorAll('.cs-labelComErro').forEach(label => label.classList.remove('cs-labelComErro'));
  secao?.querySelectorAll('.cs-photoError').forEach(erro => {
    erro.textContent = '';
    erro.hidden = true;
  });
}

// campo mascarado só está pronto com a máscara inteira (mesma ideia do
// crudParceiro.js — Mascara.remover devolve os dígitos mesmo sem foco)
function mascaraCompletaStartup(campo) {
  const tamanhos = TAMANHOS_MASCARA_STARTUP[campo.dataset.mascara];
  if (!tamanhos) return true;
  return tamanhos.includes(window.Mascara?.remover(campo).length ?? 0);
}

function erroDoCampoStartup(campo) {
  if (campo.type === 'file') {
    const arquivo = campo.files?.[0];
    if (!arquivo) return campo.required ? MENSAGEM_OBRIGATORIO_STARTUP : '';
    const tipos = {
      'image/jpeg': ['jpg', 'jpeg'],
      'image/png': ['png'],
      'image/webp': ['webp'],
    };
    const extensao = arquivo.name.split('.').pop()?.toLowerCase();
    if (!tipos[arquivo.type]?.includes(extensao)) return 'Selecione uma imagem JPG, PNG ou WEBP.';
    if (arquivo.size > TAMANHO_MAX_FOTO_STARTUP) return MENSAGEM_FOTO_GRANDE_STARTUP;
    return '';
  }
  if (campo.required && campo.type === 'text' && campo.value.trim() === '') return MENSAGEM_OBRIGATORIO_STARTUP;
  if (campo.type === 'date' && campo.validity.badInput) return MENSAGEM_DATA_STARTUP;
  if (campo.validity.valueMissing) return MENSAGEM_OBRIGATORIO_STARTUP;
  if (campo.value === '') return '';
  if (campo.type === 'date') {
    if (campo.validity.rangeOverflow) return MENSAGEM_DATA_FUTURA_STARTUP;
  }
  if (campo.dataset.mascara === 'cep') {
    const cep = window.Mascara?.remover(campo) ?? campo.value.replace(/\D/g, '');
    if (!/^\d{8}$/.test(cep) || cep === '00000000') return MENSAGEM_CEP_STARTUP;
  }
  if (campo.type === 'url' && !/^https?:\/\//i.test(campo.value.trim())) return 'Informe um link válido, começando com http:// ou https://.';
  if (campo.validity.typeMismatch && campo.type === 'email') return MENSAGEM_EMAIL_STARTUP;
  if (!campo.validity.valid || !mascaraCompletaStartup(campo)) return MENSAGEM_INCOMPLETO_STARTUP;
  if (campo.dataset.mascara === 'cnpj' && window.Validador?.cnpj(campo.value) === false) return MENSAGEM_CNPJ_STARTUP;
  return '';
}

async function consultarUnicidadeStartup(campo, valor, id = 0) {
  const resposta = await fetch(ENDPOINT_VALIDAR_CAMPO_STARTUP, {
    method: 'POST',
    body: new URLSearchParams({
      campo,
      valor,
      id: String(id),
      id_startup: String(startupEmEdicaoId || 0),
    }),
  });
  const dados = await resposta.json();
  if (!resposta.ok && resposta.status !== 409) throw new Error('Falha na validação.');
  return dados;
}

async function validarUnicidadeCampo(campo) {
  if (!campo) return true;
  const mensagemLocal = erroDoCampoStartup(campo);
  if (mensagemLocal) {
    mostrarErroCampoStartup(campo.closest('.cs-formGroup'), mensagemLocal);
    return false;
  }
  const valor = campo.value.trim();
  const id = campo.id === 'founderEmail'
    ? Number(founderModalAlvo?.dataset.id || 0)
    : Number(startupEmEdicaoId || 0);
  try {
    const dados = await consultarUnicidadeStartup(campo.id, valor, id);
    if (campo.id === 'founderEmail' && !founderDialog.open) return false;
    if (campo.value.trim() !== valor) return false;
    if (dados.valido) {
      limparErroCampoStartup(campo);
      return true;
    }
    mostrarErroCampoStartup(campo.closest('.cs-formGroup'), dados.erro || 'Valor já cadastrado.');
    return false;
  } catch (erro) {
    if (campo.value.trim() === valor) {
      mostrarErroCampoStartup(campo.closest('.cs-formGroup'), 'Não foi possível validar este campo agora.');
    }
    return false;
  }
}

function agendarValidacaoUnicidade(campo) {
  clearTimeout(timersValidacaoStartup.get(campo));
  if (!campo.value.trim() || erroDoCampoStartup(campo)) return;
  timersValidacaoStartup.set(campo, setTimeout(() => validarUnicidadeCampo(campo), 450));
}

for (const campo of [document.getElementById('startupName'), document.getElementById('cnpj'), document.getElementById('founderEmail')]) {
  if (!campo) continue;
  campo.addEventListener('input', () => {
    limparErroCampoStartup(campo);
    agendarValidacaoUnicidade(campo);
  });
  campo.addEventListener('blur', () => {
    clearTimeout(timersValidacaoStartup.get(campo));
    validarUnicidadeCampo(campo);
  });
}

// ──────────────────────────────────────────────
// CEP — preenche o endereço pela ViaCEP assim que o CEP fica completo.
// "Não encontrado" e falha de rede só avisam (o CEP é opcional e o endereço
// continua editável à mão); só preenche o que a API devolveu.
// ──────────────────────────────────────────────
const ENDPOINT_VIACEP = 'https://viacep.com.br/ws/';
const cepStartup = document.getElementById('cep');
let avisoCep = '';
let consultaCep = 0;

// Campos que a ViaCEP preenche ficam azul claro (igual ao autopreenchimento do navegador)
// até a pessoa editar o campo ou o CEP mudar.
const CAMPOS_ENDERECO_CEP = ['logradouro', 'bairro', 'cidade', 'uf'].map(id => document.getElementById(id));
function limparDestaqueCep() {
  CAMPOS_ENDERECO_CEP.forEach(campo => campo.classList.remove('cs-autopreenchido'));
}
CAMPOS_ENDERECO_CEP.forEach(campo => campo.addEventListener('input', () => campo.classList.remove('cs-autopreenchido')));

function avisarCep(mensagem) {
  avisoCep = mensagem;
  if (mensagem) mostrarErroCampoStartup(cepStartup.closest('.cs-formGroup'), mensagem);
  else limparErroCampoStartup(cepStartup);
}

async function consultarCep() {
  // cada digitação invalida a consulta anterior, que pode responder atrasada
  const consulta = ++consultaCep;
  avisarCep('');
  limparDestaqueCep();

  const digitos = cepStartup.value.replace(/\D/g, '');
  const cep = digitos.slice(0, 8);
  if (cep.length < 8 || cep === '00000000') return;

  try {
    const resposta = await fetch(`${ENDPOINT_VIACEP}${cep}/json/`);
    if (!resposta.ok) throw new Error('Falha na consulta do CEP.');
    const dados = await resposta.json();
    if (consulta !== consultaCep) return;

    if (dados.erro) {
      avisarCep('CEP não encontrado. Preencha o endereço manualmente.');
      return;
    }

    const endereco = {
      logradouro: dados.logradouro,
      bairro: dados.bairro,
      cidade: dados.localidade,
      uf: dados.uf,
    };
    for (const [id, valor] of Object.entries(endereco)) {
      if (!valor) continue;
      const campo = document.getElementById(id);
      campo.value = valor;
      campo.classList.add('cs-autopreenchido');
      limparErroCampoStartup(campo);
    }
  } catch (erro) {
    if (consulta === consultaCep) {
      avisarCep('Não foi possível consultar o CEP. Preencha o endereço manualmente.');
    }
  }
}

cepStartup?.addEventListener('input', consultarCep);

for (const formulario of [form, founderForm]) {
  formulario?.addEventListener('focusout', evento => {
    const campo = evento.target;
    if (!(campo instanceof HTMLInputElement || campo instanceof HTMLSelectElement)) return;
    if (['startupName', 'cnpj', 'founderEmail'].includes(campo.id) || campo.type === 'file' || campo.hidden) return;
    const erro = erroDoCampoStartup(campo) || (campo === cepStartup ? avisoCep : '');
    if (erro) mostrarErroCampoStartup(campo.closest('.cs-formGroup'), erro);
    else limparErroCampoStartup(campo);
  });
}

fotoStartupInput?.addEventListener('change', () => {
  const mensagem = erroDoCampoStartup(fotoStartupInput);
  const erroFoto = fotoStartupInput.closest('.cs-photoUpload')?.querySelector('.cs-photoError');
  if (mensagem) {
    fotoStartupInput.value = ''; // não guarda o arquivo recusado; assim ele nem chega ao envio
    previewFotoStartup?.mostrar('');
    mostrarErroCampoStartup(fotoStartupInput.closest('.cs-photoUpload'), mensagem);
  } else if (erroFoto) {
    erroFoto.textContent = '';
    erroFoto.hidden = true;
  }
});

function etapaValidaStartup(secao) {
  if (!secao) return true;
  limparErrosEtapaStartup(secao);

  const campos = secao.querySelectorAll('input, select, textarea');
  let primeiroPendente = null;

  for (const campo of campos) {
    if (campo.hidden || campo.closest('[hidden]')) continue;

    const mensagem = erroDoCampoStartup(campo);
    if (!mensagem) continue;

    mostrarErroCampoStartup(campo.closest('.cs-formGroup, .cs-photoUpload'), mensagem);
    primeiroPendente = primeiroPendente ?? campo;
  }

  primeiroPendente?.focus();
  return primeiroPendente === null;
}

/** Trilha do topo: escuro na etapa da vez, verde nas que já ficaram pra trás. */
function marcarEtapaStartup(numeroAtual) {
  etapasStartup.forEach(etapa => {
    const numero = Number(etapa.dataset.etapa);
    etapa.classList.toggle('cs-stepAtiva', numero === numeroAtual);
    etapa.classList.toggle('cs-stepConcluida', numero < numeroAtual);
  });
}

function mostrarEtapaStartup(numero) {
  secoesStartup.forEach(secao => {
    secao.classList.toggle('active', Number(secao.dataset.etapa) === numero);
  });

  marcarEtapaStartup(numero);

  btnVoltarStartup?.toggleAttribute('hidden', numero === 1);
  btnAvancarStartup?.toggleAttribute('hidden', numero === 3);
  btnSalvarStartupForm?.toggleAttribute('hidden', numero !== 3);
}

async function avancarStartup() {
  const secaoAtual = mainModal?.querySelector('.cs-card.active');
  const etapaAtual = Number(secaoAtual?.dataset.etapa || 1);

  if (!etapaValidaStartup(secaoAtual)) return;

  if (etapaAtual === 1) {
    btnAvancarStartup.disabled = true;
    try {
      const resultados = await Promise.all([
        validarUnicidadeCampo(document.getElementById('startupName')),
        validarUnicidadeCampo(document.getElementById('cnpj')),
      ]);
      if (resultados.some(valido => !valido)) return;
    } finally {
      btnAvancarStartup.disabled = false;
    }
  }

  mostrarEtapaStartup(Math.min(etapaAtual + 1, 3));
}

function voltarStartup() {
  const secaoAtual = mainModal?.querySelector('.cs-card.active');
  const etapaAtual = Number(secaoAtual?.dataset.etapa || 1);

  mostrarEtapaStartup(Math.max(etapaAtual - 1, 1));
}

// os botões "AVANÇAR"/"VOLTAR" do modal chamam essas duas pelo onclick
// (ver App/Views/crudStartups.php), igual crudParceiro.js faz com as dele.
window.avancarStartup = avancarStartup;
window.voltarStartup  = voltarStartup;

// ──────────────────────────────────────────────
// SUBMIT — cadastro / edição de startup
// ──────────────────────────────────────────────
const ENDPOINT_ADICIONAR_STARTUP = '/ParkTec/startups/adicionar';
const ENDPOINT_ATUALIZAR_STARTUP = '/ParkTec/startups/atualizar';
const ENDPOINT_BUSCAR_STARTUP    = '/ParkTec/startups/buscar';

// null = cadastrando uma startup nova; com valor = editando essa startup
let startupEmEdicaoId = null;

function limparErrosStartupForm() {
  const antigo = form.querySelector('.cs-fieldError');
  if (antigo) antigo.remove();
}

function mostrarErrosStartupForm(erros) {
  limparErrosStartupForm();
  const msg = document.createElement('p');
  msg.className = 'cs-fieldError';
  msg.style.color = '#c0392b';
  msg.style.margin = '8px 0 0';
  msg.textContent = erros.join(' • ');
  form.querySelector('.cs-modalFooter').before(msg);
}

// As chaves de errosCampos são os ids dos campos em crudStartups.php; a exceção é
// founderEmail, que vem como { posição do fundador: mensagem }.
function mostrarErrosCamposServidor(errosCampos) {
  const campos = [];
  for (const [id, mensagem] of Object.entries(errosCampos)) {
    if (id === 'founderEmail') continue;
    const campo = document.getElementById(id);
    if (!campo) continue;
    mostrarErroCampoStartup(campo.closest('.cs-formGroup, .cs-photoUpload'), mensagem);
    campos.push(campo);
  }
  if (campos.length) {
    // o primeiro campo na ordem da tela decide a etapa que abre e onde fica o foco
    campos.sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1));
    mostrarEtapaStartup(Number(campos[0].closest('.cs-card')?.dataset.etapa || 1));
    campos[0].focus();
    return;
  }

  const indiceFundador = Object.keys(errosCampos.founderEmail || {})[0];
  if (indiceFundador !== undefined) {
    const item = foundersList.querySelectorAll('.cs-founderItem')[Number(indiceFundador)];
    if (!item) return;
    mostrarEtapaStartup(3);
    abrirDetalhesFundador(item);
    const campo = founderForm.querySelector('#founderEmail');
    mostrarErroCampoStartup(campo.closest('.cs-formGroup'), errosCampos.founderEmail[indiceFundador]);
    campo.focus();
  }
}

// Contador da biografia (X/3000)
function atualizarContadorBiografia() {
  if (contadorBiografia && biografiaStartup) contadorBiografia.textContent = `${biografiaStartup.value.length}/${biografiaStartup.maxLength}`;
}
biografiaStartup?.addEventListener('input', atualizarContadorBiografia);

// Zera o formulário pra um cadastro novo. O botão "STARTUP" já abre o modal
// sozinho (class="abrirModal" no HTML, ver Componentes/modal.js) — falta só
// isto, chamado à parte, senão cancelar uma edição e clicar em "STARTUP" em
// seguida reabriria o formulário ainda com os dados da startup anterior.
function resetarFormularioStartup() {
  startupEmEdicaoId = null;
  form.reset();
  previewFotoStartup?.mostrar('');
  form.querySelector('#startup-photo').required = true;
  form.querySelector('.cs-photoLabel')?.classList.add('campoObrigatorio');
  foundersList?.replaceChildren();
  document.getElementById('founderRequiredError').hidden = true;
  atualizarContadorBiografia();
  consultaCep++;
  avisoCep = '';
  limparDestaqueCep();
  limparErrosStartupForm();
  secoesStartup.forEach(secao => limparErrosEtapaStartup(secao));
  mostrarEtapaStartup(1);

  const tituloEl = mainModal?.querySelector('.modalTitulo');
  if (tituloEl) tituloEl.lastChild.textContent = 'Cadastrar Startup';
}

document.addEventListener('click', evento => {
  if (evento.target.closest('.botaoAddStartup')) {
    resetarFormularioStartup();
  }
});

// Busca os dados de uma startup e abre o mesmo modal já preenchido, em modo
// de edição — o submit passa a chamar /atualizar em vez de /adicionar, e a
// fundadores carregados também podem ser editados no modal secundário.
async function abrirEdicaoStartup(id) {
  const { dados, falha } = await requisitarJson(`${ENDPOINT_BUSCAR_STARTUP}?id=${encodeURIComponent(id)}`);
  if (falha || !dados.sucesso || !dados.startup) {
    alert(falha || dados.erro || 'Não foi possível carregar essa startup.');
    return;
  }

  try {
    const s = dados.startup;
    resetarFormularioStartup();
    startupEmEdicaoId = Number(s.id);
    const fotoObrigatoria = !s.logo || s.logo === 'nothing';
    form.querySelector('#startup-photo').required = fotoObrigatoria;
    form.querySelector('.cs-photoLabel')?.classList.toggle('campoObrigatorio', fotoObrigatoria);

    form.querySelector('#startupName').value = s.nome || '';
    form.querySelector('#biografia').value = s.biografia || '';
    atualizarContadorBiografia();
    form.querySelector('#cnpj').value = window.Mascara ? Mascara.aplicarEm(s.cnpj, 'cnpj') : (s.cnpj || '');
    form.querySelector('#site').value = s.site || '';
    form.querySelector('#instagram').value = s.instagram || '';
    form.querySelector('#linkedin').value = s.linkedin || '';
    form.querySelector('#cep').value = window.Mascara && s.cep ? Mascara.aplicarEm(s.cep, 'cep') : (s.cep || '');
    form.querySelector('#logradouro').value = s.logradouro || '';
    form.querySelector('#numero').value = s.numero ?? 0;
    form.querySelector('#complemento').value = s.complemento || '';
    form.querySelector('#bairro').value = s.bairro || '';
    form.querySelector('#cidade').value = s.cidade || '';
    form.querySelector('#uf').value = s.uf || '';
    form.querySelector('#programs').value = s.outras_participacoes || '';
    form.querySelector('#sector').value = s.setor_atuacao || '';

    form.querySelector('#dataFundacao').value = s.data_fundacao || '';

    if (s.logo && s.logo !== 'nothing') {
      const caminhoFoto = s.logo.startsWith('Assets/')
        ? `/ParkTec/Public/${s.logo}`
        : `/ParkTec/Public/Upload/Startup/${encodeURIComponent(s.logo)}`;
      previewFotoStartup?.mostrar(caminhoFoto);
    }

    (s.fundadores || []).forEach(fundador => {
      const item = criarFounderItem();
      item.dataset.id = fundador.id;
      item.dataset.nome = fundador.nome || '';
      item.dataset.cargo = fundador.funcao || '';
      item.dataset.email = fundador.email || '';
      item.dataset.telefone = fundador.telefone || '';
      item.querySelector('.cs-founderNome').textContent = fundador.nome || '';
      foundersList.appendChild(item);
    });
    renumerarFundadores();

    const tituloEl = mainModal?.querySelector('.modalTitulo');
    if (tituloEl) tituloEl.lastChild.textContent = 'Editar Startup';

    secoesStartup.forEach(secao => limparErrosEtapaStartup(secao));
    mostrarEtapaStartup(1);
    abrirModal('modalCadastroStartup');
  } catch (erro) {
    console.error(erro);
    alert('Erro inesperado ao montar o formulário de edição. Recarregue a página e tente de novo.');
  }
}

form.addEventListener('submit', async e => {
  e.preventDefault();

  const etapaAtual = Number(mainModal?.querySelector('.cs-card.active')?.dataset.etapa || 1);
  if (etapaAtual !== 3) {
    await avancarStartup();
    return;
  }

  const itensFundadores = [...foundersList.querySelectorAll('.cs-founderItem')];
  if (itensFundadores.length === 0) {
    document.getElementById('founderRequiredError').hidden = false;
    addFounderBtn.focus();
    return;
  }

  const saveBtn = document.getElementById('btnSalvarStartup');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Salvando...';
  limparErrosStartupForm();

  const editando = startupEmEdicaoId !== null;
  const formData = new FormData(form);
  if (editando) formData.set('id_startup', startupEmEdicaoId);
  const fundadores = [...foundersList.querySelectorAll('.cs-founderItem')].map(item => ({
    id: Number(item.dataset.id || 0),
    nome: item.dataset.nome,
    funcao: item.dataset.cargo,
    email: item.dataset.email,
    telefone: item.dataset.telefone,
  }));
  formData.set('fundadores', JSON.stringify(fundadores));

  try {
    const { dados, falha } = await requisitarJson(editando ? ENDPOINT_ATUALIZAR_STARTUP : ENDPOINT_ADICIONAR_STARTUP, {
      method: 'POST',
      body: formData,
    });

    if (falha) {
      mostrarErrosStartupForm([falha]);
    } else if (dados.sucesso) {
      sessionStorage.setItem('mostrarModalSalvo', '1');
      window.location.reload();
    } else if (dados.errosCampos) {
      mostrarErrosCamposServidor(dados.errosCampos);
    } else if (dados.erro === 'Adicione pelo menos um fundador.') {
      document.getElementById('founderRequiredError').hidden = false;
      addFounderBtn.focus();
    } else {
      mostrarErrosStartupForm([dados.erro || 'Não foi possível salvar a startup.']);
    }
  } catch (erro) {
    console.error(erro);
    mostrarErrosStartupForm(['Erro inesperado na tela. Recarregue a página e tente de novo.']);
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'SALVAR';
  }
});

// ──────────────────────────────────────────────
// TABELA DE STARTUPS — editar
// (delegado no tbody, que já existe no HTML antes do tabela.js preencher as
// linhas via AJAX — ver Componentes/tabela.js)
// ──────────────────────────────────────────────
const tableBody = document.querySelector('.tabelaCorpoStartups');

if (tableBody) {
  tableBody.addEventListener('click', e => {
    const editBtn = e.target.closest('.botaoEditarStartup');
    if (editBtn) {
      abrirEdicaoStartup(editBtn.dataset.id);
    }
  });
}

// ──────────────────────────────────────────────
// FUNDADORES — adicionar / remover
// Os fundadores são enviados junto com o cadastro da startup.
// ──────────────────────────────────────────────

// Renumera os itens conforme a posição atual na lista (1..N).
function renumerarFundadores() {
  foundersList.querySelectorAll('.cs-founderItem').forEach((item, index) => {
    const numero    = index + 1;
    const badge     = item.querySelector('.cs-founderNumber');
    const nomeBtn   = item.querySelector('.cs-founderNome');
    const removeBtn = item.querySelector('.cs-founderRemove');

    if (badge) badge.textContent = numero;
    if (nomeBtn) nomeBtn.setAttribute('aria-label', `Editar fundador ${numero}: ${item.dataset.nome}`);
    if (removeBtn) removeBtn.setAttribute('aria-label', `Remover fundador ${numero}`);
  });
}

function criarFounderItem() {
  const item = document.createElement('div');
  item.className = 'cs-founderItem';

  const badge = document.createElement('span');
  badge.className = 'cs-founderNumber';

  const nomeBtn = document.createElement('button');
  nomeBtn.type = 'button';
  nomeBtn.className = 'cs-founderNome';
  nomeBtn.addEventListener('click', () => {
    abrirDetalhesFundador(item);
  });

  const removeBtn = document.createElement('button');
  removeBtn.type = 'button';
  removeBtn.className = 'cs-founderRemove';
  removeBtn.title = 'Remover fundador';
  removeBtn.textContent = '×';
  removeBtn.addEventListener('click', e => {
    e.stopPropagation();
    item.remove();
    renumerarFundadores();
  });

  item.appendChild(badge);
  item.appendChild(nomeBtn);
  item.appendChild(removeBtn);

  return item;
}

if (addFounderBtn) {
  addFounderBtn.addEventListener('click', () => {
    abrirDetalhesFundador();
  });
}

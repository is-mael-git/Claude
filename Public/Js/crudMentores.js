document.addEventListener('DOMContentLoaded', () => window.mostrarModalSalvo?.());

(function () {
  const modal = document.querySelector('.areaModalCentral');
  if (!modal) return;

  const TOTAL_PASSOS = 3;
  const ICONE_SETA = '<i class="fa-solid fa-plus"></i>';

  /* ---------- Preview de imagens (reaproveita o mesmo <img>) ---------- */
  function configurarPreview(idInput, seletorImg, classeNova) {
    const input = modal.querySelector(idInput);
    const img = modal.querySelector(seletorImg);
    if (!input || !img) return;

    let urlAtual = null;

    input.addEventListener('change', () => {
      const arquivo = input.files?.[0];
      if (!arquivo) return;

      if (urlAtual) URL.revokeObjectURL(urlAtual);
      urlAtual = URL.createObjectURL(arquivo);

      img.src = urlAtual;
      img.alt = 'Prévia da imagem selecionada';
      img.classList.add(classeNova);
    });
  }

  configurarPreview('#mentorFoto', '.fotoPerfil', 'novaFotoPerfil');
  configurarPreview('#bannerMentor', '.fotoBanner', 'novaFotoBanner');

  /* ---------- Máscaras e tratamento de campos ---------- */
  const inputTelefone = modal.querySelector('input[name="telefone"]');

  const inputEmail = modal.querySelector('input[name="email"]');
  if (inputEmail) {
    inputEmail.addEventListener('input', (e) => {
      e.target.value = e.target.value.toLowerCase().replace(/\s+/g, '');
    });
  }

  const inputLinkedin = modal.querySelector('input[name="linkedin"]');

  if (inputLinkedin) {
    inputLinkedin.addEventListener('input', (e) => {
      e.target.value = e.target.value
        .replace(/\s+/g, '')
        .toLowerCase();
    });
  }

  /* ---------- Contador da biografia ---------- */
  const textarea = modal.querySelector('.biografiaInput textarea');
  const contador = modal.querySelector('.qtdCaracter');
  if (textarea && contador) {
    const limite = Number(textarea.maxLength) || 500;
    textarea.addEventListener('input', () => {
      const qtd = textarea.value.length;
      contador.textContent = `${qtd}/${limite}`;
      contador.classList.toggle('limiteAtingido', qtd >= limite);
    });
  }

  /* ---------- Texto do botão dos dropdowns (um único listener) ---------- */
  modal.addEventListener('change', (e) => {
    const alvo = e.target;
    if (!alvo.matches('input[type="checkbox"], input[type="radio"]')) return;

    const areaDrop = alvo.closest('.areaDrop');
    if (!areaDrop) return;

    atualizarTextoDropdown(areaDrop);
    if (areaDrop.closest('.dropModalidade')) {
      areaDrop.classList.remove('aberto');
      areaDrop.querySelector('.dropDisponibilidade')?.setAttribute('aria-expanded', 'false');
    }
  });

  modal.addEventListener('click', (e) => {
    const botao = e.target.closest('.dropHorarios .dropDisponibilidade, .dropModalidade .dropDisponibilidade');
    if (!botao) return;

    const areaDrop = botao.closest('.areaDrop');
    modal.querySelectorAll('.areaDrop.aberto').forEach((aberto) => {
      if (aberto !== areaDrop) {
        aberto.classList.remove('aberto');
        aberto.querySelector('.dropDisponibilidade')?.setAttribute('aria-expanded', 'false');
      }
    });
    const aberto = areaDrop.classList.toggle('aberto');
    botao.setAttribute('aria-expanded', String(aberto));
  });

  document.addEventListener('click', (e) => {
    const areaDropAberta = modal.querySelector('.dropHorarios .areaDrop.aberto, .dropModalidade .areaDrop.aberto');
    if (!areaDropAberta || areaDropAberta.contains(e.target)) return;

    areaDropAberta.classList.remove('aberto');
    areaDropAberta.querySelector('.dropDisponibilidade')?.setAttribute('aria-expanded', 'false');
  });

  /* ---------- Navegação entre passos ---------- */
  const btnAvancar = modal.querySelectorAll('.btnAvancarCard');
  const btnVoltar = modal.querySelectorAll('.btnVoltarCard');
  const btnSalvar = modal.querySelector('.btnSalvarMentor');
  const form = modal.closest('form');
  if (form) form.noValidate = true; // a validação é feita por este arquivo
  const inputInstagram = modal.querySelector('input[name="instagram"]');
  const inputIdMentor = modal.querySelector('#mentorId');
  const inputFoto = modal.querySelector('#mentorFoto');
  const inputBanner = modal.querySelector('#bannerMentor');
  const fotoPerfil = modal.querySelector('.fotoPerfil');
  const fotoBanner = modal.querySelector('.fotoBanner');
  const FOTO_PERFIL_PADRAO = fotoPerfil?.src ?? '';
  const BANNER_PADRAO = fotoBanner?.src ?? '';
  let possuiFotoExistente = false;
  const ACAO_ADICIONAR = '/ParkTec/mentores/adicionar';
  const ACAO_ATUALIZAR = '/ParkTec/mentores/atualizar';
  const URL_UPLOAD = '/ParkTec/Public/Upload/';

  fotoPerfil?.addEventListener('error', () => {
    if (fotoPerfil.src === FOTO_PERFIL_PADRAO) return;
    possuiFotoExistente = false;
    fotoPerfil.src = FOTO_PERFIL_PADRAO;
    fotoPerfil.classList.remove('novaFotoPerfil');
  });

  fotoBanner?.addEventListener('error', () => {
    if (fotoBanner.src === BANNER_PADRAO) return;
    fotoBanner.src = BANNER_PADRAO;
    fotoBanner.classList.remove('novaFotoBanner');
  });

  const IDS_COMPETENCIAS = {
    'estrategia': 1,
    'networking&conexoes': 2,
    'produto&tecnologia': 3,
    'operacoes': 3,
    'gestao': 4,
    'juridico&pi': 4,
    'capital&investimentos': 5,
    'financas': 5,
    'marketing&vendas': 2,
    'mercados&clientes': 2,
  };

  if (!modal.dataset.step) modal.dataset.step = '1';

  const passoAtual = () => Number(modal.dataset.step) || 1;

  function irPara(passo) {
    modal.dataset.step = passo;
    atualizarBotoes(passo);
  }

  window.irParaEtapaMentor = irPara;

  function atualizarBotoes(passo) {
    btnVoltar.forEach((botao) => {
      botao.style.display = passo === 1 ? 'none' : 'inline-block';
    });
    btnAvancar.forEach((botao) => {
      botao.style.display = passo === TOTAL_PASSOS ? 'none' : 'inline-block';
    });
    if (btnSalvar) btnSalvar.style.display = passo === TOTAL_PASSOS ? 'inline-block' : 'none';
  }

  /* ---------- Erros inline ---------- */
  function spanErro(campo) {
    const chave = campo.name || campo.id;
    if (!chave) return null;
    return modal.querySelector(`.msgErro[data-erro-para="${chave}"]`);
  }

  function mostrarErro(campo, mensagem) {
    const span = spanErro(campo);
    if (span) {
      span.textContent = mensagem;
      span.classList.add('ativo');
    }
    campo.classList.add('campoInvalido');
  }

  function limparErro(campo) {
    const span = spanErro(campo);
    if (span) {
      span.textContent = '';
      span.classList.remove('ativo');
    }
    campo.classList.remove('campoInvalido');
  }

  let jaMudouPasso = false;
  let validandoManual = false;

  // Intercepta o balão nativo ("Preencha este campo") e mostra a mensagem inline
  modal.addEventListener('invalid', (e) => {
    e.preventDefault();
    const campo = e.target;
    const v = campo.validity;

    let msg = campo.validationMessage; // vale para customError
    if (v.valueMissing) msg = 'Campo obrigatório.';
    else if (v.typeMismatch && campo.type === 'email') msg = 'E-mail inválido.';
    else if (v.typeMismatch) msg = 'Valor inválido.';

    mostrarErro(campo, msg);

    const secao = campo.closest('[data-card]');
    const passo = Number(secao?.dataset.card);
    if (!validandoManual && !jaMudouPasso && passo && passo !== passoAtual()) {
      jaMudouPasso = true;
      irPara(passo);
      setTimeout(() => { jaMudouPasso = false; }, 0);
    }
  }, true);

  /* ---------- Validação ---------- */
  const SECOES = {
    1: '.cardDadosPessoais',
    2: '.cardDisponibilidade',
    3: '.cardCernes',
  };

  // Grupos obrigatórios por passo: [seletor, mensagem]
  const GRUPOS = {
    1: [['input[name="mentorFoto"]', 'Campo obrigatório.'],
        ['input[name="nome"]', 'Campo obrigatório.'],
        ['select[name="email"]', 'Campo obrigatório.']
      ],
    2: [
      ['input[name="semana[]"]', 'Campo obrigatório.'],
      ['input[name="horario[]"]', 'Campo obrigatório.'],
      ['input[name="modalidade"]', 'Campo obrigatório.'],
    ],
    3: [['input[name="competencia[]"]', 'Campo obrigatório.']],
  };

  let camposInvalidos = [];
  const registrarInvalido = (campo) => camposInvalidos.push(campo);

  function primeiroInvalido() {
    return [...camposInvalidos].sort((a, b) =>
      a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1
    )[0];
  }

  // Rola até o título do campo (inputs de grupos ficam em dropdowns ocultos) e foca os de texto
  function destacarCampo(campo) {
    if (!campo) return;

    const alvo =
      spanErro(campo)?.closest('.inputMentorTitulo, .tituloCardMentor') ||
      campo.closest('.areaDrop') ||
      campo.closest('.perfilContainer') ||
      campo;

    alvo.scrollIntoView({ behavior: 'smooth', block: 'start' });

    if (campo.matches('textarea, input:not([type="checkbox"]):not([type="radio"]):not([type="file"])')) {
      campo.focus({ preventScroll: true });
    }
  }

  function validarGrupo(secao, seletor, mensagem) {
    const inputs = secao.querySelectorAll(seletor);
    if (!inputs.length) return true;

    const ok = Array.from(inputs).some((i) => {
      if (i.type === 'file') {
        const fotoExistenteCarregada = i === inputFoto
          && possuiFotoExistente
          && fotoPerfil?.complete
          && fotoPerfil.naturalWidth > 0;
        return i.files?.length > 0 || fotoExistenteCarregada;
      }
      return i.matches('input[type="checkbox"], input[type="radio"]') ? i.checked : i.value !== '';
    });
    inputs.forEach((i) => i.setCustomValidity(''));

    if (!ok) {
      inputs[0].setCustomValidity(mensagem);
      inputs[0].checkValidity(); // dispara "invalid" -> mostra erro inline
      registrarInvalido(inputs[0]);
      return false;
    }

    limparErro(inputs[0]);
    return true;
  }

  function validarPasso(passo) {
    const secao = modal.querySelector(SECOES[passo]);
    if (!secao) return true;

    validandoManual = true;
    camposInvalidos = [];
    let valido = true;

    // Campos "required": percorre TODOS
    secao
      .querySelectorAll('input[required], select[required], textarea[required]')
      .forEach((campo) => {
        campo.setCustomValidity('');
        if (!campo.checkValidity()) {
          registrarInvalido(campo);
          valido = false;
        } else {
          limparErro(campo);
        }
      });

    // Telefone completo (só se não estiver vazio, para não trocar "Campo obrigatório.")
    if (passo === 1 && inputTelefone && !inputTelefone.validity.valueMissing) {
      const digitos = inputTelefone.value.replace(/\D/g, '');
      inputTelefone.setCustomValidity(digitos.length < 10 ? 'Telefone inválido.' : '');
      if (digitos.length < 10) {
        inputTelefone.checkValidity();
        registrarInvalido(inputTelefone);
        valido = false;
      } else {
        limparErro(inputTelefone);
      }
    }

    // Instagram e LinkedIn: opcionais, mas se preenchidos precisam ser links http(s)
    if (passo === 1) {
      [inputInstagram, inputLinkedin].forEach((campo) => {
        if (!campo) return;
        const valor = campo.value.trim();
        if (valor !== '' && !/^https?:\/\/\S+$/i.test(valor)) {
          mostrarErro(campo, 'O link deve começar com http:// ou https://.');
          registrarInvalido(campo);
          valido = false;
        } else {
          limparErro(campo);
        }
      });
    }

    // Grupos: percorre TODOS
    for (const [seletor, msg] of GRUPOS[passo] || []) {
      if (!validarGrupo(secao, seletor, msg)) valido = false;
    }

    validandoManual = false;
    return valido;
  }

  // Limpa o erro assim que o usuário mexe no campo
  modal.addEventListener('input', (e) => {
    e.target.setCustomValidity?.('');
    limparErro(e.target);
  });

  modal.addEventListener('change', (e) => {
    const alvo = e.target;

    if (alvo.matches('input[type="checkbox"], input[type="radio"]')) {
      modal
        .querySelectorAll(`input[name="${alvo.name}"]`)
        .forEach((i) => {
          i.setCustomValidity('');
          limparErro(i);
        });
    }
  });

  /* ---------- Eventos dos botões ---------- */
  // Só troca de card se os campos obrigatórios do passo atual estiverem preenchidos
  function tentarIrPara(destino) {
    if (!validarPasso(passoAtual())) {
      destacarCampo(primeiroInvalido());
      return;
    }
    irPara(destino);
  }

  btnAvancar.forEach((botao) => {
    botao.addEventListener('click', () => {
      const atual = passoAtual();
      if (atual < TOTAL_PASSOS) tentarIrPara(atual + 1);
    });
  });

  btnVoltar.forEach((botao) => {
    botao.addEventListener('click', () => {
      const atual = passoAtual();
      if (atual > 1) irPara(atual - 1); // Voltar é livre, sem validação
    });
  });

  // Enter avança o card visível (e não o botão do passo 1)
  modal.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' || e.target.tagName === 'TEXTAREA') return;
    if (e.target.matches('button')) return;

    e.preventDefault();
    if (passoAtual() < TOTAL_PASSOS) {
      modal.querySelector(`${SECOES[passoAtual()]} .btnAvancarCard`)?.click();
    }
  });

  // Validação final de todos os passos antes do envio
  function validarTudo(e) {
    let primeiroPasso = null;
    let campoFoco = null;

    for (let passo = 1; passo <= TOTAL_PASSOS; passo++) {
      if (!validarPasso(passo) && primeiroPasso === null) {
        primeiroPasso = passo;
        campoFoco = primeiroInvalido();
      }
    }

    if (primeiroPasso !== null) {
      e.preventDefault();
      irPara(primeiroPasso);
      destacarCampo(campoFoco);
      return false;
    }
    return true;
  }

  if (form) {
    form.addEventListener('submit', validarTudo);
  } else {
    btnSalvar?.addEventListener('click', validarTudo);
  }

  function limparTodosErros() {
    modal.querySelectorAll('.msgErro').forEach((s) => {
      s.textContent = '';
      s.classList.remove('ativo');
    });
    modal.querySelectorAll('.campoInvalido').forEach((c) => c.classList.remove('campoInvalido'));
  }

  function atualizarTextoDropdown(dropdown) {
    const botao = dropdown?.querySelector('button');
    if (!botao) return;
    const selecionados = Array.from(dropdown.querySelectorAll('input:checked'))
      .map((input) => input.closest('.itemDadoDrop')?.querySelector('.selecaoItemNome')?.textContent.trim())
      .filter(Boolean);

    const textoSelecionado = botao.querySelector('.textoDropDisponibilidade');
    if (textoSelecionado) {
      textoSelecionado.textContent = selecionados.length ? selecionados.join(', ') : 'Selecione';
      return;
    }

    botao.innerHTML = selecionados.length ? selecionados.join(', ') : ICONE_SETA;
  }

  function trocarTituloModal(texto) {
    const titulo = modal.querySelector('.modalTitulo');
    const textoTitulo = titulo && Array.from(titulo.childNodes)
      .reverse()
      .find((node) => node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '');
    if (textoTitulo) textoTitulo.nodeValue = ` ${texto} `;
  }

  function resetarFormularioMentor() {
    form?.reset();
    if (!form) return;

    limparTodosErros();
    if (inputIdMentor) inputIdMentor.value = '';
    possuiFotoExistente = false;
    form.action = ACAO_ADICIONAR;
    if (fotoPerfil) {
      fotoPerfil.src = FOTO_PERFIL_PADRAO;
      fotoPerfil.classList.remove('novaFotoPerfil');
    }
    if (fotoBanner) {
      fotoBanner.src = BANNER_PADRAO;
      fotoBanner.classList.remove('novaFotoBanner');
    }
    modal.querySelectorAll('.areaDrop.aberto').forEach((areaDrop) => {
      areaDrop.classList.remove('aberto');
      areaDrop.querySelector('.dropDisponibilidade')?.setAttribute('aria-expanded', 'false');
    });
    modal.querySelectorAll('.areaDrop').forEach(atualizarTextoDropdown);
    irPara(1);
    trocarTituloModal('Cadastro de Mentores');
  }

  modal.closest('dialog')?.addEventListener('close', resetarFormularioMentor);

  function preencherFormularioMentor(mentor) {
    form?.reset();
    if (!form) return;

    limparTodosErros();

    const campos = ['nome', 'email', 'telefone', 'instagram', 'linkedin', 'biografia'];
    campos.forEach((nome) => {
      const campo = form.elements.namedItem(nome);
      if (campo) campo.value = mentor[nome] ?? '';
    });
    if (inputTelefone) inputTelefone.value = window.Mascara.aplicarEm(inputTelefone.value, 'telefone');

    const parceiro = form.querySelector('select[name="id_parceiro"]');
    if (parceiro) {
      parceiro.value = mentor.id_parceiro ?? '';
      parceiro.dispatchEvent(new Event('change', { bubbles: true }));
    }
    form.querySelectorAll('input[name="semana[]"]').forEach((input) => {
      input.checked = Number(mentor[input.value]) === 1;
    });
    form.querySelectorAll('input[name="horario[]"]').forEach((input) => {
      input.checked = Number(mentor[input.value]) === 1;
    });

    const modalidade = { REMOTO: 'online', HIBRIDO: 'hibrido', PRESENCIAL: 'presencial' }[mentor.modalidade];
    const radioModalidade = form.querySelector(`input[name="modalidade"][value="${modalidade}"]`);
    if (radioModalidade) radioModalidade.checked = true;

    const competenciasSelecionadas = new Set(mentor.competencias.map(Number));
    form.querySelectorAll('input[name="competencia[]"]').forEach((input) => {
      input.checked = competenciasSelecionadas.has(IDS_COMPETENCIAS[input.value]);
    });
    modal.querySelectorAll('.areaDrop').forEach(atualizarTextoDropdown);

    if (inputIdMentor) inputIdMentor.value = mentor.id_mentor;
    possuiFotoExistente = Boolean(mentor.foto);
    if (inputFoto) inputFoto.value = '';
    if (inputBanner) inputBanner.value = '';
    if (fotoPerfil) {
      fotoPerfil.src = mentor.foto
        ? `${URL_UPLOAD}Mentor/${encodeURIComponent(mentor.foto)}`
        : FOTO_PERFIL_PADRAO;
      fotoPerfil.classList.remove('novaFotoPerfil');
    }
    if (fotoBanner) {
      fotoBanner.src = mentor.banner
        ? `${URL_UPLOAD}Mentores/Banners/${encodeURIComponent(mentor.banner)}`
        : BANNER_PADRAO;
      fotoBanner.classList.remove('novaFotoBanner');
    }

    form.action = ACAO_ATUALIZAR;
    irPara(1);
    trocarTituloModal('Editar Mentor');
  }

  async function abrirEdicaoMentor(id) {
    try {
      const resposta = await fetch(`/ParkTec/mentores/buscar?id=${encodeURIComponent(id)}`);
      const resultado = await resposta.json();
      if (!resposta.ok || !resultado.sucesso || !resultado.mentor) {
        window.alert(resultado.erro || 'Não foi possível carregar esse mentor.');
        return;
      }

      preencherFormularioMentor(resultado.mentor);
      window.abrirModal('modalCadastroMentor');
    } catch (erro) {
      window.alert('Erro de conexão ao carregar o mentor.');
    }
  }

  document.addEventListener('click', (evento) => {
    const botaoEditar = evento.target.closest('.botaoEditarMentor');
    if (botaoEditar) {
      abrirEdicaoMentor(botaoEditar.dataset.id);
      return;
    }
    if (evento.target.closest('.btnCadastroMentor')) {
      resetarFormularioMentor();
    }
  });

  atualizarBotoes(passoAtual());
})();

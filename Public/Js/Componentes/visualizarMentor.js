(function () {
  const UPLOAD = '/ParkTec/Public/Upload/';
  const FOTO_PADRAO = '/ParkTec/Public/Assets/Icones/Vector.svg';
  const BANNER_PADRAO = '/ParkTec/Public/Assets/Icones/exBanner.svg';

  const DIAS = [
    { chave: 'segunda', rotulo: 'Segunda-feira' },
    { chave: 'terca', rotulo: 'Terça-feira' },
    { chave: 'quarta', rotulo: 'Quarta-feira' },
    { chave: 'quinta', rotulo: 'Quinta-feira' },
    { chave: 'sexta', rotulo: 'Sexta-feira' },
    { chave: 'sabado', rotulo: 'Sábado' },
    { chave: 'domingo', rotulo: 'Domingo' },
  ];

  const TURNOS = [
    { chave: 'matutino', rotulo: 'Matutino' },
    { chave: 'vespertino', rotulo: 'Vespertino' },
    { chave: 'noturno', rotulo: 'Noturno' },
  ];

  const MODALIDADES = {
    REMOTO: 'Online',
    HIBRIDO: 'Híbrido',
    PRESENCIAL: 'Presencial',
    online: 'Online',
    hibrido: 'Híbrido',
    presencial: 'Presencial',
  };

  function formatarTelefone(telefone) {
    if (!telefone) return '';
    const limpo = String(telefone).replace(/\D/g, '');
    if (window.Mascara?.aplicarEm) {
      return window.Mascara.aplicarEm(limpo, 'telefone');
    }
    if (limpo.length === 11) {
      return limpo.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    }
    if (limpo.length === 10) {
      return limpo.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    }
    return telefone;
  }

  function preencherTags(container, itens) {
    if (!container) return;
    container.replaceChildren();
    if (!itens || !itens.length) return; // Mantém vazio caso não haja itens

    itens.forEach((texto) => {
      const tag = document.createElement('span');
      tag.className = 'campoTag';
      tag.textContent = texto;
      container.appendChild(tag);
    });
  }

  function preencherLink(container, url, tipo) {
    if (!container) return;
    container.replaceChildren();
    if (!url || typeof url !== 'string' || !url.trim()) return; // Mantém vazio

    const valorLimpo = url.trim();
    let href = valorLimpo;

    if (!/^https?:\/\//i.test(href)) {
      if (tipo === 'instagram') {
        const usuario = valorLimpo.replace(/^@/, '');
        href = `https://instagram.com/${usuario}`;
      } else if (tipo === 'linkedin') {
        href = `https://${valorLimpo.replace(/^\/+/, '')}`;
      } else {
        href = `https://${valorLimpo}`;
      }
    }

    const a = document.createElement('a');
    a.href = href;
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    a.textContent = valorLimpo;
    container.appendChild(a);
  }

  function limparVisualizacaoMentor() {
    const raiz = document.querySelector('.areaVisualizacaoMentor');
    if (!raiz) return;

    const campo = (nome) => raiz.querySelector(`[data-campo="${nome}"]`);

    const camposTexto = ['nome', 'email', 'telefone', 'parceiro', 'biografia'];
    camposTexto.forEach((nome) => {
      const el = campo(nome);
      if (el) el.textContent = '';
    });

    const statusEl = campo('status');
    if (statusEl) {
      statusEl.textContent = '';
      statusEl.className = 'visStatus status';
    }

    const camposContainers = ['linkedin', 'instagram', 'modalidade', 'dias', 'horarios', 'competencias'];
    camposContainers.forEach((nome) => {
      const el = campo(nome);
      if (el) el.replaceChildren();
    });

    const foto = campo('foto');
    if (foto) {
      foto.src = FOTO_PADRAO;
    }

    const banner = campo('banner');
    if (banner) {
      banner.src = BANNER_PADRAO;
    }
  }

  window.limparVisualizacaoMentor = limparVisualizacaoMentor;

  window.preencherVisualizacaoMentor = function (mentor) {
    const raiz = document.querySelector('.areaVisualizacaoMentor');
    if (!raiz || !mentor) return;

    limparVisualizacaoMentor();

    const campo = (nome) => raiz.querySelector(`[data-campo="${nome}"]`);

    // Textos simples (se não tiver dado, mantém vazio '')
    const elNome = campo('nome');
    if (elNome) elNome.textContent = mentor.nome ? String(mentor.nome).trim() : '';

    const elEmail = campo('email');
    if (elEmail) elEmail.textContent = mentor.email ? String(mentor.email).trim() : '';

    const elTelefone = campo('telefone');
    if (elTelefone) elTelefone.textContent = formatarTelefone(mentor.telefone);

    const elParceiro = campo('parceiro');
    if (elParceiro) elParceiro.textContent = mentor.parceiro_nome ? String(mentor.parceiro_nome).trim() : '';

    const elModalidade = campo('modalidade');
    if (elModalidade) {
      const modFormatada = MODALIDADES[mentor.modalidade] || mentor.modalidade || '';
      preencherTags(elModalidade, modFormatada ? [modFormatada] : []);
    }

    const elBio = campo('biografia');
    if (elBio) elBio.textContent = mentor.biografia ? String(mentor.biografia).trim() : '';

    // Status badge
    const statusEl = campo('status');
    if (statusEl) {
      const ativo = Number(mentor.ativo) === 1;
      statusEl.textContent = ativo ? 'Ativo' : 'Inativo';
      statusEl.className = `visStatus status ${ativo ? 'statusAtivo' : ''}`.trim();
    }

    // Links sociais
    preencherLink(campo('linkedin'), mentor.linkedin, 'linkedin');
    preencherLink(campo('instagram'), mentor.instagram, 'instagram');

    // Imagens
    const foto = campo('foto');
    if (foto) {
      foto.onerror = () => {
        foto.onerror = null;
        foto.src = FOTO_PADRAO;
      };
      foto.src = mentor.foto ? `${UPLOAD}Mentor/${encodeURIComponent(mentor.foto)}` : FOTO_PADRAO;
    }

    const banner = campo('banner');
    if (banner) {
      banner.onerror = () => {
        banner.onerror = null;
        banner.src = BANNER_PADRAO;
      };
      banner.src = mentor.banner ? `${UPLOAD}Mentores/Banners/${encodeURIComponent(mentor.banner)}` : BANNER_PADRAO;
    }

    // Dias disponíveis (semana)
    const diasSelecionados = DIAS.filter((d) => Number(mentor[d.chave]) === 1).map((d) => d.rotulo);
    preencherTags(campo('dias'), diasSelecionados);

    // Horários / Turnos
    const turnosSelecionados = TURNOS.filter((t) => Number(mentor[t.chave]) === 1).map((t) => t.rotulo);
    preencherTags(campo('horarios'), turnosSelecionados);

    // Competências e áreas de atuação
    let competenciasItens = [];
    if (mentor.areas_atuacao) {
      competenciasItens = String(mentor.areas_atuacao)
        .split(',')
        .map((c) => c.trim())
        .filter(Boolean);
    }
    preencherTags(campo('competencias'), competenciasItens);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const dialogVisualizar = document.getElementById('visualizarMentor');
    if (dialogVisualizar) {
      dialogVisualizar.addEventListener('close', limparVisualizacaoMentor);
    }
  });
})();
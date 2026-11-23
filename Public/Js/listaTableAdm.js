const dados = {
    mentores: [
        {
            startup: "Nu Pagamentos S.A.",
            mentor: "Deyvid Michel Oliveira de Jesus"
        },
        {
            startup: "TechVendas Ltda.",
            mentor: "Carlos Silva"
        },
        {
            startup: "TechVendas Ltda.",
            mentor: "Carlos Silva"
        }
    ],

    relatos: [
        {
            startup: "Nu Pagamentos S.A.",
            mentor: "Deyvid Michel Oliveira de Jesus"
        },
        {
            startup: "TechVendas Ltda.",
            mentor: "Carlos Silva"
        }
    ]
};


let tipoLista = "mentores";


function carregarTabela() {

    const tabela = document.getElementById("listaAdm");
    tabela.innerHTML =""
    dados[tipoLista].forEach(item => {

        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${item.startup}</td>

            <td>${item.mentor}</td>

            <td>
                ${tipoLista === "mentores"
                ? `<span class="acao">✓ Validar</span>`
                : `<span class="acao">◉ Ver relato</span>`
            }
            </td>
        `;

        tabela.appendChild(tr);
    });
}


document.getElementById("btnMentores").addEventListener("click", function () {

    tipoLista = "mentores";

    document
        .getElementById("btnMentores")
        .classList.add("ativo");

    document
        .getElementById("btnRelatos")
        .classList.remove("ativo");

    carregarTabela();
});


document.getElementById("btnRelatos").addEventListener("click", function () {

    tipoLista = "relatos";

    document
        .getElementById("btnRelatos")
        .classList.add("ativo");

    document
        .getElementById("btnMentores")
        .classList.remove("ativo");

    carregarTabela();
});


carregarTabela();
/* ============================================================
   CONFIGURAÇÃO — ajuste aqui para integrar ao seu sistema
   ============================================================ */
const API_URL = '';   // ex.: '/api/mentores/123/validacao'  (vazio = modo demonstração)
const MENTOR_ID = 123;
 
/* Substitua este objeto pelo retorno do seu back-end
   (ex.: const mentor = await (await fetch('/api/mentores/123')).json()) */
const mentor = {
  nome: 'Rafael Mendes',
  email: 'rafael.mendes@exemplo.com',
  empresa: 'Startup Hub MS',
  telefone: '(67) 99999-9999',
  instagram: 'rafael.mentor',
  linkedin: 'linkedin.com/in/rafael-mendes',
  foto: '',      // URL da foto de perfil (vazio = iniciais)
  banner: '',    // URL do banner
  dias: ['Segunda', 'Quarta', 'Sexta'],
  horario: '18h às 21h',
  modalidade: 'Híbrido',            // 'Online' | 'Presencial' | 'Híbrido'
  local: 'Sede da Startup Hub MS',  // só é exibido em Presencial ou Híbrido
  biografia: 'Mentor há 6 anos, com experiência em validação de produto, captação de investimento e estruturação comercial de startups em estágio inicial.',
  competencias: ['Produto e Tecnologia', 'Capital & Investimentos', 'Estratégia', 'Networking & Conexões', 'Marketing & Vendas']
};
 
const DIAS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
const EIXOS = {
  'Tecnologia': ['Produto e Tecnologia', 'Operações'],
  'Capital': ['Capital & Investimentos', 'Finanças'],
  'Empreendedor': ['Estratégia', 'Gestão'],
  'Mercado': ['Networking & Conexões', 'Marketing & Vendas', 'Mercados & Clientes'],
  'Gestão': ['Jurídico & PI']
};
 
/* ============================================================ */
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const campo = (rotulo, valor) => `<div><span class="lb">${rotulo}</span><div class="fd">${esc(valor) || '—'}</div></div>`;
 
function renderizar(m) {
  const iniciais = m.nome.split(' ').map(p => p[0]).slice(0, 2).join('').toUpperCase();
  $('avatar').innerHTML = m.foto ? `<img src="${esc(m.foto)}" alt="" style="width:100%;height:100%;object-fit:cover">` : iniciais;
  if (m.banner) $('banner').style.backgroundImage = `url("${esc(m.banner)}")`;
  else $('banner').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-label="Sem banner"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/></svg>';
 
  $('dados').innerHTML =
    campo('Nome', m.nome) + campo('Email', m.email) + campo('Empresa', m.empresa) +
    `<div class="two">${campo('Telefone', m.telefone)}${campo('Instagram', m.instagram)}</div>` +
    campo('Linkedin', m.linkedin);
 
  $('dias').innerHTML = DIAS.map(d => {
    const on = m.dias.includes(d);
    return `<div class="day${on ? ' on' : ''}"><i>${on ? '✓' : ''}</i>${d}</div>`;
  }).join('');
 
  $('horario').innerHTML = campo('Horários', m.horario) + campo('Modalidade', m.modalidade);
  const precisaLocal = ['Presencial', 'Híbrido'].includes(m.modalidade);
  $('local').innerHTML = precisaLocal ? campo('Local do atendimento', m.local) : '';
  $('bio').textContent = m.biografia;
 
  $('competencias').innerHTML = Object.entries(EIXOS).map(([eixo, itens]) =>
    `<div class="grp"><b>${eixo}</b><div class="chips">${itens.map(i =>
      `<span class="chip${m.competencias.includes(i) ? ' on' : ''}">${esc(i)}</span>`).join('')}</div></div>`).join('');
}
 
/* Envia a decisão ao back-end. Em modo demonstração apenas simula. */
async function enviarDecisao(decisao, motivo = '') {
  if (!API_URL) { await new Promise(r => setTimeout(r, 400)); return; }
  const resp = await fetch(API_URL, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ mentorId: MENTOR_ID, status: decisao, motivo })
  });
  if (!resp.ok) throw new Error('Falha ao salvar');
}
 
function concluir(tipo, texto) {
  const badge = $('status');
  badge.className = 'badge ' + (tipo === 'ok' ? 'ok' : 'bad');
  badge.textContent = tipo === 'ok' ? 'Validado' : 'Recusado';
  $('acoes').style.display = 'none';
  $('rejeicao').classList.remove('show');
  const r = $('resultado');
  r.className = 'result show ' + tipo;
  r.textContent = texto;
}
 
async function decidir(decisao, motivo, botoes) {
  botoes.forEach(b => b.disabled = true);
  try {
    await enviarDecisao(decisao, motivo);
    if (decisao === 'aprovado') concluir('ok', 'Mentor validado. O cadastro foi liberado.');
    else concluir('bad', 'Cadastro recusado. O mentor será notificado com o motivo informado.');
  } catch (e) {
    $('erro').textContent = 'Não foi possível salvar. Verifique a conexão e tente de novo.';
    $('rejeicao').classList.add('show');
    botoes.forEach(b => b.disabled = false);
  }
}
 
$('validar').onclick = () => decidir('aprovado', '', [$('validar'), $('recusar')]);
$('recusar').onclick = () => { $('rejeicao').classList.add('show'); $('motivo').focus(); };
$('cancelar').onclick = () => { $('rejeicao').classList.remove('show'); $('erro').textContent = ''; };
$('confirmar').onclick = () => {
  const motivo = $('motivo').value.trim();
  if (!motivo) { $('erro').textContent = 'Informe o motivo da recusa.'; $('motivo').focus(); return; }
  $('erro').textContent = '';
  decidir('recusado', motivo, [$('confirmar'), $('cancelar'), $('recusar'), $('validar')]);
};
$('motivo').oninput = () => { $('erro').textContent = ''; };
$('fechar').onclick = () => { $('modal').style.display = 'none'; };
 
renderizar(mentor);
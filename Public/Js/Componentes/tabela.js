const url = window.location.pathname.split('/')[2];
 
 
async function buscar(endpoint) {
    const resposta = await fetch(`/ParkTec/${endpoint}/tabela`);
    const dados = await resposta.json();
    return dados;
}
 
const BASE_UPLOAD = "/ParkTec/Public/Upload/"

function escaparHtml(valor) {
    return String(valor ?? '').replace(/[&<>"']/g, caractere => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[caractere]);
}
 
// Coloque como deve ser o corpo da sua tabela aqui ↓↓
 
const templateCorpo = {
    'parceiro': (p) => {
        const ativo = Number(p.ativo) === 1
        const digitosTelefone = String(p.telefone ?? '').replace(/\D/g, '')
        const numeroWhatsapp = digitosTelefone.length === 10 || digitosTelefone.length === 11
            ? `55${digitosTelefone}`
            : digitosTelefone
 
        return `<tr class="linhaTabelaCorpo linhaTabelaCorpoParceiros" data-id="${p.id}">
            <td class="itemTabelaCorpo itemTabelaParceiros1">
                        <p>${p.nome}</p>
                    </td>
                    <td class="itemTabelaCorpo itemTabelaParceiros2 conteudoTabelaParceiros2">
                        ${p.area_nome}
                    </td>
                    <td class="itemTabelaCorpo itemTabelaParceiros3">${mascaraCnpj(p.cnpj)}</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros4">${p.email_representante}</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros5">${numeroWhatsapp ? `<a href="https://wa.me/${numeroWhatsapp}" target="_blank" rel="noopener noreferrer" aria-label="Conversar pelo WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>` : '<i class="fa-brands fa-whatsapp"></i>'}${mascaraTelefone(p.telefone)}</td>
                    <td class="itemTabelaCorpo itemTabelaParceiros6">
                        <span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">${ativo ? 'Ativo' : 'Inativo'}</span>
                    </td>
                    <td class="itemTabelaCorpo itemTabelaParceiros7">
                        <div class="wrapperTabelaParceiros7">
                        <div class="btnSwitch">
                                <label class="switch">
                                    <input type="checkbox" name="toggle" class="toggle" ${ativo ? 'checked' : ''}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <button class="botaoEditarParceiro botaoEditar" id="abrirParceiro${p.id}" data-id="${p.id}">
                                <i> <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8586 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z" fill="#2F3C54"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z" fill="#2F3C54"/>
            </svg></i>
                            </button>
                        </div>
                    </td>
                    </tr>`
    },
 
    'startup': (s) => {
        const ativo = Number(s.ativo) === 1
        const id = Number(s.id)

        const mapaSetor = {
            tech: 'Tecnologia', health: 'Saúde', edu: 'Educação', fin: 'Finanças',
            agro: 'Agronegócio', retail: 'Varejo', logistica: 'Logística', outros: 'Outros'
        }

        const setorLabel = mapaSetor[s.setor_atuacao] || (s.setor_atuacao || '—')
        const localizacao = [s.cidade, s.uf].filter(Boolean).join(' / ') || '—'

        return `<tr class="linhaTabelaCorpo linhaTabelaCorpoStartups" data-id="${id}">
            <td class="itemTabelaCorpo itemTabelaStartups1">
                <p>${escaparHtml(s.nome)}</p>
            </td>
            <td class="itemTabelaCorpo itemTabelaStartups2">${escaparHtml(setorLabel)}</td>
            <td class="itemTabelaCorpo itemTabelaStartups3">${escaparHtml(mascaraCnpj(s.cnpj))}</td>
            <td class="itemTabelaCorpo itemTabelaStartups4">${escaparHtml(localizacao)}</td>
            <td class="itemTabelaCorpo itemTabelaStartups5">${escaparHtml(s.trl ?? '—')}</td>
            <td class="itemTabelaCorpo itemTabelaStartups6">
                <span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">${ativo ? 'Ativo' : 'Inativo'}</span>
            </td>
            <td class="itemTabelaCorpo itemTabelaStartups7">
                <div class="wrapperTabelaStartups7">
                    <div class="btnSwitch">
                        <label class="switch">
                            <input type="checkbox" name="toggle" class="toggle" ${ativo ? 'checked' : ''}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <button type="button" class="botaoEditarStartup botaoEditar" id="abrirStartup${id}" data-id="${id}" aria-label="Editar ${escaparHtml(s.nome)}" title="Editar">
                        <i><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8586 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z" fill="#2F3C54"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z" fill="#2F3C54"/>
            </svg></i>
                    </button>
                </div>
            </td>
        </tr>`
    },
    'mentor': (m) => {
        const ativo = Number(m.ativo) === 1;
        return `                
                <tr class="linhaTabelaCorpo linhaTabelaCorpoMentores" data-id="${m.id_usuario}">
                    <td class="itemTabelaCorpo itemTabelaMentores1">${m.nome}</td>
                    <td class="itemTabelaCorpo itemTabelaMentores2">${m.areas_atuacao}</td>
                    <td class="itemTabelaCorpo itemTabelaMentores3">${m.email}</td>
                    <td class="itemTabelaCorpo itemTabelaMentores4">${mascaraTelefone(m.telefone)}</td>
                    <td class="itemTabelaCorpo itemTabelaMentores6">
                        <span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">${ativo ? 'Ativo' : 'Inativo'}</span>
                    </td>
                    <td class="itemTabelaCorpo itemTabelaParceiros7">
                        <div class="wrapperTabelaParceiros7">
                        <div class="btnSwitch">
                                <label class="switch">
                                    <input type="checkbox" name="toggle" class="toggle" ${ativo ? 'checked' : ''}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <button type="button" class="botaoEditarMentor botaoEditar" id="abrirMentor${m.id_mentor}" data-id="${m.id_mentor}">
                                <i> <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8586 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z" fill="#2F3C54"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z" fill="#2F3C54"/>
            </svg></i>
                            </button>
                        </div>
                    </td>
                </tr>`

    },
    'questionario': (q) => {
        const ativo = Number(q.ativo) === 1;

        return `
    <tr class="linhaTabelaCorpo" data-id="${q.id}">
        <td class="itemTabelaCorpo itemTabelaQuestionario1">
            <p>${q.nome}</p>
        </td>
 
        <td class="itemTabelaCorpo itemTabelaQuestionario2">
            ${q.quantidade_questoes}
        </td>
 
        <td class="itemTabelaCorpo itemTabelaQuestionario3">
            <span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">
                ${ativo ? 'Ativo' : 'Inativo'}
            </span>
        </td>
 
        <td class="itemTabelaCorpo itemTabelaQuestionario4">
            <div class="wrapperTabelaQuestionario">
 
                <div class="btnSwitch">
                    <label class="switch">
                        <input
                            type="checkbox"
                            name="toggle"
                            class="toggle"
                            ${ativo ? 'checked' : ''}
                        >
                        <span class="slider"></span>
                    </label>
                </div>

                <button
                    class="botaoEditarQuestionario botaoEditar abrirModal"
                    data-modal="modalAtualizarQuestionario"
                    data-id="${q.id}"
                >
                    <i>
                        <svg
                            width="21"
                            height="20"
                            viewBox="0 0 21 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8586 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z"
                                fill="#2F3C54"
                            />
 
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z"
                                fill="#2F3C54"
                            />
                        </svg>
                    </i>
                </button>

                <button
                    class="botaoVisualizarQuestionario"
                    type="button"
                    data-id="${q.id}"
                    aria-label="Visualizar questionário"
                >
                    <i>
                        <svg
                            width="22"
                            height="16"
                            viewBox="0 0 22 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M11 0.5C5.5 0.5 1.25 5.5 0.5 8C1.25 10.5 5.5 15.5 11 15.5C16.5 15.5 20.75 10.5 21.5 8C20.75 5.5 16.5 0.5 11 0.5ZM11 13.5C7.1 13.5 3.9 10.4 2.65 8C3.9 5.6 7.1 2.5 11 2.5C14.9 2.5 18.1 5.6 19.35 8C18.1 10.4 14.9 13.5 11 13.5Z"
                                fill="#2F3C54"
                            />
                            <path
                                d="M11 4.5C9.07 4.5 7.5 6.07 7.5 8C7.5 9.93 9.07 11.5 11 11.5C12.93 11.5 14.5 9.93 14.5 8C14.5 6.07 12.93 4.5 11 4.5ZM11 9.5C10.17 9.5 9.5 8.83 9.5 8C9.5 7.17 10.17 6.5 11 6.5C11.83 6.5 12.5 7.17 12.5 8C12.5 8.83 11.83 9.5 11 9.5Z"
                                fill="#2F3C54"
                            />
                        </svg>
                    </i>
                </button>
 
            </div>
        </td>
    </tr>`;
    },
    'questao': (q) => {
        const ativo = Number(q.ativo) === 1;

        return `
    <tr class="linhaTabelaCorpo" data-id="${q.id}">
        <td class="itemTabelaCorpo itemTabelaQuestionario1">
            <p>${q.pergunta}</p>
        </td>

        <td class="itemTabelaCorpo itemTabelaQuestionario2">
            ${q.eixos || q.competencia || 'Sem eixo'}
        </td>

        <td class="itemTabelaCorpo itemTabelaQuestionario3">
            <span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">
                ${ativo ? 'Ativo' : 'Inativo'}
            </span>
        </td>

        <td class="itemTabelaCorpo itemTabelaQuestionario4">
            <div class="wrapperTabelaQuestionario">

                <div class="btnSwitch">
                    <label class="switch">
                        <input
                            type="checkbox"
                            name="toggle"
                            class="toggle"
                            ${ativo ? 'checked' : ''}
                        >
                        <span class="slider"></span>
                    </label>
                </div>

                <button
                    class="botaoEditarQuestao botaoEditar abrirModal"
                    data-modal="modalAtualizarQuestao"
                    data-id="${q.id}"
                >
                    <i>
                        <svg
                            width="21"
                            height="20"
                            viewBox="0 0 21 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8586 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z"
                                fill="#2F3C54"
                            />

                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z"
                                fill="#2F3C54"
                            />
                        </svg>
                    </i>
                </button>

            </div>
        </td>
    </tr>`;
    },
    'mentoriaAndamento': (ma) => `<tr class="linhaTabelaCorpo linhaTabelaCorpoATM">
                                
                                <td class="itemTabelaCorpo itemTabelaATM2 itemTabelaCorpoATM itemTabelaHistorico2 nomeHistoricoReunioesATM">
                                        <p class="nomeHistoricoReunioesATM "> ${ma.nomestartup} </p>
                                    </div>
                                </td>
                                <td class="itemTabelaCorpo itemTabelaATM3 itemTabelaCorpoATM itemTabelaHistorico3 itemHistoricoATM">
                                    <p class="itemTabelaHistorico3 mercadosHistoricoATM"> ${ma.areaatuacao} </p>
                                </td>
                                <td class="itemTabelaCorpo itemTabelaATM4 itemTabelaCorpoATM itemTabelaHistorico4"> ${ma.email} ${ma.linkedin} ${ma.instagram} </td>
                                </td>
                            </tr>`,

    'equipe': (e) => {
        const ativo = Number(e.ativo) === 1
        const digitosTelefone = String(e.telefone ?? '').replace(/\D/g, '')
        const numeroWhatsapp = digitosTelefone.length === 10 || digitosTelefone.length === 11
            ? `55${digitosTelefone}`
            : digitosTelefone

        return `<tr class="linhaTabelaCorpo linhaTabelaCorpoEquipe" data-id="${Number(e.id)}">
        <td class="itemTabelaCorpo itemTabelaEquipe1"><p>${escaparHtml(e.nome)}</p></td>
        <td class="itemTabelaCorpo itemTabelaEquipe2">${escaparHtml(e.email)}</td>
        <td class="itemTabelaCorpo itemTabelaEquipe3">${numeroWhatsapp ? `<a href="https://wa.me/${numeroWhatsapp}" target="_blank" rel="noopener noreferrer" aria-label="Conversar com ${escaparHtml(e.nome)} pelo WhatsApp"><i class="fa-brands fa-whatsapp"></i></a> ` : ''}${escaparHtml(e.telefone)}</td>
        <td class="itemTabelaCorpo itemTabelaEquipe4">${escaparHtml(e.cpf)}</td>
        <td class="itemTabelaCorpo itemTabelaEquipe5">${escaparHtml(e.cargo)}</td>
        <td class="itemTabelaCorpo itemTabelaEquipe6">${escaparHtml(e.perfil)}</td>
        <td class="itemTabelaCorpo itemTabelaEquipeStatus"><span class="textoStatus status ${ativo ? 'statusAtivo' : ''}">${ativo ? 'Ativo' : 'Inativo'}</span></td>
        <td class="itemTabelaCorpo itemTabelaEquipe7"><div class="wrapperTabelaParceiros7 equipeAcoesTabela"><div class="btnSwitch"><label class="switch" aria-label="Alterar status de ${escaparHtml(e.nome)}"><input type="checkbox" name="toggle" class="toggle" ${ativo ? 'checked' : ''}><span class="slider"></span></label></div><button class="botaoEditarParceiro botaoEditar equipeBotaoEditar" id="editarEquipe${Number(e.id)}" type="button" data-id="${Number(e.id)}" aria-label="Editar ${escaparHtml(e.nome)}" title="Editar"><i><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.9402 1.98054C20.0687 2.10941 20.1408 2.28395 20.1408 2.46592C20.1408 2.64788 20.0687 2.82242 19.9402 2.95129L18.5061 4.38679L15.7561 1.63679L17.1902 0.201291C17.3192 0.0724044 17.494 0 17.6763 0C17.8587 0 18.0334 0.0724044 18.1624 0.201291L19.9402 1.97917V1.98054ZM17.534 5.35754L14.784 2.60754L5.4161 11.9768C5.34042 12.0525 5.28345 12.1447 5.24973 12.2463L4.14285 15.5655C4.12278 15.6261 4.11993 15.691 4.13462 15.753C4.14931 15.815 4.18097 15.8718 4.22605 15.9168C4.27113 15.9619 4.32786 15.9936 4.3899 16.0083C4.45194 16.023 4.51684 16.0201 4.57735 16L7.8966 14.8932C7.99803 14.8598 8.0903 14.8033 8.1661 14.7282L17.534 5.35754Z" fill="#2F3C54"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 17.876C0 18.423 0.217299 18.9476 0.604092 19.3344C0.990886 19.7212 1.51549 19.9385 2.0625 19.9385H17.1875C17.7345 19.9385 18.2591 19.7212 18.6459 19.3344C19.0327 18.9476 19.25 18.423 19.25 17.876V9.62598C19.25 9.44364 19.1776 9.26877 19.0486 9.13984C18.9197 9.01091 18.7448 8.93848 18.5625 8.93848C18.3802 8.93848 18.2053 9.01091 18.0764 9.13984C17.9474 9.26877 17.875 9.44364 17.875 9.62598V17.876C17.875 18.0583 17.8026 18.2332 17.6736 18.3621C17.5447 18.491 17.3698 18.5635 17.1875 18.5635H2.0625C1.88016 18.5635 1.7053 18.491 1.57636 18.3621C1.44743 18.2332 1.375 18.0583 1.375 17.876V2.75098C1.375 2.56864 1.44743 2.39377 1.57636 2.26484C1.7053 2.13591 1.88016 2.06348 2.0625 2.06348H11C11.1823 2.06348 11.3572 1.99104 11.4861 1.86211C11.6151 1.73318 11.6875 1.55831 11.6875 1.37598C11.6875 1.19364 11.6151 1.01877 11.4861 0.889841C11.3572 0.760909 11.1823 0.688477 11 0.688477H2.0625C1.51549 0.688477 0.990886 0.905775 0.604092 1.29257C0.217299 1.67936 0 2.20397 0 2.75098V17.876Z" fill="#2F3C54"/>
            </svg></i></button></div></td>
    </tr>`
    }
}
 
//Ao adicionar um novo template, adicione aqui também ↓↓
const TEMPLATE_POR_TELA = {
    'parceiros': templateCorpo.parceiro,
    'questionario': templateCorpo.questionario,
    'questao': templateCorpo.questao,
    'mentores': templateCorpo.mentor,
    'startups': templateCorpo.startup,
    'equipe': templateCorpo.equipe
}
 
function recortarPagina(dados) {
    if (!temPaginacao) return dados
 
    const inicio = (state.page - 1) * state.perPage
 
    return dados.slice(inicio, inicio + state.perPage)
}
 
function montarLinhas(dados) {
    const template = TEMPLATE_POR_TELA[url]
 
    if (!template) return ''
 
    return dados.map(item => template(item)).join('')
}
 
const QUANTIDADES_POR_PAGINA = [1, 5, 10, 25, 50]
 
// Usado por telas que paginam sem oferecer o select de quantidade.
const PADRAO_POR_PAGINA = 10
 
const parametrosUrl = new URLSearchParams(location.search)
 
// a escolha fica na URL porque trocar de página recarrega a tela
const quantidadeEscolhida = Number(parametrosUrl.get('quantidade'))
 
const perPage = QUANTIDADES_POR_PAGINA.includes(quantidadeEscolhida)
    ? quantidadeEscolhida
    : PADRAO_POR_PAGINA
 
const state = {
    page: Number(parametrosUrl.get('pagina')) || 1,
    perPage,
    totalPage: 1,
    maxVisibleButtons: 5
}
 
const html = {
    get(element) {
        return document.querySelector(element)
    }
}
 
const temPaginacao = Boolean(html.get('.paginacao'))
 
 
function irParaPagina(numero) {
    const destino = Math.min(Math.max(1, Number(numero) || 1), state.totalPage)
 
    if (destino === state.page) return
 
    const url = new URL(location.href)
    url.searchParams.set('pagina', destino)
    location.assign(url)
}
 
const controls = {
    createListeners() {
        if (!temPaginacao) return
 
        const acoes = {
            '.primeiro': () => irParaPagina(1),
            '.anterior': () => irParaPagina(state.page - 1),
            '.proximo': () => irParaPagina(state.page + 1),
            '.ultimo': () => irParaPagina(state.totalPage)
        }
 
        // cada seta é opcional — a tela pode trazer só parte dos controles
        Object.entries(acoes).forEach(([seletor, acao]) => {
            html.get(seletor)?.addEventListener('click', acao)
        })
    }
}
 
const quantidade = {
    montar() {
        const select = html.get('#quantidadePagina')
 
        // nem toda tela que usa a tabela tem esse select
        if (!select) return
 
        select.innerHTML = QUANTIDADES_POR_PAGINA
            .map(valor => `<option value="${valor}">${valor} por página</option>`)
            .join('')
 
        select.value = state.perPage
 
        select.addEventListener('change', () => {
            const url = new URL(location.href)
 
            url.searchParams.set('quantidade', select.value)
 
            // a página atual pode nem existir com o novo tamanho
            url.searchParams.set('pagina', 1)
 
            location.assign(url)
        })
    }
}
 
const list = {
    update() {
        const tbody = html.get('.tabelaCorpo')
 
        if (!tbody) return
 
        tbody.innerHTML = montarLinhas(recortarPagina(state.dados))
    }
}
 
const buttons = {
    create(number, numeros) {
        const button = document.createElement('div')
        button.innerHTML = number
 
        if (state.page === number) {
            button.classList.add('paginaAtual')
        }
 
        button.addEventListener('click', (event) => {
            irParaPagina(event.currentTarget.innerText)
        })
 
        numeros.appendChild(button)
    },
    update() {
        const numeros = html.get('.paginacao .numeros')
 
        // tela sem paginação (ou só com as setas) não tem o que numerar
        if (!numeros) return
 
        numeros.innerHTML = ''
 
        const { maxLeft, maxRight } = buttons.calculateMaxVisible()
 
        for (let page = maxLeft; page <= maxRight; page++) {
            buttons.create(page, numeros)
        }
        if (state.page == 1) {
            html.get('.primeiro')?.classList.add("setasPaginacaoInativo");
            html.get('.anterior')?.classList.add("setasPaginacaoInativo");
        }
 
        if (state.page == state.totalPage) {
            html.get('.proximo')?.classList.add("setasPaginacaoInativo");
            html.get('.ultimo')?.classList.add("setasPaginacaoInativo");
        }
    },
    calculateMaxVisible() {
        const { maxVisibleButtons } = state
 
        let maxLeft = state.page - Math.floor(maxVisibleButtons / 2)
        let maxRight = state.page + Math.floor(maxVisibleButtons / 2)
 
        if (maxLeft < 1) {
            maxLeft = 1
            maxRight = maxVisibleButtons
        }
 
        if (maxRight > state.totalPage) {
            maxLeft = state.totalPage - (maxVisibleButtons - 1)
            maxRight = state.totalPage
 
            if (maxLeft < 1) {
                maxLeft = 1
            }
        }
 
        return { maxLeft, maxRight }
    }
}
 
function update() {
    list.update()
    buttons.update()
}
 
(async function init() {
    if (!TEMPLATE_POR_TELA[url] || !html.get('.tabelaCorpo')) return
 
    state.dados = await buscar(url)
 
    if (temPaginacao) {
        state.totalPage = Math.max(1, Math.ceil(state.dados.length / state.perPage))
 
        state.page = Math.min(Math.max(1, state.page), state.totalPage)
    }
 
    update()
    controls.createListeners()
    quantidade.montar()
})()
 
function valorInput(indice, valor) {
    document.querySelector(indice).setAttribute('value', valor);
}

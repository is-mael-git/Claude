(function () {
    console.log("calendarioAdm.js carregado");

    const calendario = document.getElementById("calendario");
    const mesAno = document.getElementById("mesAno");
    const btnAnterior = document.getElementById("anterior");
    const btnProximo = document.getElementById("proximo");

    if (!calendario || !mesAno || !btnAnterior || !btnProximo) {
        console.error("Calendário: elemento não encontrado no HTML", {
            calendario, mesAno, btnAnterior, btnProximo
        });
        return;
    }

    const meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    const diasSemana = [
        "Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"
    ];

    const dataAtual = new Date();
    let mes = dataAtual.getMonth();
    let ano = dataAtual.getFullYear();

    /*
     * Reuniões mentor <-> startup.
     * Chave: "AAAA-MM-DD"
     */
    let reunioes = {
        "2026-10-08": [
            { id: 1, mentor: "Ana Souza", startup: "AgroTech", hora: "14:00" }
        ],
        "2026-10-15": [
            { id: 2, mentor: "Carlos Lima", startup: "FinPay", hora: "10:00" },
            { id: 3, mentor: "Ana Souza", startup: "EduHub", hora: "16:30" }
        ]
    };

    /* ===================== MODAL ===================== */

    // CSS do modal injetado pelo JS (prefixo rm- para não conflitar com o site)
    const estilo = document.createElement("style");
    estilo.textContent = `
        .rm-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .rm-caixa {
            background: #fff;
            color: #000;
            width: 90%;
            max-width: 420px;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            padding-bottom: 10px;
            font-family: inherit;
        }
        .rm-topo {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--cor-fundo-escuro, #222);
            color: var(--cor-branco, #fff);
            padding: 12px 16px;
            border-radius: 10px 10px 0 0;
        }
        .rm-titulo {
            font-size: 16px;
            margin: 0;
            color: var(--cor-branco, #fff);
        }
        .rm-fechar {
            background: none;
            border: none;
            color: var(--cor-branco, #fff);
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }
        .rm-item {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e5e5;
        }
        .rm-hora {
            background: #6838d8;
            color: var(--cor-branco, #fff);
            font-weight: bold;
            font-size: 13px;
            padding: 4px 8px;
            border-radius: 6px;
        }
        .rm-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 14px;
        }
    `;
    document.head.appendChild(estilo);

    function escapeHTML(texto) {
        const div = document.createElement("div");
        div.textContent = texto;
        return div.innerHTML;
    }

    const overlay = document.createElement("div");
    overlay.className = "rm-overlay";
    overlay.innerHTML = `
        <div class="rm-caixa">
            <div class="rm-topo">
                <h3 class="rm-titulo"></h3>
                <button class="rm-fechar" type="button" aria-label="Fechar">&times;</button>
            </div>
            <div class="rm-lista"></div>
        </div>
    `;
    document.body.appendChild(overlay);

    const tituloModal = overlay.querySelector(".rm-titulo");
    const listaModal = overlay.querySelector(".rm-lista");

    function abrirModal(chave) {
        console.log("Abrindo modal para:", chave);

        const lista = reunioes[chave] || [];
        const [a, m, d] = chave.split("-");

        tituloModal.textContent = `Reuniões de ${d}/${m}/${a}`;

        listaModal.innerHTML = lista
            .map((r) => `
                <div class="rm-item">
                    <span class="rm-hora">${escapeHTML(r.hora)}</span>
                    <div class="rm-info">
                        <strong>${escapeHTML(r.startup)}</strong>
                        <span>Mentor: ${escapeHTML(r.mentor)}</span>
                    </div>
                </div>
            `)
            .join("");

        overlay.style.display = "flex";
    }

    function fecharModal() {
        overlay.style.display = "none";
    }

    overlay.addEventListener("click", (e) => {
        if (e.target === overlay || e.target.closest(".rm-fechar")) {
            fecharModal();
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") fecharModal();
    });

    /* ===================== CALENDÁRIO ===================== */

    function gerarChave(a, m, d) {
        return `${a}-${String(m + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
    }

    function criarDia(data, foraDoMes) {
        const divDia = document.createElement("div");
        divDia.classList.add("dia");

        const hoje = new Date();

        if (foraDoMes) {
            divDia.classList.add("fora-mes");
        } else if (
            data.getDate() === hoje.getDate() &&
            data.getMonth() === hoje.getMonth() &&
            data.getFullYear() === hoje.getFullYear()
        ) {
            divDia.classList.add("hoje");
        }

        divDia.innerHTML = `
            <span class="semana">${diasSemana[data.getDay()]}</span>
            <span class="numero">${String(data.getDate()).padStart(2, "0")}</span>
        `;

        if (!foraDoMes) {
            const chave = gerarChave(data.getFullYear(), data.getMonth(), data.getDate());
            const reunioesDoDia = reunioes[chave] || [];

            if (reunioesDoDia.length > 0) {
                divDia.classList.add("com-reuniao");
                divDia.style.cursor = "pointer";
                divDia.addEventListener("click", () => abrirModal(chave));

                const marca = document.createElement("span");
                marca.classList.add("reuniao-marca");
                marca.textContent = reunioesDoDia.length;
                marca.title = reunioesDoDia
                    .map((r) => `${r.hora} - ${r.mentor} x ${r.startup}`)
                    .join("\n");

                divDia.appendChild(marca);
            }
        }

        return divDia;
    }

    function renderizarCalendario() {
        calendario.innerHTML = "";
        mesAno.textContent = `${meses[mes]} ${ano}`;

        const ultimoDia = new Date(ano, mes + 1, 0).getDate();
        const ultimoDiaMesAnterior = new Date(ano, mes, 0).getDate();
        const primeiroDiaSemana = new Date(ano, mes, 1).getDay();

        for (let i = primeiroDiaSemana - 1; i >= 0; i--) {
            const data = new Date(ano, mes - 1, ultimoDiaMesAnterior - i);
            calendario.appendChild(criarDia(data, true));
        }

        for (let dia = 1; dia <= ultimoDia; dia++) {
            calendario.appendChild(criarDia(new Date(ano, mes, dia), false));
        }

        const resto = (primeiroDiaSemana + ultimoDia) % 7;
        if (resto !== 0) {
            for (let dia = 1; dia <= 7 - resto; dia++) {
                calendario.appendChild(criarDia(new Date(ano, mes + 1, dia), true));
            }
        }

        calendario.classList.toggle("seis-semanas", primeiroDiaSemana + ultimoDia > 35);
    }

    btnProximo.addEventListener("click", () => {
        mes++;
        if (mes > 11) {
            mes = 0;
            ano++;
        }
        renderizarCalendario();
    });

    btnAnterior.addEventListener("click", () => {
        mes--;
        if (mes < 0) {
            mes = 11;
            ano--;
        }
        renderizarCalendario();
    });

    renderizarCalendario();
})();
/* =========================================================
   MENU LATERAL — Script principal
   Controla: navegação ativa, toggle abrir/fechar,
   persistência via localStorage, menu mobile (hamburger),
   pesquisa desktop e mobile
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    /* SELETORES — Elementos do DOM */
    const sidebar = document.querySelector('.menuLateral');
    const opcoesMenu = document.querySelectorAll('.menuLateralCaixaOpcaoMenu');
    const toggleBtn = document.getElementById('openBtnMenuLateral');

    // O seu botão checkbox
    const btnHamburgerMenuLateral = document.getElementById('burger');

    // Seleciona automaticamente a label associada ao ID 'burger'
    const labelHamburguer = document.querySelector(`label[for="burger"]`) || document.querySelector('.menuLateralBurger');

    /* NAVEGAÇÃO — Destaque do item ativo */
    function ativarOpcaoMenu(opcaoClicada) {
        opcoesMenu.forEach(function (opcao) {
            opcao.classList.remove('active');
        });
        opcaoClicada.classList.add('active');
    }

    opcoesMenu.forEach(function (opcao) {
        opcao.addEventListener('click', function () {
            ativarOpcaoMenu(this);
        });
    });

    /* TOGGLE — Expandir / Recolher menu (desktop) */
    function toggleSidebar() {
        sidebar.classList.toggle('fechado');
        var estaFechado = sidebar.classList.contains('fechado');
        localStorage.setItem('menuFechado', estaFechado ? 'true' : 'false');
        document.documentElement.classList.toggle('menuLateralEstadoFechado', estaFechado);
    }

    function restaurarEstadoMenu() {
        var menuFechado = localStorage.getItem('menuFechado');
        if (menuFechado === 'true') {
            sidebar.classList.add('fechado');
            document.documentElement.classList.add('menuLateralEstadoFechado');
        } else {
            document.documentElement.classList.remove('menuLateralEstadoFechado');
        }
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    restaurarEstadoMenu();

    /* =====================================================
       MENU MOBILE — Hamburger 
    ===================================================== */
    btnHamburgerMenuLateral.addEventListener('change', function () {
        if (this.checked) {
            sidebar.classList.add('mobileAberto');
        } else {
            sidebar.classList.remove('mobileAberto');
        }
    });

    // Fecha o menu ao clicar fora
    document.addEventListener('click', function (e) {
        const menuAberto = sidebar.classList.contains('mobileAberto');

        const clicouForaSidebar = !sidebar.contains(e.target);
        const clicouNoCheckbox = btnHamburgerMenuLateral.contains(e.target);
        const clicouNaLabel = labelHamburguer && labelHamburguer.contains(e.target);

        if (menuAberto && clicouForaSidebar && !clicouNoCheckbox && !clicouNaLabel) {
            sidebar.classList.remove('mobileAberto');
            btnHamburgerMenuLateral.checked = false; // Desmarca o checkbox com segurança
        }
    });


    /* =====================================================
       PESQUISA — Mobile (expandir/recolher)
    ===================================================== */
    const containerPesquisaMobile = document.querySelector('.menuLateralContainerPesquisarMobile');
    const btnPesquisaMobile = containerPesquisaMobile.querySelector('.menuLateralBotaoNotificacao');
    const inputPesquisaMobile = containerPesquisaMobile.querySelector('.menuLateralInputPesquisar');

    btnPesquisaMobile.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();

        containerPesquisaMobile.classList.toggle('expandido');

        if (containerPesquisaMobile.classList.contains('expandido')) {
            setTimeout(() => {
                inputPesquisaMobile.focus();
            }, 100);
        }
    });

    document.addEventListener('click', (e) => {
        if (containerPesquisaMobile.classList.contains('expandido') && !containerPesquisaMobile.contains(e.target)) {
            containerPesquisaMobile.classList.remove('expandido');
        }
    });


    /* =====================================================
       PESQUISA — Desktop (dropdown de resultados)
       [MÁSCARA — ainda sem fonte de dados real]
    ===================================================== */
    const formPesquisa = document.querySelector('.menuLateralContainerPesquisar');
    const inputPesquisa = document.getElementById('pesquisar');
    const painelPesquisa = document.getElementById('pesquisaResultados');
    const listaPesquisa = document.getElementById('listaResultadosPesquisa');
    const semResultadoPesquisa = document.getElementById('pesquisaSemResultado');

    if (formPesquisa && inputPesquisa && painelPesquisa) {

        formPesquisa.addEventListener('submit', (e) => e.preventDefault());

        function abrirPainelPesquisa() {
            painelPesquisa.classList.add('aberto');
        }

        function fecharPainelPesquisa() {
            painelPesquisa.classList.remove('aberto');
        }

        function renderizarPesquisa(filtro) {
            listaPesquisa.innerHTML = '';
            semResultadoPesquisa.style.display = 'block';
        }

        inputPesquisa.addEventListener('input', () => {
            const valor = inputPesquisa.value;

            if (valor.trim().length === 0) {
                fecharPainelPesquisa();
                return;
            }

            renderizarPesquisa(valor);
            abrirPainelPesquisa();
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.menuLateralContainerPesquisar')) {
                fecharPainelPesquisa();
            }
        });
    }

});

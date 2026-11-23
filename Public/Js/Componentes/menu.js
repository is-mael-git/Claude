/* =========================================================
   MENU LATERAL — Script principal
   Controla: navegação ativa, toggle abrir/fechar,
   persistência via localStorage, menu mobile (hamburger),
   pesquisa desktop e mobile
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    /* SELETORES — Elementos do DOM */
    const sidebar = document.querySelector('.menuLateral');
    const opcoesMenu = document.querySelectorAll('.menuLateralCaixaOpcaoMenu:not(.menuLateralSubmenuToggle)');
    const botoesSubmenu = document.querySelectorAll('.menuLateralSubmenuToggle');
    const toggleBtn = document.getElementById('openBtnMenuLateral');

    // O seu botão checkbox
    const btnHamburgerMenuLateral = document.getElementById('burger');

    // Seleciona automaticamente a label associada ao ID 'burger'
    const labelHamburguer = document.querySelector(`label[for="burger"]`) || document.querySelector('.menuLateralBurger');

    /* Mantém o nav centralizado fechado, sem deslocá-lo se o submenu couber abaixo. */
    const nav = sidebar.querySelector('.menuLateralNav');
    const itens = nav ? [...nav.children] : [];

    function ajustarPosicaoNav() {
        if (!nav) return;
        const gap = parseFloat(getComputedStyle(nav).rowGap) || 0;
        const alturaItens = itens.reduce((total, item) =>
            total + item.getBoundingClientRect().height, gap * Math.max(0, itens.length - 1));
        const alturaSubmenus = itens.reduce((total, item) => {
            const submenu = item.querySelector(':scope > .menuLateralSubmenu');
            return total + (submenu ? submenu.getBoundingClientRect().height : 0);
        }, 0);
        const centroFechado = Math.max(0, (nav.clientHeight - (alturaItens - alturaSubmenus)) / 2);
        const espacoParaExpandir = Math.max(0, nav.clientHeight - alturaItens);
        const deslocamento = `${Math.min(centroFechado, espacoParaExpandir)}px`;
        if (nav.style.paddingTop !== deslocamento) nav.style.paddingTop = deslocamento;
    }

    if (nav) {
        nav.classList.add('menuLateralNavDinamica');
        ajustarPosicaoNav();
        const observador = new ResizeObserver(ajustarPosicaoNav);
        observador.observe(nav);
        itens.forEach(item => observador.observe(item));
        window.addEventListener('resize', ajustarPosicaoNav);
    }

    /* NAVEGAÇÃO — Destaque do item ativo */
    function ativarOpcaoMenu(opcaoClicada) {
        opcoesMenu.forEach(function (opcao) {
            opcao.classList.remove('active');
        });
        botoesSubmenu.forEach(botao => botao.classList.remove('active'));
        opcaoClicada.classList.add('active');
    }

    document.querySelectorAll('.menuLateralSubmenuItem[type="button"]').forEach(function (opcao) {
        opcao.addEventListener('click', function () {
            ativarOpcaoMenu(this);
        });
    });

    /* Um submenu por vez; a escolha permanece durante a navegação nesta aba. */
    const chaveSubmenu = 'menuLateralSubmenuAberto';

    botoesSubmenu.forEach(function (botao) {
        const submenu = document.getElementById(botao.getAttribute('aria-controls'));
        submenu.addEventListener('transitionend', ajustarPosicaoNav);
        botao.addEventListener('click', function () {
            const menuFechado = sidebar.classList.contains('fechado');
            const vaiAbrir = menuFechado || this.getAttribute('aria-expanded') !== 'true';
            if (menuFechado) toggleSidebar();
            sessionStorage.setItem(chaveSubmenu, vaiAbrir ? submenu.id : '');
            sincronizarSubmenu(sidebar.classList.contains('fechado'));
            ajustarPosicaoNav();
        });
    });

    /* TOGGLE — Expandir / Recolher menu (desktop) */
    function sincronizarSubmenu(estaFechado) {
        const idAberto = sessionStorage.getItem(chaveSubmenu);
        botoesSubmenu.forEach(function (botao) {
            const submenu = document.getElementById(botao.getAttribute('aria-controls'));
            const filhoAtivo = !!submenu.querySelector('.menuLateralSubmenuItem.active');
            // Mantém no máximo um submenu aberto, mesmo quando a página atual está em outro grupo.
            const aberto = !estaFechado && submenu.id === idAberto;
            botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
            submenu.inert = !aberto;
            botao.classList.toggle('active', estaFechado && filhoAtivo);
        });
    }

    function toggleSidebar() {
        sidebar.classList.toggle('fechado');
        var estaFechado = sidebar.classList.contains('fechado');
        sincronizarSubmenu(estaFechado);
        ajustarPosicaoNav();
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
        const salvo = sessionStorage.getItem(chaveSubmenu);
        if (salvo === null || (salvo !== '' && ![...botoesSubmenu].some(botao => botao.getAttribute('aria-controls') === salvo))) {
            const ativo = [...botoesSubmenu].find(botao => botao.getAttribute('aria-expanded') === 'true');
            sessionStorage.setItem(chaveSubmenu, ativo?.getAttribute('aria-controls') || '');
        }
        sincronizarSubmenu(sidebar.classList.contains('fechado'));
        ajustarPosicaoNav();
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    restaurarEstadoMenu();
    const finalizarRestauracao = () => document.documentElement.classList.remove('menuLateralInicializando');
    requestAnimationFrame(() => requestAnimationFrame(finalizarRestauracao));
    // Abas em segundo plano podem suspender requestAnimationFrame.
    setTimeout(finalizarRestauracao, 200);

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

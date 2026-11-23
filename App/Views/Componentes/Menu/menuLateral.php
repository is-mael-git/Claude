<?php
require_once BASE_MODAL;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ParkTechCG - Sistema de Gestão do Parque Tecnológico de Campo Grande">
    <title>ParkTechCG — Área de Trabalho</title>
    <script>
        (function () {
            try {
                if (localStorage.getItem('menuFechado') === 'true') {
                    document.documentElement.classList.add('menuLateralEstadoFechado');
                }
            } catch (erro) { }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/ParkTec/Public/Css/style.css">
    <link rel="icon" href="/ParkTec/Public/Assets/ImgSistema/logo1.jpg" type="image/png">
    <script src="/ParkTec/Public/Js/Componentes/menu.js" defer></script>
    <script src="/ParkTec/Public/Js/Componentes/modal.js" defer></script>
</head>

<body class="menuLateralFundo">
    <!-- <div class="menuLateralOverlayMenu" id="overlay_menu"></div>  AINDA NÃO SABEMOS O QUE FAZER COM ELE -->

    <?php require_once __DIR__ . '/Secoes/asideMenuLateral.php' ?>

    <?php require_once __DIR__ . '/Secoes/headerMenuLateral.php' ?>

    <!-- <div class="menuLateralAreaTrabalho"></div> -->

    <?php require_once __DIR__ . '/Secoes/modalNotificacaoMenuLateral.php' ?>

    <?php require_once __DIR__ . '/Secoes/modalPerfilMenuLateral.php' ?>

</body>

</html>

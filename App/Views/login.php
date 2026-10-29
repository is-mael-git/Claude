<?php
use Core\auth;
require_once BASE_MODAL;
require_once BASE_COMPONENTES . "adicionarMentor.php";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/Public/Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="<?= BASE_JS ?>/login.js" defer></script>
    <script src= "<?=  BASE_JS ?>Componentes/modal.js"></script>

    <title>ParkTec</title>
</head>

<body class="containerLogin">
    <header class="areaStyleLogin">

        <div class="areaFotoLogin">
            <div class="logoLogin" >
                <img src="<?=  BASE_IMGSIS ?>logoAutaQuali.svg" alt="Logo ParkTecCG">
            </div>
        </div>

        <div class="areaDesignLogin">
            <div class="trianguloLogin"></div>
        </div>

    </header>

    <main class="areaCadastroLogin">
        <div class="logoLogin2">
            <img src="<?=  BASE_IMGSIS ?>/logo-footer-sem-texto.png" alt="Logo ParkTecCG">
        </div>
        <form action="<?=  BASE_URL ?>login" method="POST" class="formLogin">
            <!-- Gerador de token -->
            <input type="hidden" name="csrfToken" value="<?= htmlspecialchars(auth::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="logoLogin3">
                <img src="<?=  BASE_IMGSIS ?>sgr.svg" alt="Logo SGR, sistema de gerenciamento de residêntes">
            </div>
            <div class="inputsLogin">
                <div class="inputLogin">
                    <!-- Esses dois labels em cima dos dois inputs são para recursos de maior acessibilidade. Eles estão configurados para não aparecer, mas ao usar o recurso de escuta ficará mais acessível para mais usuários. -->
                    <label class="srOnly" for="emailLogin">Email</label>
                    <input type="email" name="email" id="emailLogin" placeholder="Email">
                    <i class="fa-solid fa-user userIconLogin"></i>
                </div>
                <div class="inputLogin">
                    <label class="srOnly" for="senhaLogin">Senha</label>
                    <input class="inputLogin" type="password" name="senha" id="senhaLogin" placeholder="Senha">
                    <i class="bi bi-eye-slash-fill eyeIconLogin" id="btnSenha" onclick="mostrarSenha()"></i>
                </div>
            </div>
            <div class="acoesLogin">
                <div class="acoesBoxLogin">
                    <div class="boxCheckLogin">
                        <input type="checkbox" id="checkbox1Login" name="lembrarAcesso" value="1" checked/>
                        <label for="checkbox1Login"></label>
                    </div>          
                    <label for="checkbox1Login">Lembrar-acesso!</label>
                </div>
                <a href="PAGES/menu_lateral.html">Esqueci minha senha</a>
            </div>
            <div class="cadastreSeLogin">
                <a class="abrirModal" type="button" data-modal="modalCadastroMentor">Cadastre-se.</a>
            </div>
            <div class="botaoLogin">
                <button class="btnLogin" type="submit">Acessar</button>
            </div>
        </form>   
    </main>
<script src="<?= BASE_JS ?>crudMentores.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padronização de Botões</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>Public/Css/style.css">
    <script src="<?= BASE_JS ?>Componentes/btnDropDown.js" defer></script>
    <script src="<?= BASE_JS ?>Componentes/btnAtivoInativo.js" defer></script>
    <script src=" <?= BASE_JS?>Bibliotecas/coloris.js "></script>

</head>

<body class="areaBtnPadrao">
    <br>
    <button class="btnSalvar">Salvar</button>
    <br>
    <button class="btnCancelar">Cancelar</button> <br>
    <br>
    <button class="btnDefault">default</button><br>
    <br>
    <button class="adicionarTrl"><i class="fa-solid fa-plus"></i></button> <br>
    <br>
    <div class="select">
        <div
            class="selected"
            data-default="All"
            data-one="option-1"
            data-two="option-2"
            data-three="option-3">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                height="1em"
                viewBox="0 0 512 512"
                class="arrow">
                <path
                    d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"></path>
            </svg>
        </div>
        <div class="options">
            <div title="all">
                <input id="all" name="option" type="radio" checked="" />
                <label class="option" for="all" data-txt="All"></label>
            </div>
            <div title="option-1">
                <input id="option-1" name="option" type="radio" />
                <label class="option" for="option-1" data-txt="option-1"></label>
            </div>
            <div title="option-2">
                <input id="option-2" name="option" type="radio" />
                <label class="option" for="option-2" data-txt="option-2"></label>
            </div>
            <div title="option-3">
                <input id="option-3" name="option" type="radio" />
                <label class="option" for="option-3" data-txt="option-3"></label>
            </div>
        </div>
    </div>
    <br>
    <span class="textoStatus status" id="status">Inativo</span>
    <div class="btnSwitch">
        <label class="switch">
            <input type="checkbox" name="toggle" id="toggle" class="toggle">
            <span class="slider"></span>
        </label>
    </div>
    <span class="textoStatus status" id="status">Inativo</span>
    <div class="btnSwitch">
        <label class="switch">
            <input type="checkbox" id="toggle" class="toggle">
            <span class="slider"></span>
        </label>
    </div>
    <span class="textoStatus status" id="status">Inativo</span>
    <div class="btnSwitch">
        <label class="switch">
            <input type="checkbox" id="toggle" class="toggle">
            <span class="slider"></span>
        </label>
    </div>
    <span class="textoStatus status" id="status">Inativo</span>
    <div class="btnSwitch">
        <label class="switch">
            <input type="checkbox" id="toggle" class="toggle">
            <span class="slider"></span>
        </label>
    </div>
    <span class="textoStatus status" id="status">Inativo</span>
    <div class="btnSwitch">
        <label class="switch">
            <input type="checkbox" id="toggle" class="toggle">
            <span class="slider"></span>
        </label>
    </div>
    <br>
    <label class="container">
        <input checked="checked" type="checkbox">
        <div class="checkmark"></div>
    </label>
    <br>

    <br>
    <button class="btnXClaro"><i class="fa-solid fa-x"></i></button>
    <br>
    <button class="btnXEscuro"><i class="fa-solid fa-x"></i></button>
    <br>
    <button class="btnEditar"><i class="fa-regular fa-pen-to-square"></i>
    </button>
    <input type="text" value="#86AC18" aria-label="Escolher cor" data-texto="Escolher Cor" data-coloris>




</body>

</html>
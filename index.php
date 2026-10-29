<?php

//Retirar quando for fazer alguma entrega

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . "/App/";
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

$config = require __DIR__ . '/Config/aplicativo.php';
define('BASE_URL', $config['base_folder']);
define('BASE_ASSETS', $config['base_assets']);
define('BASE_IMGSIS', $config['base_imgsis']);
define('BASE_UPLOAD', $config['base_upload']);
define('BASE_JS', $config['base_js']);
define('BASE_MODAL', $_SERVER['DOCUMENT_ROOT'] . $config['base_modal']);
define('BASE_MENU', $_SERVER['DOCUMENT_ROOT'] . $config['base_menu']);
define('BASE_COMPONENTES', $_SERVER['DOCUMENT_ROOT'] . $config['base_componentes']);


// provavelmente provisório
$rotaAtual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$rotasPublicas = [
    BASE_URL . 'login'
];

if (!in_array($rotaAtual, $rotasPublicas, true)){
    Core\auth::requerLogin();
}
// Até aqui

$router = new Core\roteador();
require_once __DIR__ . "/App/Rotas/rotas.php";

$router->envio();
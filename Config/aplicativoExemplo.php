<?php
return[
    
    'app_name'          => 'SGRTEC',
    'base_folder'       => '/ParkTec/',
    'base_assets'       => '/ParkTec/Public/Assets/',
    'base_imgsis'       => '/ParkTec/Public/Assets/ImgSistema/',
    'base_js'           => '/ParkTec/Public/Js/',
    'base_upload'      => '/ParkTec/Public/Upload/',
    'base_modal'        => '/ParkTec/App/Views/Componentes/funcaoModal.php',
    'base_menu'         => '/ParkTec/App/Views/Componentes/Menu/menuLateral.php',
    'base_componentes'  => '/ParkTec/App/Views/Componentes/',
    'db' => [
        'host'          => 'localhost',
        'dbname'        => 'sgrtec',
        'usuario'       => 'root',
        'senha'         => ''
    ],
    'jwt' => [
        'secret'        => 'chave-jwt',
        'ttl'           => 1800,
        'cookie_name'   => 'sgrtec',
        'cookie_secure' => false
    ]
];
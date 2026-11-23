<?php

$router->adicionar(
    "POST",
    "/notificacoes/visualizar",
    "controllerNotificacao@visualizar"
);

$router->adicionar(
    "POST",
    "/notificacoes/status",
    "controllerNotificacao@alterarStatus"
);

$router->adicionar(
    "GET",
    "/notificacoes/listar",
    "controllerNotificacao@listar"
);

$router->adicionar(
    "GET",
    "/notificacoes/eventos",
    "controllerNotificacao@eventos"
);

// rota pra resetar o valor do visualizado no banco
$router->adicionar(
    "POST",
    "/notificacoes/resetar",
    "controllerMenuLateral@resetarVisualizacao"
);


//rotas crud parceiros

$router->adicionar("POST", "/parceiros/adicionar", "controllerParceiros@adicionar");
$router->adicionar("POST", "/parceiros/atualizar", "controllerParceiros@atualizar");
$router->adicionar("POST", "/parceiros/status", "controllerParceiros@status");
$router->adicionar("GET", "/parceiros/index", "controllerParceiros@index");
$router->adicionar("GET", "/parceiros/cards", "controllerParceiros@cards");
$router->adicionar("GET", "/parceiros/tabela", "controllerParceiros@tabela");
$router->adicionar("GET", "/parceiros/buscar", "controllerParceiros@buscar");

//rotas crud mentores

$router->adicionar("GET", "/mentores/index", "controllerCrudMentores@index");
$router->adicionar("POST", "/mentores/adicionar", "controllerCrudMentores@adicionar");
$router->adicionar("POST", "/mentores/atualizar", "controllerCrudMentores@atualizar");
$router->adicionar("GET", "/mentores/buscar", "controllerCrudMentores@buscar");
$router->adicionar("GET", "/mentores/tabela", "controllerCrudMentores@listar");
$router->adicionar("POST", "/mentores/status", "controllerCrudMentores@status");


//rotas crud startups

$router->adicionar("GET", "/startups/index", "controllerStartups@index");
$router->adicionar("GET", "/startups/tabela", "controllerStartups@tabela");
$router->adicionar("POST", "/startups/adicionar", "controllerStartups@adicionar");
$router->adicionar("GET", "/startups/buscar", "controllerStartups@buscar");
$router->adicionar("POST", "/startups/validar-campo", "controllerStartups@validarCampo");
$router->adicionar("POST", "/startups/atualizar", "controllerStartups@atualizar");
$router->adicionar("POST", "/startups/status", "controllerStartups@status");


//rotas area trabalho adm

$router->adicionar("GET", "/areaTrabalho/adm", "controllerAreaTrabalhoAdm@index");
$router->adicionar("GET", "/areaTrabalho/adm/quantidadeDash","controllerAreaTrabalhoAdm@quantidadeDash");
$router->adicionar("GET", "/areaTrabalho/adm/teste","controllerAreaTrabalhoAdm@questionarioQtd");
$router->adicionar("GET", "/relato/buscar", "controllerAreaTrabalhoAdm@buscarRelato");
$router->adicionar("POST", "/relato/comentario", "controllerAreaTrabalhoAdm@salvarComentarioRelato");

//rota area de trabalho mentor

$router->adicionar("GET", "/areaTrabalhoMentor/index", "controllerAreaTrabalhoMentor@index");
$router->adicionar("GET", "/areaTrabalhoMentor/tabela", "controllerAreaTrabalhoMentor@tabela");

//rota equipe + novo perfil

$router->adicionar("GET", "/equipe/index", "controllerEquipe@index");
$router->adicionar("GET", "/equipe/tabela", "controllerEquipe@tabela");
$router->adicionar("GET", "/equipe/buscar", "controllerEquipe@buscar");
$router->adicionar("GET", "/equipe/cpf-existe", "controllerEquipe@cpfExiste");
$router->adicionar("POST", "/equipe/cadastrar", "controllerEquipe@cadastrar");
$router->adicionar("POST", "/equipe/perfil/cadastrar", "controllerEquipe@cadastrarPerfil");
$router->adicionar("POST", "/equipe/atualizar", "controllerEquipe@atualizar");
$router->adicionar("POST", "/equipe/status", "controllerEquipe@status");


//teste

$router->adicionar("GET", "/componentes/botao", "controllerTesteComponentes@botao");

// ------------------ Login ------------------
$router->adicionar('GET', '/', 'controllerAuth@loginForm');
$router->adicionar('GET', '/login', 'controllerAuth@loginForm');
$router->adicionar('POST', '/login', 'controllerAuth@login');
$router->adicionar('POST', '/logout', 'controllerAuth@logout');

//rotas Questionario
$router->adicionar("GET", "/questionario/index", "controllerQuestionario@index");
$router->adicionar("GET", "/questionario/tabela", "controllerQuestionario@tabela");
$router->adicionar("POST", "/questionario/adicionar", "controllerQuestionario@cadastrar");
$router->adicionar("GET", "/questionario/buscar", "controllerQuestionario@buscar");
$router->adicionar("GET", "/questionario/fazer", "controllerQuestionario@fazer");
$router->adicionar("POST", "/questionario/atualizar", "controllerQuestionario@atualizar");
$router->adicionar("POST", "/questionario/status", "controllerQuestionario@alterarStatus");

//rotas Questao
$router->adicionar("GET", "/questao/index", "controllerQuestao@index");
$router->adicionar("GET", "/questao/listar", "controllerQuestao@listar");
$router->adicionar("GET", "/questao/tabela", "controllerQuestao@listar");
$router->adicionar("POST", "/questao/adicionar", "controllerQuestao@cadastrar");
$router->adicionar("GET", "/questao/buscar", "controllerQuestao@buscar");
$router->adicionar("POST", "/questao/atualizar", "controllerQuestao@atualizar");
$router->adicionar("POST", "/questao/status", "controllerQuestao@alterarStatus");

//rota match mentor e startup

$router->adicionar("GET", "/areaTrabalho/adm/mentoria", "controllerAreaTrabalhoAdm@buscarMentoria");
$router->adicionar("POST", "/areaTrabalho/adm/mentoria/aprovar", "controllerAreaTrabalhoAdm@aprovarMentoria");

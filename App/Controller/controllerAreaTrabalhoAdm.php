<?php

namespace Controller;

use Core\auth;
use Core\controller;
use Models\questionario;
use Models\areaTrabalhoAdm;
use Models\relato;
use Models\mentoria;

class controllerAreaTrabalhoAdm extends controller
{
    public function index(): void
    {
        $this->mostrarTela("/areaTrabalhoAdm", [
            'usuario' => auth::usuario(),
            'sucesso' => auth::pegarFlash('sucesso'),
            'idMentoriaDemo' => mentoria::buscarUltimoId()
        ]);
    }

    public function buscarRelato()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Relato inválido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $respostas = relato::buscarPorRespostaId($id);

            if ($respostas === []) {
                http_response_code(404);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Relato não encontrado.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            echo json_encode([
                'sucesso' => true,
                'relatos' => $respostas
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (\Throwable $erro) {
            error_log($erro->getMessage());
            http_response_code(500);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível carregar o relato.'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

    public function salvarComentarioRelato()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $dados = json_decode(
                file_get_contents('php://input'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $erro) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados do comentário inválidos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $comentarios = $dados['comentarios'] ?? null;
        $ids = [];

        if (!is_array($comentarios) || $comentarios === []) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Informe os relatos e comentários válidos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $comentariosValidados = [];

        foreach ($comentarios as $item) {
            if (!is_array($item)) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Informe relatos válidos e comentários de até 500 caracteres.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $id = $item['id'] ?? null;
            $comentario = $item['comentario'] ?? null;
            $quantidadeCaracteres = is_string($comentario)
                ? preg_match_all('/./us', $comentario)
                : false;

            if (
                (!is_int($id) && !(is_string($id) && ctype_digit($id))) ||
                (int) $id <= 0 ||
                isset($ids[(int) $id]) ||
                $quantidadeCaracteres === false ||
                $quantidadeCaracteres > 500
            ) {
                http_response_code(400);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Informe relatos válidos e comentários de até 500 caracteres.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $ids[(int) $id] = true;
            $comentariosValidados[] = [
                'id' => (int) $id,
                'comentario' => trim($comentario)
            ];
        }

        try {
            if (!relato::salvarComentarios($comentariosValidados)) {
                http_response_code(404);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Um ou mais relatos não foram encontrados.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Comentários salvos com sucesso.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $erro) {
            error_log($erro->getMessage());
            http_response_code(500);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível salvar o comentário.'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

     public function quantidadeDash()
    {

        $questionarioQtd = questionario::qtdAtivos();

        $startupQtd = areaTrabalhoAdm::listar();

        $mentoresQtd = areaTrabalhoAdm::listarMentores();

        $dadosFiltrado = [

            "quantidadeQuestionario" => $questionarioQtd["quantidade_questionario"],

            "quantidadeStartups" => $startupQtd,

            "quantidadeMentores" => $mentoresQtd
        ];

        header('Content-Type: application/json');

        http_response_code(200);

        echo json_encode($dadosFiltrado);

        exit;
    }
    public function buscarMentoria(): void{
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID da mentoria inválido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $dados = mentoria::buscarDados($id);

        if ($dados === null) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Mentoria não encontrada.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'sucesso' => true,
            'mentoria' => $dados
        ], JSON_UNESCAPED_UNICODE);

        exit;

    }

    public function aprovarMentoria(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $dados = json_decode(
                file_get_contents('php://input'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $erro) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados da mentoria inválidos.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = filter_var($dados['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID da mentoria inválido.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            if (!mentoria::aprovar($id)) {
                http_response_code(404);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Mentoria não encontrada.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Mentoria aprovada.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $erro) {
            error_log($erro->getMessage());
            http_response_code(500);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível aprovar a mentoria.'
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }

}

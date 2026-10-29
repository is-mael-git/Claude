<?php

namespace Controller;

use Core\controller;
use Models\questao;
use Throwable;

class controllerQuestao extends controller
{
    public function index()
    {
        $eixos = questao::listarEixos();
        $competencias = questao::listarCompetencias();

        $this->mostrarTela("/questao", [
            'eixos' => $eixos,
            'competencias' => $competencias
        ]);
    }


    public function listar()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $questoes = questao::listar(($_GET['somenteAtivas'] ?? '') === '1');

            http_response_code(200);

            echo json_encode(
                $questoes,
                JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível buscar as questões.',
                'erro' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    public function cadastrar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $pergunta = trim(
            $_POST['pergunta'] ?? ''
        );

        $idEixo = (int) (
            $_POST['id_eixo'] ?? 0
        );


        if ($pergunta === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Digite a pergunta.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if (mb_strlen($pergunta) > 255) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'A pergunta pode possuir no máximo 255 caracteres.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if ($idEixo <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Selecione um eixo válido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        try {

            $idQuestao = questao::cadastrar(
                $pergunta,
                $idEixo
            );


            http_response_code(201);

            echo json_encode([
                'sucesso' => true,
                'id' => $idQuestao,
                'mensagem' => 'Questão cadastrada com sucesso.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível cadastrar a questão.',
                'erro' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    public function buscar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) (
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questão inválida.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        try {

            $questaoEncontrada =
                questao::buscarPorId($id);


            if (!$questaoEncontrada) {

                http_response_code(404);

                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Questão não encontrada.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            http_response_code(200);

            echo json_encode(
                $questaoEncontrada,
                JSON_UNESCAPED_UNICODE
            );

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível buscar a questão.',
                'erro' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    public function atualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) (
            $_POST['id'] ?? 0
        );

        $pergunta = trim(
            $_POST['pergunta'] ?? ''
        );

        $idEixo = (int) (
            $_POST['id_eixo'] ?? 0
        );


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questão inválida.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if ($pergunta === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Digite a pergunta.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if (mb_strlen($pergunta) > 255) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'A pergunta pode possuir no máximo 255 caracteres.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if ($idEixo <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Selecione um eixo válido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        try {

            $questaoEncontrada =
                questao::buscarPorId($id);


            if (!$questaoEncontrada) {

                http_response_code(404);

                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Questão não encontrada.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }


            questao::atualizar(
                $id,
                $pergunta,
                $idEixo
            );


            http_response_code(200);

            echo json_encode([
                'sucesso' => true,
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível atualizar a questão.',
                'erro' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }


    public function alterarStatus()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) (
            $_POST['id'] ?? 0
        );

        $ativo = (int) (
            $_POST['ativo'] ?? -1
        );


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questão inválida.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        if (!in_array($ativo, [0, 1], true)) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Status inválido.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        try {

            $questaoEncontrada =
                questao::buscarPorId($id);


            if (!$questaoEncontrada) {

                http_response_code(404);

                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Questão não encontrada.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            if ($ativo === 0) {
                questao::inativarERemoverDeQuestionarios($id);
            } else {
                questao::alterarStatus($id, $ativo);
            }


            http_response_code(200);

            echo json_encode([
                'sucesso' => true,
                'ativo' => $ativo,
                'mensagem' =>
                    $ativo === 1
                        ? 'Questão ativada com sucesso.'
                        : 'Questão inativada e removida dos questionários.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível alterar o status da questão.',
                'erro' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }

        exit;
    }
}

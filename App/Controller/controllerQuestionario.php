<?php

namespace Controller;

use Core\controller;
use Models\questionario;
use Models\questao;
use Throwable;

class controllerQuestionario extends controller
{
    public function index()
    {
        $eixos = questao::listarEixos();
        $competencias = questao::listarCompetencias();

        $this->mostrarTela("/questionario", [
            'eixos' => $eixos,
            'competencias' => $competencias
        ]);
    }


    public function tabela()
    {
        $questionarios = questionario::listar();

        header('Content-Type: application/json');
        http_response_code(200);

        echo json_encode($questionarios);

        exit;
    }

    public function cadastrar()
    {
        header('Content-Type: application/json');

        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $questoes = $_POST['questoes'] ?? [];


        if ($nome === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Digite o nome do questionário.'
            ]);

            exit;
        }


        if (
            !is_array($questoes) ||
            count($questoes) === 0
        ) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Selecione pelo menos uma questão.'
            ]);

            exit;
        }


        if (count($questoes) > 50) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'O questionário pode possuir no máximo 50 questões.'
            ]);

            exit;
        }


        $questoes = array_values(
            array_unique(
                array_map(
                    'intval',
                    $questoes
                )
            )
        );


        try {

            $idQuestionario =
                questionario::cadastrar(
                    $nome,
                    $descricao,
                    $questoes
                );


            http_response_code(201);

            echo json_encode([
                'sucesso' => true,
                'id' => $idQuestionario,
                'mensagem' => 'Questionário cadastrado com sucesso.'
            ]);

        } catch (\InvalidArgumentException $erro) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível cadastrar o questionário.'
            ]);
        }

        exit;
    }


    public function buscar()
    {
        header('Content-Type: application/json');

        $id = (int) ($_GET['id'] ?? 0);


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário inválido.'
            ]);

            exit;
        }


        $questionario =
            questionario::buscarPorId($id);


        if (!$questionario) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário não encontrado.'
            ]);

            exit;
        }


        echo json_encode($questionario);

        exit;
    }


    public function fazer()
    {
        header('Content-Type: application/json');

        $id = (int) ($_GET['id'] ?? 0);


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário inválido.'
            ]);

            exit;
        }


        $questionario =
            questionario::buscarParaResponder($id);


        if (!$questionario) {

            http_response_code(404);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário não encontrado.'
            ]);

            exit;
        }


        echo json_encode(
            $questionario,
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }


    public function atualizar()
    {
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);

        $nome =
            trim(
                $_POST['nome'] ?? ''
            );

        $descricao =
            trim(
                $_POST['descricao'] ?? ''
            );

        $questoes =
            $_POST['questoes'] ?? [];


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário inválido.'
            ]);

            exit;
        }


        if ($nome === '') {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Digite o nome do questionário.'
            ]);

            exit;
        }


        if (
            !is_array($questoes) ||
            count($questoes) === 0
        ) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Selecione pelo menos uma questão.'
            ]);

            exit;
        }


        if (count($questoes) > 50) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'O questionário pode possuir no máximo 50 questões.'
            ]);

            exit;
        }


        $questoes = array_values(
            array_unique(
                array_map(
                    'intval',
                    $questoes
                )
            )
        );


        try {

            questionario::atualizar(
                $id,
                $nome,
                $descricao,
                $questoes
            );


            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Questionário atualizado com sucesso.'
            ]);

        } catch (\InvalidArgumentException $erro) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => $erro->getMessage()
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível atualizar o questionário.'
            ]);
        }

        exit;
    }


    public function alterarStatus()
    {
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        $ativo = (int) ($_POST['ativo'] ?? -1);


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Questionário inválido.'
            ]);

            exit;
        }


        if (!in_array($ativo, [0, 1], true)) {

            http_response_code(400);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Status inválido.'
            ]);

            exit;
        }


        try {

            questionario::alterarStatus(
                $id,
                $ativo
            );


            echo json_encode([
                'sucesso' => true,
                'ativo' => $ativo,
                'mensagem' =>
                    $ativo === 1
                        ? 'Questionário ativado com sucesso.'
                        : 'Questionário inativado com sucesso.'
            ]);

        } catch (Throwable $erro) {

            http_response_code(500);

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível alterar o status do questionário.'
            ]);
        }

        exit;
    }

}

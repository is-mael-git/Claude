<?php

namespace Controller;

use Core\controller;
use Models\usuarioParceiros;
use Core\validador;

class controllerParceiros extends controller
{
    public function adicionar()
    {
        $imagem = $this->subirFoto(true);

        $dados = $this->dadosFormulario($imagem);

        usuarioParceiros::cadastrarParceiro($dados);
        $_SESSION['parceiroSalvo'] = true;
        $this->redirecionar("/parceiros/index");
        return;
    }
    public function atualizar()
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->redirecionar("/parceiros/index");
            return;
        }

        $imagem = $this->subirFoto() ?? (usuarioParceiros::buscarPorId($id)['foto'] ?? '');

        $dados = $this->dadosFormulario($imagem);

        usuarioParceiros::atualizarParceiro($dados, $id);
        $_SESSION['parceiroSalvo'] = true;
        $this->redirecionar("/parceiros/index");
        return;
    }

    public function buscar()
    {
        $id = (int) ($_GET['id'] ?? 0);

        header('Content-Type: application/json; charset=utf-8');

        if ($id <= 0) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            exit;
        }

        $parceiro = usuarioParceiros::buscarPorId($id);

        if (!$parceiro) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'erro' => 'Parceiro não encontrado.']);
            exit;
        }

        echo json_encode(['sucesso' => true, 'parceiro' => $parceiro]);
        exit;
    }

    public function status()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            exit;
        }

        $ativo = ($_POST['ativo'] ?? '') === '1';

        $sucesso = usuarioParceiros::alterarStatus($id, $ativo);

        echo json_encode(['sucesso' => $sucesso, 'ativo' => $ativo]);
        exit;
    }

    public function index()
    {
        // o aviso de salvo atravessa o redirecionamento pela sessão, e vale uma vez só
        $salvo = !empty($_SESSION['parceiroSalvo']);

        unset($_SESSION['parceiroSalvo']);

        $this->mostrarTela("crudParceiro", [
            'areas' => usuarioParceiros::listarAreas(),
            'salvo' => $salvo
        ]);
    }

    public function cards(){

        $cards = usuarioParceiros::listar();

        $this->mostrarTela("cardsParceiros", ['cards' => $cards]);
    }

    public function tabela()
    {
        $parceiro = usuarioParceiros::listar();
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($parceiro);
        exit;
    }

    private function dadosFormulario(?string $imagem): array
    {   
        if(!validador::validarCnpj($_POST['cnpj'])){
            die("CNPJ Parceiro Invalido");
        }
        if(!validador::validarTel($_POST['telefone'])){
            die("Telefone Parceiro Invalido");
        }
        if(!validador::validarTel($_POST['telefoneRepresentante'])){
            die("Telefone Representante Invalido");
        }
        return [
            'foto'                      => $imagem ?? "",
            'nome'                      => $this->antiXss($_POST['nome'] ?? ""),
            'cnpj'                      => $this->antiXss($_POST['cnpj'] ?? ""),
            'telefone'                  => $this->antiXss($_POST['telefone'] ?? ""),
            'instagram'                 => $this->antiXss($_POST['instagram'] ?? ""),
            'id_area_atuacao'           => $this->idAreaAtuacao(),
            'site'                      => $this->antiXss($_POST['site'] ?? ""),
            'linkedin'                  => $this->antiXss($_POST['linkedin'] ?? ""),
            'nome_representante'        => $this->antiXss($_POST['nomeRepresentante'] ?? ""),
            'telefone_representante'    => $this->antiXss($_POST['telefoneRepresentante'] ?? ""),
            'cargo_representante'       => $this->antiXss($_POST['cargoRepresentante'] ?? ""),
            'email_representante'       => $this->antiXss($_POST['emailRepresentante'] ?? "")
        ];
    }

    private function antiXss(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    
    private function idAreaAtuacao(): int
    {
        $novaArea = trim($_POST['novaAreaAtuacao'] ?? '');

        if ($novaArea !== '') {
            return usuarioParceiros::cadastrarArea($this->antiXss($novaArea));
        }

        $id = (int) ($_POST['idAreaAtuacao'] ?? 0);

        if ($id <= 0) {
            die("Area de Atuacao Obrigatoria");
        }

        return $id;
    }

    private function subirFoto(bool $obrigatoria = false): ?string
    {
        $arquivo = $_FILES['foto'] ?? null;

        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($obrigatoria) {
                $this->erroUploadFoto('Anexe uma imagem JPEG, PNG ou WEBP.');
            }

            return null;
        }

        $caminhoTemporario = $arquivo['tmp_name'] ?? '';
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($caminhoTemporario)) {
            $this->erroUploadFoto('Não foi possível receber a imagem. Tente novamente.');
        }

        $tiposPermitidos = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/webp' => ['webp'],
        ];
        $detectorMime = new \finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $detectorMime->file($caminhoTemporario);
        $extensao = strtolower(pathinfo(basename((string) ($arquivo['name'] ?? '')), PATHINFO_EXTENSION));
        $dadosImagem = @getimagesize($caminhoTemporario);

        if (
            !is_string($tipoMime)
            || !isset($tiposPermitidos[$tipoMime])
            || !in_array($extensao, $tiposPermitidos[$tipoMime], true)
            || $dadosImagem === false
            || ($dadosImagem['mime'] ?? null) !== $tipoMime
        ) {
            $this->erroUploadFoto('O arquivo deve ser uma imagem JPEG, JPG, PNG ou WEBP.');
        }

        $imagem = bin2hex(random_bytes(16)) . '.' . $extensao;
        $destino = $_SERVER['DOCUMENT_ROOT'] . BASE_UPLOAD . 'Parceiros/' . $imagem;

        if (!move_uploaded_file($caminhoTemporario, $destino)) {
            http_response_code(500);
            exit('Falha ao salvar a imagem.');
        }

        return $imagem;
    }

    private function erroUploadFoto(string $mensagem): void
    {
        http_response_code(422);
        exit($mensagem);
    }
}

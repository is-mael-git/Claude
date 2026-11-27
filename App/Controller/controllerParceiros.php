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
        $colunasPermitidas = ['id', 'nome', 'area_nome', 'telefone', 'ativo'];
        $orderBy = $this->ordenacaoTabela($colunasPermitidas, 'id', 'ASC');
        $area = $_GET['area'] ?? null;

        $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : null;
        $offset = $limit !== null && isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : null;

        $parceiros = usuarioParceiros::listar($orderBy, $area, $limit, $offset);
        $total = usuarioParceiros::contar($area);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'dados' => $parceiros,
            'total' => $total,
        ]);
        exit;
    }

    private function dadosFormulario(?string $imagem): array
    {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $cnpj = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) ($_POST['cnpj'] ?? '')));
        $telefone = preg_replace('/\D/', '', (string) ($_POST['telefone'] ?? ''));
        $site = trim((string) ($_POST['site'] ?? ''));
        $instagram = trim((string) ($_POST['instagram'] ?? ''));
        $linkedin = trim((string) ($_POST['linkedin'] ?? ''));
        $nomeRepresentante = trim((string) ($_POST['nomeRepresentante'] ?? ''));
        $telefoneRepresentante = preg_replace('/\D/', '', (string) ($_POST['telefoneRepresentante'] ?? ''));
        $cargoRepresentante = trim((string) ($_POST['cargoRepresentante'] ?? ''));
        $emailRepresentante = trim((string) ($_POST['emailRepresentante'] ?? ''));

        if (!validador::required($nome) || mb_strlen($nome) > 100) {
            $this->erroValidacao('Nome do parceiro é obrigatório e deve ter até 100 caracteres.');
        }
        if (!validador::validarCnpj($cnpj)) {
            $this->erroValidacao('CNPJ do parceiro inválido.');
        }
        if (!validador::validarTel($telefone) || strlen($telefone) > 11) {
            $this->erroValidacao('Telefone do parceiro inválido.');
        }
        foreach (['Site' => $site, 'Instagram' => $instagram, 'LinkedIn' => $linkedin] as $rotulo => $url) {
            if ($url !== '' && (mb_strlen($url) > 255 || !preg_match('/^https?:\/\//i', $url))) {
                $this->erroValidacao("{$rotulo} deve começar com http:// ou https:// e ter até 255 caracteres.");
            }
        }
        if (!validador::required($nomeRepresentante) || mb_strlen($nomeRepresentante) > 100) {
            $this->erroValidacao('Nome do representante é obrigatório e deve ter até 100 caracteres.');
        }
        if (!validador::validarTel($telefoneRepresentante) || strlen($telefoneRepresentante) > 11) {
            $this->erroValidacao('Telefone do representante inválido.');
        }
        if (!validador::required($cargoRepresentante) || mb_strlen($cargoRepresentante) > 50) {
            $this->erroValidacao('Cargo do representante é obrigatório e deve ter até 50 caracteres.');
        }
        if (!validador::required($emailRepresentante) || !validador::email($emailRepresentante) || mb_strlen($emailRepresentante) > 320) {
            $this->erroValidacao('E-mail do representante inválido.');
        }

        return [
            'foto'                      => $imagem ?? '',
            'nome'                      => $this->antiXss($nome),
            'cnpj'                      => $cnpj,
            'telefone'                  => $telefone,
            'instagram'                 => $this->antiXss($instagram),
            'id_area_atuacao'           => $this->idAreaAtuacao(),
            'site'                      => $this->antiXss($site),
            'linkedin'                  => $this->antiXss($linkedin),
            'nome_representante'        => $this->antiXss($nomeRepresentante),
            'telefone_representante'    => $telefoneRepresentante,
            'cargo_representante'       => $this->antiXss($cargoRepresentante),
            'email_representante'       => $this->antiXss($emailRepresentante)
        ];
    }

    private function antiXss(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    private function idAreaAtuacao(): int
    {
        $novaArea = trim((string) ($_POST['novaAreaAtuacao'] ?? ''));

        if ($novaArea !== '') {
            if (mb_strlen($novaArea) > 50) {
                $this->erroValidacao('O nome da nova área de atuação deve ter até 50 caracteres.');
            }
            return usuarioParceiros::cadastrarArea($this->antiXss($novaArea));
        }

        $id = (int) ($_POST['idAreaAtuacao'] ?? 0);

        if ($id <= 0) {
            $this->erroValidacao('Área de atuação obrigatória.');
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

    private function erroValidacao(string $mensagem): never
    {
        http_response_code(422);
        exit($mensagem);
    }

    private function erroUploadFoto(string $mensagem): never
    {
        $this->erroValidacao($mensagem);
    }
}

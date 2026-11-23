<?php

namespace Controller;

use Core\controller;
use Models\usuarioMentor;
use Models\usuarioParceiros;
use Exception;
use PDOException;

class controllerCrudMentores extends controller
{
    private const MODALIDADES = [
        'online'     => 'REMOTO',
        'hibrido'    => 'HIBRIDO',
        'presencial' => 'PRESENCIAL',
    ];

    private const DIAS    = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
    private const TURNOS  = ['matutino', 'vespertino', 'noturno'];

    
    private const COMPETENCIAS = [
        'produto&tecnologia'    => 3,
        'operacoes'             => 3,
        'capital&investimentos' => 5,
        'financas'              => 5,
        'estrategia'            => 1,
        'gestao'                => 4,
        'networking&conexoes'   => 2,
        'marketing&vendas'      => 2,
        'mercados&clientes'     => 2,
        'juridico&pi'           => 4,
    ];

    private const TIPOS_IMAGEM = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const TAMANHO_MAX = 2 * 1024 * 1024; // 2 MB

    public function index()
    {
        $salvo = !empty($_SESSION['mentorSalvo']);
        $erro = $_SESSION['mentorErro'] ?? null;
        unset($_SESSION['mentorSalvo'], $_SESSION['mentorErro']);

        $this->mostrarTela("crudMentores", [
            'parceiros' => usuarioParceiros::listar(),
            'salvo' => $salvo,
            'erro' => $erro,
        ]);
    }

    public function adicionar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "mentores/index");
            exit();
        }

        try {
            $dados = $this->prepararDados();

            $dados['foto']   = $this->subirImagem('mentorFoto', 'Mentor/', true);
            $dados['banner'] = $this->subirImagem('banner', 'Mentores/Banners/', false);

            usuarioMentor::cadastrarMentor($dados);
            $_SESSION['mentorSalvo'] = true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $msg = $e->getCode() === '23000'
                ? 'E-mail, telefone, Linkedin, Instagram ou foto já cadastrados.'
                : 'Erro ao salvar o mentor.';
            $_SESSION['mentorErro'] = $msg;
        } catch (Exception $e) {
            $_SESSION['mentorErro'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "mentores/index");
        exit();
    }

    public function atualizar()
    {
        $idMentor = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$idMentor || $idMentor <= 0) {
            $_SESSION['mentorErro'] = 'Mentor não encontrado.';
            header("Location: " . BASE_URL . "mentores/index");
            exit();
        }

        try {
            $mentorAtual = usuarioMentor::buscarPorId((int) $idMentor);
            if (!$mentorAtual) {
                throw new Exception('Mentor não encontrado.');
            }

            $dados = $this->prepararDados();
            $dados['foto'] = $this->subirImagem('mentorFoto', 'Mentor/', false)
                ?? $mentorAtual['foto'];
            $dados['banner'] = $this->subirImagem('banner', 'Mentores/Banners/', false)
                ?? $mentorAtual['banner'];

            usuarioMentor::atualizarMentor($dados, (int) $idMentor);
            $_SESSION['mentorSalvo'] = true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['mentorErro'] = $e->getCode() === '23000'
                ? 'E-mail, telefone, Linkedin ou Instagram já cadastrados.'
                : 'Erro ao atualizar o mentor.';
        } catch (Exception $e) {
            $_SESSION['mentorErro'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "mentores/index");
        exit();
    }

    public function buscar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            exit;
        }

        $mentor = usuarioMentor::buscarPorId((int) $id);
        if (!$mentor) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'erro' => 'Mentor não encontrado.']);
            exit;
        }

        echo json_encode(['sucesso' => true, 'mentor' => $mentor]);
        exit;
    }

    private function prepararDados(): array
    {
        $nome      = trim($_POST['nome'] ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $telefone  = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
        $biografia = trim($_POST['biografia'] ?? '');

        if ($nome === '' || $email === '' || $telefone === '' || $biografia === '') {
            throw new Exception('Preencha todos os campos obrigatórios.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('E-mail inválido.');
        }
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            throw new Exception('Telefone inválido.');
        }
        if (mb_strlen($biografia) > 500) {
            throw new Exception('A biografia deve ter no máximo 500 caracteres.');
        }

        $idParceiro = filter_var($_POST['id_parceiro'] ?? null, FILTER_VALIDATE_INT);
        if (!$idParceiro) {
            throw new Exception('Selecione a empresa.');
        }

        $semana  = array_values(array_intersect((array) ($_POST['semana'] ?? []), self::DIAS));
        $horario = array_values(array_intersect((array) ($_POST['horario'] ?? []), self::TURNOS));
        if (!$semana)  throw new Exception('Selecione pelo menos um dia da semana.');
        if (!$horario) throw new Exception('Selecione pelo menos um horário.');


        $modalidade = self::MODALIDADES[$_POST['modalidade'] ?? ''] ?? null;
        if (!$modalidade) {
            throw new Exception('Selecione uma modalidade.');
        }

        $competencias = [];
        foreach ((array) ($_POST['competencia'] ?? []) as $valor) {
            if (isset(self::COMPETENCIAS[$valor])) {
                $competencias[] = self::COMPETENCIAS[$valor];
            }
        }
        $competencias = array_values(array_unique($competencias));
        if (!$competencias) {
            throw new Exception('Selecione pelo menos uma competência.');
        }

        $instagram = trim($_POST['instagram'] ?? '');
        $linkedin  = trim($_POST['linkedin'] ?? '');

        return [
            'nome'         => $nome,
            'email'        => $email,
            'telefone'     => $telefone,
            'biografia'    => $biografia,
            'linkedin'     => $linkedin !== '' ? $linkedin : null,
            'instagram'    => $instagram !== '' ? $instagram : null,
            'id_parceiro'  => $idParceiro,
            'semana'       => $semana,
            'horario'      => $horario,
            'modalidade'   => $modalidade,
            'competencias' => $competencias,
        ];
    }

    private function subirImagem(string $campo, string $pasta, bool $obrigatoria): ?string
    {
        $arquivo = $_FILES[$campo] ?? null;

        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($obrigatoria) {
                throw new Exception('A foto de perfil é obrigatória.');
            }
            return null;
        }

        $erroUpload = $arquivo['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($erroUpload !== UPLOAD_ERR_OK) {
            $mensagensUpload = [
                UPLOAD_ERR_INI_SIZE   => 'A imagem excede o limite de upload configurado no servidor.',
                UPLOAD_ERR_FORM_SIZE  => 'A imagem excede o limite permitido pelo formulário.',
                UPLOAD_ERR_PARTIAL    => 'O upload da imagem foi interrompido. Tente novamente.',
                UPLOAD_ERR_NO_TMP_DIR => 'O servidor não possui diretório temporário para receber imagens.',
                UPLOAD_ERR_CANT_WRITE => 'O servidor não conseguiu gravar a imagem temporária.',
                UPLOAD_ERR_EXTENSION  => 'O servidor bloqueou o upload da imagem.',
            ];
            throw new Exception($mensagensUpload[$erroUpload] ?? 'Falha no upload da imagem.');
        }
        if ($arquivo['size'] > self::TAMANHO_MAX) {
            throw new Exception('A imagem deve ter no máximo 2 MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        if (!isset(self::TIPOS_IMAGEM[$mime])) {
            throw new Exception('Formato de imagem inválido. Use JPG, PNG ou WEBP.');
        }

        $nomeFinal = bin2hex(random_bytes(16)) . '.' . self::TIPOS_IMAGEM[$mime];
        $diretorio = dirname(__DIR__, 2) . '/Public/Upload/' . trim($pasta, '/') . DIRECTORY_SEPARATOR;

        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            error_log("Não foi possível criar o diretório de upload de mentores: {$diretorio}");
            throw new Exception('Não foi possível criar a pasta de destino das imagens.');
        }
        if (!is_writable($diretorio)) {
            error_log("Sem permissão de escrita no diretório de upload de mentores: {$diretorio}");
            throw new Exception('O servidor não tem permissão para gravar imagens na pasta de destino.');
        }

        $destino = $diretorio . $nomeFinal;
        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            error_log("Falha ao mover imagem de mentor para: {$destino}");
            throw new Exception('Falha ao salvar a imagem.');
        }

        return $nomeFinal;
    }

    public function listar() {
        $mentores = usuarioMentor::listarMentores();
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($mentores);
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

        $sucesso = usuarioMentor::alterarStatus($id, $ativo);

        echo json_encode(['sucesso' => $sucesso, 'ativo' => $ativo]);
        exit;
    }

}
<?php

namespace Controller;

use Core\controller;
use Core\validador;
use DateTimeImmutable;
use DateTimeZone;
use Models\startups;
use PDOException;
use RuntimeException;
use Throwable;

class controllerStartups extends controller
{
    private const BIOGRAFIA_MAX = 3000;
    private const FOTO_MAX_BYTES = 2 * 1024 * 1024;

    public function index()
    {
        $this->mostrarTela('crudStartups');
    }

    public function cardStartup()
    {
        $this->mostrarTela('cardStartup', [
            'cardStartup' => startups::listarCards(),
        ]);
    }

    public function adicionar()
    {
        $logoSalvo = null;

        try {
            if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => ['startup-photo' => 'Arquivo excede o limite do servidor.']], 413);
                return;
            }

            [$dados, $fundadores, $erros, $statusErro] = $this->dadosFormulario(true);
            if ($erros) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $erros], $statusErro);
                return;
            }

            $logoSalvo = $this->subirFoto();
            $dados['logo'] = $logoSalvo;

            $id = startups::cadastrar($dados, $fundadores);
            $this->responderJson(['sucesso' => true, 'id_startup' => $id]);
        } catch (Throwable $e) {
            $this->removerFoto($logoSalvo);
            $this->responderErro($e, 'adicionar');
        }
    }

    public function atualizar()
    {
        $id = (int) ($_POST['id_startup'] ?? 0);
        $novaFoto = null;

        try {
            if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => ['startup-photo' => 'Arquivo excede o limite do servidor.']], 413);
                return;
            }

            if ($id <= 0) {
                $this->responderJson(['sucesso' => false, 'erro' => 'ID inválido.'], 422);
                return;
            }

            $atual = startups::buscarPorId($id);
            if (!$atual) {
                $this->responderJson(['sucesso' => false, 'erro' => 'Startup não encontrada.'], 404);
                return;
            }

            [$dados, $fundadores, $erros, $statusErro] = $this->dadosFormulario(false, $id);
            if ($erros) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $erros], $statusErro);
                return;
            }

            $novaFoto = $this->subirFoto();
            $dados['logo'] = $novaFoto ?? $atual['logo'];

            $ok = startups::atualizar($id, $dados, $fundadores);
            if ($novaFoto && !empty($atual['logo']) && preg_match('/^[a-f0-9]{32}\.(?:jpg|jpeg|png|webp)$/', $atual['logo'])) {
                $this->removerFoto($atual['logo']);
            }

            $this->responderJson(['sucesso' => $ok]);
        } catch (Throwable $e) {
            $this->removerFoto($novaFoto);
            $this->responderErro($e, 'atualizar');
        }
    }

    public function buscar()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->responderJson(['sucesso' => false, 'erro' => 'ID inválido.'], 422);
            return;
        }

        $startup = startups::buscarPorId($id);
        if (!$startup) {
            $this->responderJson(['sucesso' => false, 'erro' => 'Startup não encontrada.'], 404);
            return;
        }

        $this->responderJson(['sucesso' => true, 'startup' => $startup]);
    }

    public function validarCampo()
    {
        $campo = $_POST['campo'] ?? '';
        $valor = trim($_POST['valor'] ?? '');
        $id = max(0, (int) ($_POST['id'] ?? 0));
        $startupId = max(0, (int) ($_POST['id_startup'] ?? 0));

        $mensagens = [
            'startupName'  => 'Nome da startup já cadastrado.',
            'cnpj'         => 'CNPJ já cadastrado.',
            'founderEmail' => 'E-mail já cadastrado.',
        ];

        if (!isset($mensagens[$campo])) {
            $this->responderJson(['valido' => false, 'erro' => 'Campo inválido.'], 422);
            return;
        }

        if ($campo === 'cnpj') {
            $valor = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $valor));
        } elseif ($campo === 'founderEmail') {
            $valor = strtolower($valor);
        }

        if (!validador::required($valor)) {
            $this->responderJson(['valido' => false, 'erro' => 'Campo obrigatório.'], 422);
            return;
        }

        $emUso = startups::campoEmUso($campo, $valor, $id, $startupId);
        $this->responderJson(['valido' => !$emUso, 'erro' => $emUso ? $mensagens[$campo] : null]);
    }

    public function tabela()
    {
        $colunasPermitidas = ['id', 'nome', 'setor_atuacao', 'cnpj', 'cidade', 'ativo'];
        $orderBy = $this->ordenacaoTabela($colunasPermitidas, 'nome', 'ASC');
        $setor = $_GET['setor'] ?? null;

        $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : null;
        $offset = $limit !== null && isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : null;

        $this->responderJson([
            'dados' => startups::listar($orderBy, $setor, $limit, $offset),
            'total' => startups::contar($setor),
        ]);
    }

    public function status()
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0 || !in_array($_POST['ativo'] ?? null, ['0', '1'], true)) {
            $this->responderJson(['sucesso' => false, 'erro' => 'Dados de status inválidos.'], 422);
            return;
        }

        $ativo = $_POST['ativo'] === '1';
        $sucesso = startups::alterarStatus($id, $ativo);
        $this->responderJson(['sucesso' => $sucesso, 'ativo' => $ativo], $sucesso ? 200 : 404);
    }

    private function dadosFormulario(bool $exigeFoto, int $idStartup = 0): array
    {
        $dataRaw = trim($_POST['data_fundacao'] ?? '');
        $dataObj = DateTimeImmutable::createFromFormat('!Y-m-d', $dataRaw);
        $numero = filter_var($_POST['numero'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 2147483647],
        ]);

        $dados = [
            'nome'                 => trim($_POST['startupName'] ?? ''),
            'biografia'            => trim($_POST['biografia'] ?? '') ?: null,
            'cnpj'                 => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['cnpj'] ?? '')),
            'data_fundacao'        => $dataObj && $dataObj->format('Y-m-d') === $dataRaw ? $dataRaw : null,
            'setor_atuacao'        => trim($_POST['sector'] ?? ''),
            'site'                 => trim($_POST['site'] ?? '') ?: null,
            'instagram'            => trim($_POST['instagram'] ?? '') ?: null,
            'linkedin'             => trim($_POST['linkedin'] ?? '') ?: null,
            'cep'                  => preg_replace('/\D/', '', $_POST['cep'] ?? '') ?: null,
            'logradouro'           => trim($_POST['logradouro'] ?? ''),
            'numero'               => $numero === false ? null : $numero,
            'complemento'          => trim($_POST['complemento'] ?? '') ?: null,
            'bairro'               => trim($_POST['bairro'] ?? ''),
            'uf'                   => strtoupper(trim($_POST['uf'] ?? '')),
            'cidade'               => trim($_POST['cidade'] ?? ''),
            'outras_participacoes' => trim($_POST['programs'] ?? '') ?: null,
        ];

        $erros = [];
        $hoje = (new DateTimeImmutable('today', new DateTimeZone('America/Cuiaba')))->format('Y-m-d');

        foreach ([
            'nome'          => ['startupName', 100, 'Informe o nome da startup.'],
            'logradouro'    => ['logradouro',  255, 'Informe o logradouro.'],
            'bairro'        => ['bairro',      255, 'Informe o bairro.'],
            'cidade'        => ['cidade',      255, 'Informe a cidade.'],
            'setor_atuacao' => ['sector',       50, 'Informe o setor de atuação.'],
        ] as $chave => [$campoId, $max, $msg]) {
            if (!validador::required($dados[$chave])) {
                $erros[$campoId] = $msg;
            } elseif (mb_strlen($dados[$chave], 'UTF-8') > $max) {
                $erros[$campoId] = "Deve ter até {$max} caracteres.";
            }
        }

        if ($dados['biografia'] !== null && mb_strlen($dados['biografia'], 'UTF-8') > self::BIOGRAFIA_MAX) {
            $erros['biografia'] = 'A biografia deve ter até ' . self::BIOGRAFIA_MAX . ' caracteres.';
        }
        if (!validador::validarCnpj($dados['cnpj'])) {
            $erros['cnpj'] = $dados['cnpj'] === '' ? 'Informe o CNPJ.' : 'CNPJ inválido.';
        }
        foreach (['site', 'instagram', 'linkedin'] as $rede) {
            $url = $dados[$rede];
            if ($url !== null && (mb_strlen($url, 'UTF-8') > 255 || !preg_match('/^https?:\/\//i', $url) || !filter_var($url, FILTER_VALIDATE_URL))) {
                $erros[$rede] = 'Informe um link válido (http:// ou https://, até 255 caracteres).';
            }
        }
        if ($dados['cep'] !== null && (strlen($dados['cep']) !== 8 || $dados['cep'] === '00000000')) {
            $erros['cep'] = 'CEP inválido.';
        }
        if ($dados['numero'] === null) {
            $erros['numero'] = 'Informe o número do endereço (use 0 se não houver).';
        }
        if ($dados['complemento'] !== null && mb_strlen($dados['complemento'], 'UTF-8') > 255) {
            $erros['complemento'] = 'O complemento deve ter até 255 caracteres.';
        }
        if (!preg_match('/^[A-Z]{2}$/', $dados['uf'])) {
            $erros['uf'] = 'Informe a UF com 2 letras (ex.: MS).';
        }
        if ($dados['data_fundacao'] === null || $dados['data_fundacao'] > $hoje) {
            $erros['dataFundacao'] = $dados['data_fundacao'] === null ? 'Informe uma data de fundação válida.' : 'A data de fundação não pode ser futura.';
        }
        if ($dados['outras_participacoes'] !== null && mb_strlen($dados['outras_participacoes'], 'UTF-8') > 255) {
            $erros['programs'] = 'Deve ter até 255 caracteres.';
        }
        if ($exigeFoto && ($_FILES['foto_startup']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $erros['startup-photo'] = 'Adicione a foto da startup.';
        }

        if ($erros) {
            return [$dados, [], $erros, 422];
        }

        $fundadores = $this->fundadoresFormulario($_POST['fundadores'] ?? '[]', $idStartup > 0);
        $duplicados = [];

        if (startups::campoEmUso('startupName', $dados['nome'], $idStartup)) {
            $duplicados['startupName'] = 'Nome da startup já cadastrado.';
        }
        if (startups::campoEmUso('cnpj', $dados['cnpj'], $idStartup)) {
            $duplicados['cnpj'] = 'CNPJ já cadastrado.';
        }

        $emailsVistos = [];
        foreach ($fundadores as $i => $f) {
            if (in_array($f['email'], $emailsVistos, true)) {
                $duplicados['founderEmail'][$i] = 'E-mail de fundador repetido.';
            } elseif (startups::campoEmUso('founderEmail', $f['email'], $f['id'], $idStartup)) {
                $duplicados['founderEmail'][$i] = 'E-mail já cadastrado.';
            }
            $emailsVistos[] = $f['email'];
        }

        return [$dados, $fundadores, $duplicados, 409];
    }

    private function fundadoresFormulario(string $json, bool $permitirIds): array
    {
        $lista = json_decode($json, true);
        if (!is_array($lista) || !array_is_list($lista) || empty($lista)) {
            throw new RuntimeException(empty($lista) ? 'Adicione pelo menos um fundador.' : 'Dados dos fundadores inválidos.');
        }

        $preparados = [];
        $ids = [];

        foreach ($lista as $i => $item) {
            $n = $i + 1;
            if (!is_array($item)) {
                throw new RuntimeException("Fundador {$n}: dados inválidos.");
            }

            $id = filter_var($item['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            $f = [
                'id'       => $id,
                'nome'     => trim($item['nome'] ?? ''),
                'funcao'   => trim($item['funcao'] ?? ''),
                'email'    => strtolower(trim($item['email'] ?? '')),
                'telefone' => preg_replace('/\D/', '', $item['telefone'] ?? ''),
            ];

            if ($id === false || (!$permitirIds && $id !== 0) || ($id > 0 && in_array($id, $ids, true))) {
                throw new RuntimeException("Fundador {$n}: dados inválidos.");
            }
            if (!validador::required($f['nome']) || mb_strlen($f['nome'], 'UTF-8') > 100) {
                throw new RuntimeException("Fundador {$n}: nome obrigatório (até 100 caracteres).");
            }
            if (!validador::required($f['funcao']) || mb_strlen($f['funcao'], 'UTF-8') > 100) {
                throw new RuntimeException("Fundador {$n}: função obrigatória (até 100 caracteres).");
            }
            if (!validador::email($f['email'])) {
                throw new RuntimeException("Fundador {$n}: informe um e-mail válido.");
            }
            if ($f['telefone'] !== '' && (!validador::validarTel($f['telefone']) || strlen($f['telefone']) > 11)) {
                throw new RuntimeException("Fundador {$n}: telefone inválido (10 ou 11 dígitos).");
            }

            $ids[] = $id;
            $preparados[] = $f;
        }

        return $preparados;
    }

    private function subirFoto(): ?string
    {
        $arquivo = $_FILES['foto_startup'] ?? null;
        $erro = $arquivo['error'] ?? UPLOAD_ERR_NO_FILE;
        if (!$arquivo || $erro === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $tmp = $arquivo['tmp_name'] ?? '';
        if ($erro === UPLOAD_ERR_INI_SIZE || $erro === UPLOAD_ERR_FORM_SIZE || ($arquivo['size'] ?? 0) > self::FOTO_MAX_BYTES) {
            throw new RuntimeException('A imagem deve ter no máximo 2 MB.');
        }
        if ($erro !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            throw new RuntimeException('Não foi possível receber a imagem.');
        }

        $permitidos = ['image/jpeg' => ['jpg', 'jpeg'], 'image/png' => ['png'], 'image/webp' => ['webp']];
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $ext = strtolower(pathinfo(basename((string) ($arquivo['name'] ?? '')), PATHINFO_EXTENSION));
        $img = @getimagesize($tmp);

        if (!is_string($mime) || !isset($permitidos[$mime]) || !in_array($ext, $permitidos[$mime], true) || $img === false || ($img['mime'] ?? null) !== $mime) {
            throw new RuntimeException('O arquivo deve ser JPEG, JPG, PNG ou WEBP.');
        }

        $pasta = $_SERVER['DOCUMENT_ROOT'] . BASE_UPLOAD . 'Startup/';
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            throw new RuntimeException('Não foi possível preparar a pasta de upload.');
        }

        $nome = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($tmp, $pasta . $nome)) {
            throw new RuntimeException('Não foi possível salvar a foto.');
        }

        return $nome;
    }

    private function removerFoto(?string $nome): void
    {
        if ($nome === null) {
            return;
        }
        $caminho = $_SERVER['DOCUMENT_ROOT'] . BASE_UPLOAD . 'Startup/' . $nome;
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }

    private function responderJson(array $corpo, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($corpo);
    }

    private function responderErro(Throwable $e, string $acao): void
    {
        if ($e instanceof PDOException && $e->getCode() === '23000') {
            $this->responderJson(['sucesso' => false, 'erro' => 'Registro já cadastrado (nome, CNPJ ou e-mail em uso).'], 409);
            return;
        }
        if ($e instanceof RuntimeException && !($e instanceof PDOException)) {
            $this->responderJson(['sucesso' => false, 'erro' => $e->getMessage()], 422);
            return;
        }

        error_log("[crudStartups::{$acao}] " . get_class($e) . ': ' . $e->getMessage());
        $this->responderJson(['sucesso' => false, 'erro' => 'Erro ao salvar a startup. Tente novamente.'], 500);
    }
}

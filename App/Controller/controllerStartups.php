<?php

namespace Controller;

use Core\controller;
use Core\validador;
use DateTimeImmutable;
use DateTimeZone;
use Models\startups;
use PDOException;
use RuntimeException;

class controllerStartups extends controller
{
    public function index()
    {
        $this->mostrarTela("crudStartups");
    }

    public function adicionar()
    {
        $dados = $this->prepararDados($_POST);
        if (!validador::validarCnpj($dados['cnpj'])) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'erro' => 'CNPJ inválido.']);
            return;
        }

        $cepInformado = trim($_POST['cep'] ?? '');
        if ($cepInformado !== ''
            && (!preg_match('/^\d{5}-?\d{3}$/', $cepInformado) || $dados['cep'] === '00000000')) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'erro' => 'CEP inválido.']);
            return;
        }

        if (!$this->dadosValidos($dados)) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'erro' => 'Confira os campos obrigatórios, a data de fundação e o endereço.']);
            return;
        }

        try {
            $fundadores = $this->prepararFundadores($_POST['fundadores'] ?? '[]', $dados['data_fundacao']);
        } catch (RuntimeException $e) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
            return;
        }

        $errosCampos = $this->errosCamposDuplicados($dados, $fundadores);
        if ($errosCampos) {
            http_response_code(409);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['sucesso' => false, 'errosCampos' => $errosCampos]);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        $logoSalvo = null;
        try {
            $dados['logo'] = $this->processarUploadFoto();
            $logoSalvo = $dados['logo'];
            if ($dados['logo'] === null) {
                throw new RuntimeException('Adicione a foto da startup.');
            }
            $id = startups::cadastrar($dados, $fundadores);
            echo json_encode(['sucesso' => true, 'id_startup' => $id]);
        } catch (PDOException $e) {
            $this->removerFotoSeFalhou($logoSalvo);
            $errosCampos = $e->getCode() === '23000'
                ? $this->errosCamposDuplicados($dados, $fundadores) : [];
            http_response_code($errosCampos ? 409 : 500);
            echo json_encode($errosCampos
                ? ['sucesso' => false, 'errosCampos' => $errosCampos]
                : ['sucesso' => false, 'erro' => 'Não foi possível cadastrar a startup.']);
        } catch (RuntimeException $e) {
            $this->removerFotoSeFalhou($logoSalvo);
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function atualizar()
    {
        $id = (int) ($_POST['id_startup'] ?? 0);
        header('Content-Type: application/json; charset=utf-8');
        if ($id <= 0) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            return;
        }

        $atual = startups::buscarPorId($id);
        if (!$atual) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'erro' => 'Startup não encontrada.']);
            return;
        }

        $dados = $this->prepararDados($_POST);
        $cepInformado = trim($_POST['cep'] ?? '');
        if (!validador::validarCnpj($dados['cnpj'])
            || ($cepInformado !== '' && (!preg_match('/^\d{5}-?\d{3}$/', $cepInformado) || $dados['cep'] === '00000000'))
            || !$this->dadosValidos($dados)) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'Confira os campos obrigatórios, o CNPJ, o CEP e a data de fundação.']);
            return;
        }

        try {
            $fundadores = $this->prepararFundadores($_POST['fundadores'] ?? '[]', $dados['data_fundacao'], true);
        } catch (RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
            return;
        }

        $errosCampos = $this->errosCamposDuplicados($dados, $fundadores, $id);
        if ($errosCampos) {
            http_response_code(409);
            echo json_encode(['sucesso' => false, 'errosCampos' => $errosCampos]);
            return;
        }

        $novaFoto = null;
        try {
            $novaFoto = $this->processarUploadFoto();
            $dados['logo'] = $novaFoto ?? $atual['logo'];
            $ok = startups::atualizar($id, $dados, $fundadores);
            if ($novaFoto && $atual['logo'] && preg_match('/^[a-f0-9]{32}\.(?:jpg|jpeg|png|webp)$/', $atual['logo'])) {
                $this->removerFotoSeFalhou($atual['logo']);
            }
            echo json_encode(['sucesso' => $ok]);
        } catch (PDOException $e) {
            $this->removerFotoSeFalhou($novaFoto);
            $errosCampos = $e->getCode() === '23000'
                ? $this->errosCamposDuplicados($dados, $fundadores, $id) : [];
            http_response_code($errosCampos ? 409 : 500);
            echo json_encode($errosCampos
                ? ['sucesso' => false, 'errosCampos' => $errosCampos]
                : ['sucesso' => false, 'erro' => 'Não foi possível atualizar a startup.']);
        } catch (RuntimeException $e) {
            $this->removerFotoSeFalhou($novaFoto);
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function buscar()
    {
        $id = (int) ($_GET['id'] ?? 0);

        header('Content-Type: application/json; charset=utf-8');

        if ($id <= 0) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            return;
        }

        $startup = startups::buscarPorId($id);

        if (!$startup) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'erro' => 'Startup não encontrada.']);
            return;
        }

        echo json_encode(['sucesso' => true, 'startup' => $startup]);
    }

    public function validarCampo()
    {
        header('Content-Type: application/json; charset=utf-8');
        $campo = $_POST['campo'] ?? '';
        $valor = trim($_POST['valor'] ?? '');
        $id = max(0, (int) ($_POST['id'] ?? 0));
        $startupId = max(0, (int) ($_POST['id_startup'] ?? 0));
        if (!in_array($campo, ['startupName', 'cnpj', 'founderEmail'], true)) {
            http_response_code(422);
            echo json_encode(['valido' => false, 'erro' => 'Campo inválido.']);
            return;
        }

        if ($campo === 'cnpj') {
            $valor = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $valor));
        } elseif ($campo === 'founderEmail') {
            $valor = strtolower($valor);
        }

        if ($valor === '') {
            http_response_code(422);
            echo json_encode(['valido' => false, 'erro' => 'Campo obrigatório.']);
            return;
        }

        $mensagens = [
            'startupName' => 'Nome da startup já cadastrado.',
            'cnpj' => 'CNPJ já cadastrado.',
            'founderEmail' => 'E-mail já cadastrado.',
        ];
        $emUso = startups::campoEmUso($campo, $valor, $id, $startupId);
        echo json_encode(['valido' => !$emUso, 'erro' => $emUso ? $mensagens[$campo] : null]);
    }

    // JSON puro pro Componentes/tabela.js (buscar() faz fetch em
    // /startups/tabela) — mesmo formato de controllerParceiros::tabela().
    public function tabela()
    {
        $startups = startups::listar();

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($startups);
        exit;
    }

    public function status()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0 || !in_array($_POST['ativo'] ?? null, ['0', '1'], true)) {
            http_response_code(422);
            echo json_encode(['sucesso' => false, 'erro' => 'Dados de status inválidos.']);
            exit;
        }

        $ativo = ($_POST['ativo'] ?? '') === '1';

        $sucesso = startups::alterarStatus($id, $ativo);

        if (!$sucesso) http_response_code(404);

        echo json_encode(['sucesso' => $sucesso, 'ativo' => $ativo]);
        exit;
    }

    private function prepararDados(array $post): array
    {
        $data = (!empty($post['foundDay']) && !empty($post['foundMonth']) && !empty($post['foundYear']))
            ? sprintf('%04d-%02d-%02d', $post['foundYear'], $post['foundMonth'], $post['foundDay'])
            : null;
        $setor = trim($post['sector'] ?? '');
        if ($setor === 'outros') {
            $setor = trim($post['sectorOther'] ?? '');
        }
        $numero = filter_var($post['numero'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

        return [
            'nome'                   => trim($post['startupName'] ?? ''),
            'cnpj'                   => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $post['cnpj'] ?? '')),
            'data_fundacao'          => $data,
            'trl'                    => (int) ($post['trl'] ?? 0),
            'setor_atuacao'          => $setor,
            'site'                   => trim($post['site'] ?? '') ?: null,
            'instagram'              => trim($post['instagram'] ?? '') ?: null,
            'linkedin'               => trim($post['linkedin'] ?? '') ?: null,
            'cep'                    => preg_replace('/\D/', '', $post['cep'] ?? '') ?: null,
            'logradouro'             => trim($post['logradouro'] ?? ''),
            'numero'                 => $numero === false ? -1 : $numero,
            'complemento'            => trim($post['complemento'] ?? '') ?: null,
            'bairro'                 => trim($post['bairro'] ?? ''),
            'uf'                     => strtoupper(trim($post['uf'] ?? '')),
            'cidade'                 => trim($post['cidade'] ?? ''),
            'outras_participacoes'   => trim($post['programs'] ?? '') ?: null,
        ];
    }

    private function dadosValidos(array $dados): bool
    {
        $data = $dados['data_fundacao'];
        $partes = $data ? explode('-', $data) : [];
        $hoje = (new DateTimeImmutable('today', new DateTimeZone('America/Cuiaba')))->format('Y-m-d');

        return $dados['nome'] !== ''
            && count($partes) === 3
            && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])
            && $data <= $hoje
            && $dados['trl'] >= 1 && $dados['trl'] <= 9
            && $dados['setor_atuacao'] !== ''
            && $dados['logradouro'] !== ''
            && $dados['numero'] >= 0
            && $dados['bairro'] !== ''
            && preg_match('/^[A-Z]{2}$/', $dados['uf'])
            && $dados['cidade'] !== ''
            && ($dados['outras_participacoes'] === null || mb_strlen($dados['outras_participacoes'], 'UTF-8') <= 50)
            && ($dados['cep'] === null || strlen($dados['cep']) === 8);
    }

    private function prepararFundadores(string $json, string $dataFundacao, bool $permitirIds = false): array
    {
        $fundadores = json_decode($json, true);
        if (!is_array($fundadores) || !array_is_list($fundadores)) {
            throw new RuntimeException('Confira os dados dos fundadores.');
        }
        if (count($fundadores) === 0) {
            throw new RuntimeException('Adicione pelo menos um fundador.');
        }

        $preparados = [];
        $emails = [];
        $ids = [];
        $hoje = (new DateTimeImmutable('today', new DateTimeZone('America/Cuiaba')))->format('Y-m-d');
        foreach ($fundadores as $fundador) {
            if (!is_array($fundador)) throw new RuntimeException('Confira os dados dos fundadores.');
            $id = filter_var($fundador['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            $dados = [
                'id' => $id,
                'nome' => trim($fundador['nome'] ?? ''),
                'funcao' => trim($fundador['funcao'] ?? ''),
                'temp_empresa' => trim($fundador['temp_empresa'] ?? ''),
                'email' => strtolower(trim($fundador['email'] ?? '')),
                'telefone' => preg_replace('/\D/', '', $fundador['telefone'] ?? ''),
            ];
            $dataEntrada = DateTimeImmutable::createFromFormat('!Y-m-d', $dados['temp_empresa']);
            if ($id === false || (!$permitirIds && $id !== 0)
                || ($id > 0 && in_array($id, $ids, true))
                || $dados['nome'] === '' || strlen($dados['nome']) > 100
                || $dados['funcao'] === '' || mb_strlen($dados['funcao'], 'UTF-8') > 100
                || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)
                || !$dataEntrada || $dataEntrada->format('Y-m-d') !== $dados['temp_empresa']
                || $dados['temp_empresa'] < $dataFundacao || $dados['temp_empresa'] > $hoje
                || ($dados['telefone'] !== '' && strlen($dados['telefone']) > 15)
                || in_array($dados['email'], $emails, true)) {
                throw new RuntimeException('Confira nome, função, e-mail e data de entrada dos fundadores.');
            }
            $ids[] = $id;
            $emails[] = $dados['email'];
            $preparados[] = $dados;
        }
        return $preparados;
    }

    private function errosCamposDuplicados(array $dados, array $fundadores, int $idStartup = 0): array
    {
        $erros = [];
        if (startups::campoEmUso('startupName', $dados['nome'], $idStartup)) {
            $erros['startupName'] = 'Nome da startup já cadastrado.';
        }
        if (startups::campoEmUso('cnpj', $dados['cnpj'], $idStartup)) {
            $erros['cnpj'] = 'CNPJ já cadastrado.';
        }
        foreach ($fundadores as $indice => $fundador) {
            if (startups::campoEmUso('founderEmail', $fundador['email'], $fundador['id'], $idStartup)) {
                $erros['founderEmail'][$indice] = 'E-mail já cadastrado.';
            }
        }
        return $erros;
    }

    private function processarUploadFoto(): ?string
    {
        $arquivo = $_FILES['foto_startup'] ?? null;
        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $caminhoTemporario = $arquivo['tmp_name'] ?? '';
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($caminhoTemporario)) {
            throw new RuntimeException('Não foi possível receber a imagem. Tente novamente.');
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
            throw new RuntimeException('O arquivo deve ser uma imagem JPEG, JPG, PNG ou WEBP.');
        }

        $pasta = $_SERVER['DOCUMENT_ROOT'] . BASE_UPLOAD . 'Startup/';
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            throw new RuntimeException('Não foi possível preparar o destino da imagem.');
        }

        $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;

        if (!move_uploaded_file($caminhoTemporario, $pasta . $nomeArquivo)) {
            throw new RuntimeException('Não foi possível salvar a foto.');
        }

        return $nomeArquivo;
    }

    private function removerFotoSeFalhou(?string $nomeArquivo): void
    {
        if ($nomeArquivo === null) return;
        $caminho = $_SERVER['DOCUMENT_ROOT'] . BASE_UPLOAD . 'Startup/' . $nomeArquivo;
        if (is_file($caminho)) unlink($caminho);
    }
}

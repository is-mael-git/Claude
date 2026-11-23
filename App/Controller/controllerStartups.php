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
    // Limites que o JS (crudStartups.js) repete para avisar logo na seleção; mantenha os dois iguais.
    private const BIOGRAFIA_MAX = 3000;
    private const FOTO_MAX_BYTES = 2 * 1024 * 1024;
    private const MENSAGEM_FOTO_GRANDE = 'A imagem deve ter no máximo 2 MB. Escolha uma imagem menor.';

    public function index()
    {
        $this->mostrarTela("crudStartups");
    }

    public function adicionar()
    {
        $dados = [];
        $fundadores = [];
        $logoSalvo = null;
        try {
            if ($this->corpoExcedeuLimite()) {
                return;
            }

            $dados = $this->prepararDados($_POST);
            $erros = $this->errosDados($dados, true);
            if ($erros) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $erros], 422);
                return;
            }

            $fundadores = $this->prepararFundadores($_POST['fundadores'] ?? '[]');

            $errosCampos = $this->errosCamposDuplicados($dados, $fundadores);
            if ($errosCampos) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $errosCampos], 409);
                return;
            }

            try {
                $logoSalvo = $this->processarUploadFoto();
            } catch (RuntimeException $e) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => ['startup-photo' => $e->getMessage()]], 422);
                return;
            }
            $dados['logo'] = $logoSalvo;

            $id = startups::cadastrar($dados, $fundadores);
            $this->responderJson(['sucesso' => true, 'id_startup' => $id]);
        } catch (\Throwable $e) {
            $this->removerFotoSeFalhou($logoSalvo);
            $this->responderFalha($e, 'adicionar', $dados, $fundadores);
        }
    }

    public function atualizar()
    {
        $id = (int) ($_POST['id_startup'] ?? 0);
        $dados = [];
        $fundadores = [];
        $novaFoto = null;
        try {
            if ($this->corpoExcedeuLimite()) {
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

            $dados = $this->prepararDados($_POST);
            $erros = $this->errosDados($dados, false);
            if ($erros) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $erros], 422);
                return;
            }

            $fundadores = $this->prepararFundadores($_POST['fundadores'] ?? '[]', true);

            $errosCampos = $this->errosCamposDuplicados($dados, $fundadores, $id);
            if ($errosCampos) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $errosCampos], 409);
                return;
            }

            try {
                $novaFoto = $this->processarUploadFoto();
            } catch (RuntimeException $e) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => ['startup-photo' => $e->getMessage()]], 422);
                return;
            }
            $dados['logo'] = $novaFoto ?? $atual['logo'];

            $ok = startups::atualizar($id, $dados, $fundadores);
            if ($novaFoto && $atual['logo'] && preg_match('/^[a-f0-9]{32}\.(?:jpg|jpeg|png|webp)$/', $atual['logo'])) {
                $this->removerFotoSeFalhou($atual['logo']);
            }
            $this->responderJson(['sucesso' => $ok]);
        } catch (\Throwable $e) {
            $this->removerFotoSeFalhou($novaFoto);
            $this->responderFalha($e, 'atualizar', $dados, $fundadores, $id);
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
        $data = trim($post['data_fundacao'] ?? '');
        $dataFundacao = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        $data = $dataFundacao && $dataFundacao->format('Y-m-d') === $data ? $data : null;
        $setor = trim($post['sector'] ?? '');
        $numero = filter_var($post['numero'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 2147483647]]);
        $biografia = trim($post['biografia'] ?? '');

        return [
            'nome'                   => trim($post['startupName'] ?? ''),
            'biografia'              => $biografia === '' ? null : $biografia,
            'cnpj'                   => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $post['cnpj'] ?? '')),
            'data_fundacao'          => $data,
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

    // Confere os dados da startup e devolve [id do campo no formulário => mensagem].
    // Array vazio = tudo certo. Os ids são os de crudStartups.php, pro JS achar o campo.
    private function errosDados(array $dados, bool $exigeFoto): array
    {
        $erros = [];
        $hoje = (new DateTimeImmutable('today', new DateTimeZone('America/Cuiaba')))->format('Y-m-d');

        if ($dados['nome'] === '') {
            $erros['startupName'] = 'Informe o nome da startup.';
        } elseif (mb_strlen($dados['nome'], 'UTF-8') > 100) {
            $erros['startupName'] = 'O nome da startup deve ter até 100 caracteres.';
        }

        if ($dados['biografia'] !== null && mb_strlen($dados['biografia'], 'UTF-8') > self::BIOGRAFIA_MAX) {
            $erros['biografia'] = 'A biografia deve ter até ' . self::BIOGRAFIA_MAX . ' caracteres.';
        }

        if ($dados['cnpj'] === '') {
            $erros['cnpj'] = 'Informe o CNPJ.';
        } elseif (!validador::validarCnpj($dados['cnpj'])) {
            $erros['cnpj'] = 'CNPJ inválido.';
        }

        // links: só http/https, senão um "javascript:..." salvo aqui viraria link perigoso numa tela futura
        foreach (['site', 'instagram', 'linkedin'] as $rede) {
            if ($dados[$rede] !== null && !$this->urlValida($dados[$rede])) {
                $erros[$rede] = 'Informe um link válido, começando com http:// ou https:// (até 255 caracteres).';
            }
        }

        if ($dados['cep'] !== null && (strlen($dados['cep']) !== 8 || $dados['cep'] === '00000000')) {
            $erros['cep'] = 'CEP inválido.';
        }

        if ($dados['logradouro'] === '') {
            $erros['logradouro'] = 'Informe o logradouro.';
        } elseif (mb_strlen($dados['logradouro'], 'UTF-8') > 255) {
            $erros['logradouro'] = 'O logradouro deve ter até 255 caracteres.';
        }

        if ($dados['numero'] < 0) {
            $erros['numero'] = 'Informe o número do endereço (use 0 se não houver).';
        }

        if ($dados['complemento'] !== null && mb_strlen($dados['complemento'], 'UTF-8') > 255) {
            $erros['complemento'] = 'O complemento deve ter até 255 caracteres.';
        }

        if ($dados['bairro'] === '') {
            $erros['bairro'] = 'Informe o bairro.';
        } elseif (mb_strlen($dados['bairro'], 'UTF-8') > 255) {
            $erros['bairro'] = 'O bairro deve ter até 255 caracteres.';
        }

        if ($dados['cidade'] === '') {
            $erros['cidade'] = 'Informe a cidade.';
        } elseif (mb_strlen($dados['cidade'], 'UTF-8') > 255) {
            $erros['cidade'] = 'A cidade deve ter até 255 caracteres.';
        }

        if (!preg_match('/^[A-Z]{2}$/', $dados['uf'])) {
            $erros['uf'] = 'Informe a UF com 2 letras (ex.: MS).';
        }

        if ($dados['setor_atuacao'] === '') {
            $erros['sector'] = 'Informe o setor de atuação.';
        } elseif (mb_strlen($dados['setor_atuacao'], 'UTF-8') > 150) {
            $erros['sector'] = 'O setor de atuação deve ter até 150 caracteres.';
        }

        if ($dados['data_fundacao'] === null) {
            $erros['dataFundacao'] = 'Informe uma data de fundação válida.';
        } elseif ($dados['data_fundacao'] > $hoje) {
            $erros['dataFundacao'] = 'A data de fundação não pode ser futura.';
        }

        if ($dados['outras_participacoes'] !== null && mb_strlen($dados['outras_participacoes'], 'UTF-8') > 50) {
            $erros['programs'] = 'Participação em outros programas deve ter até 50 caracteres.';
        }

        $semFoto = ($_FILES['foto_startup']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE;
        if ($exigeFoto && $semFoto) {
            $erros['startup-photo'] = 'Adicione a foto da startup.';
        }

        return $erros;
    }

    private function urlValida(string $url): bool
    {
        return mb_strlen($url, 'UTF-8') <= 255
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    private function prepararFundadores(string $json, bool $permitirIds = false): array
    {
        $fundadores = json_decode($json, true);
        if (!is_array($fundadores) || !array_is_list($fundadores)) {
            throw new RuntimeException('Os dados dos fundadores chegaram em formato inválido. Recarregue a página e tente de novo.');
        }
        if (count($fundadores) === 0) {
            throw new RuntimeException('Adicione pelo menos um fundador.');
        }

        $preparados = [];
        $ids = [];
        foreach ($fundadores as $indice => $fundador) {
            $numero = $indice + 1;
            if (!is_array($fundador)) {
                throw $this->erroFundador($numero, 'dados inválidos. Recarregue a página e tente de novo.');
            }
            $id = filter_var($fundador['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            $dados = [
                'id' => $id,
                'nome' => trim($fundador['nome'] ?? ''),
                'funcao' => trim($fundador['funcao'] ?? ''),
                'email' => strtolower(trim($fundador['email'] ?? '')),
                'telefone' => preg_replace('/\D/', '', $fundador['telefone'] ?? ''),
            ];

            if ($id === false || (!$permitirIds && $id !== 0) || ($id > 0 && in_array($id, $ids, true))) {
                throw $this->erroFundador($numero, 'dados inválidos. Recarregue a página e tente de novo.');
            }
            if ($dados['nome'] === '') {
                throw $this->erroFundador($numero, 'informe o nome.');
            }
            if (mb_strlen($dados['nome'], 'UTF-8') > 100) {
                throw $this->erroFundador($numero, 'o nome deve ter até 100 caracteres.');
            }
            if ($dados['funcao'] === '') {
                throw $this->erroFundador($numero, 'informe a função.');
            }
            if (mb_strlen($dados['funcao'], 'UTF-8') > 100) {
                throw $this->erroFundador($numero, 'a função deve ter até 100 caracteres.');
            }
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                throw $this->erroFundador($numero, 'informe um e-mail válido.');
            }
            if ($dados['telefone'] !== '' && strlen($dados['telefone']) > 15) {
                throw $this->erroFundador($numero, 'telefone inválido (até 15 dígitos).');
            }

            $ids[] = $id;
            $preparados[] = $dados;
        }
        return $preparados;
    }

    private function erroFundador(int $numero, string $mensagem): RuntimeException
    {
        return new RuntimeException("Fundador {$numero}: {$mensagem}");
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
        $emailsVistos = [];
        foreach ($fundadores as $indice => $fundador) {
            if (in_array($fundador['email'], $emailsVistos, true)) {
                $erros['founderEmail'][$indice] = 'E-mail de fundador repetido.';
            } elseif (startups::campoEmUso('founderEmail', $fundador['email'], $fundador['id'], $idStartup)) {
                $erros['founderEmail'][$indice] = 'E-mail já cadastrado.';
            }
            $emailsVistos[] = $fundador['email'];
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
        $codigoErro = $arquivo['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($codigoErro === UPLOAD_ERR_INI_SIZE || $codigoErro === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException(self::MENSAGEM_FOTO_GRANDE);
        }
        if ($codigoErro !== UPLOAD_ERR_OK || !is_uploaded_file($caminhoTemporario)) {
            throw new RuntimeException('Não foi possível receber a imagem. Tente novamente.');
        }
        if (($arquivo['size'] ?? 0) > self::FOTO_MAX_BYTES) {
            throw new RuntimeException(self::MENSAGEM_FOTO_GRANDE);
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

    private function responderJson(array $corpo, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($corpo);
    }

    // Resposta única para qualquer falha que escape das validações (banco, upload, bug).
    // O texto técnico só vai pro log do PHP; a tela recebe uma mensagem que não expõe nada interno.
    private function responderFalha(\Throwable $e, string $acao, array $dados = [], array $fundadores = [], int $idStartup = 0): void
    {
        if ($e instanceof PDOException && $e->getCode() === '23000') {
            // UNIQUE violado (dois cadastros ao mesmo tempo): confere de novo pra apontar o campo
            $errosCampos = $this->errosCamposDuplicados($dados, $fundadores, $idStartup);
            if ($errosCampos) {
                $this->responderJson(['sucesso' => false, 'errosCampos' => $errosCampos], 409);
                return;
            }
        }

        // PDOException também é RuntimeException, por isso o teste explícito
        if ($e instanceof RuntimeException && !($e instanceof PDOException)) {
            $this->responderJson(['sucesso' => false, 'erro' => $e->getMessage()], 422);
            return;
        }

        error_log('[crudStartups::' . $acao . '] ' . get_class($e) . ': ' . $e->getMessage());
        $mensagem = $e instanceof PDOException
            ? 'Não foi possível salvar no banco de dados. Tente novamente; se continuar, avise o responsável.'
            : 'Erro inesperado no servidor. Tente novamente; se continuar, avise o responsável.';
        $this->responderJson(['sucesso' => false, 'erro' => $mensagem], 500);
    }

    // Se o corpo da requisição passar do post_max_size, o PHP descarta $_POST e $_FILES inteiros;
    // sem este aviso a tela mostraria "informe o nome..." para todos os campos.
    private function corpoExcedeuLimite(): bool
    {
        if (!empty($_POST) || !empty($_FILES) || (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) <= 0) {
            return false;
        }
        $this->responderJson([
            'sucesso' => false,
            'errosCampos' => ['startup-photo' => 'Os dados enviados passam do limite do servidor (' . ini_get('post_max_size') . '). Escolha uma imagem menor.'],
        ], 413);
        return true;
    }
}

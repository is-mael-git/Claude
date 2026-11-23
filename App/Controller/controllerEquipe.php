<?php
    namespace Controller;
    use Core\controller;
    use Models\equipe;
    class controllerEquipe extends controller{
        public function index(){
            $salvo = !empty($_SESSION['equipeSalva']);
            unset($_SESSION['equipeSalva']);
            $this->mostrarTela("equipe", [
                'perfis' => equipe::listarPerfis(),
                'salvo' => $salvo
            ]);
        }

        public function cadastrarPerfil(): void
        {
            header('Content-Type: application/json; charset=utf-8');
            $entrada = json_decode(file_get_contents('php://input'), true);
            if (!is_array($entrada)) {
                $entrada = $_POST;
            }

            $nome = trim((string) ($entrada['nome'] ?? ''));
            $permissoes = $entrada['permissoes'] ?? [];
            $tamanhoNome = preg_match_all('/./us', $nome);

            if ($nome === '' || $tamanhoNome === false || $tamanhoNome > 50 || !is_array($permissoes)) {
                http_response_code(422);
                echo json_encode(['sucesso' => false, 'erro' => 'Informe um nome de perfil válido.']);
                exit;
            }

            try {
                $id = equipe::cadastrarPerfil($nome, $permissoes);
                http_response_code(201);
                echo json_encode([
                    'sucesso' => true,
                    'perfil' => ['id' => $id, 'nome' => $nome]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            } catch (\InvalidArgumentException $erro) {
                http_response_code(422);
                echo json_encode(['sucesso' => false, 'erro' => $erro->getMessage()], JSON_UNESCAPED_UNICODE);
                exit;
            } catch (\PDOException $erro) {
                if ((int) ($erro->errorInfo[1] ?? 0) === 1062) {
                    http_response_code(409);
                    echo json_encode(['sucesso' => false, 'erro' => 'Já existe um perfil com esse nome.'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                error_log('Falha ao cadastrar perfil de equipe: ' . $erro->getMessage());
                http_response_code(500);
                echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar o perfil.'], JSON_UNESCAPED_UNICODE);
                exit;
            } catch (\Throwable $erro) {
                error_log('Falha ao cadastrar perfil de equipe: ' . $erro->getMessage());
                http_response_code(500);
                echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar o perfil.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        public function tabela(){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(equipe::listar(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        public function buscar(){
            header('Content-Type: application/json; charset=utf-8');
            $id = (int) ($_GET['id'] ?? 0);
            $membro = $id > 0 ? equipe::buscarPorId($id) : null;
            if (!$membro) {
                http_response_code(404);
                echo json_encode(['sucesso' => false, 'erro' => 'Membro da equipe não encontrado.']);
                exit;
            }
            echo json_encode(['sucesso' => true, 'membro' => $membro], JSON_UNESCAPED_UNICODE);
            exit;
        }

        public function status(){
            header('Content-Type: application/json; charset=utf-8');
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(422);
                echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
                exit;
            }
            $ativo = ($_POST['ativo'] ?? '') === '1';
            echo json_encode(['sucesso' => equipe::alterarStatus($id, $ativo), 'ativo' => $ativo]);
            exit;
        }

        public function cpfExiste(){
            header('Content-Type: application/json; charset=utf-8');
            $cpf = preg_replace('/\D/', '', $_GET['cpf'] ?? '');
            $ignorarId = (int) ($_GET['ignorar'] ?? 0);
            echo json_encode(['existe' => strlen($cpf) === 11 && equipe::cpfExiste($cpf, $ignorarId > 0 ? $ignorarId : null)]);
            exit;
        }

        public function cadastrar(){
            $arquivoFoto = $_FILES['foto'] ?? null;
            if (!$arquivoFoto || ($arquivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($arquivoFoto['tmp_name'] ?? '')) {
                http_response_code(422);
                exit('Anexe uma imagem válida.');
            }

            $detectorMime = new \finfo(FILEINFO_MIME_TYPE);
            $tipoFoto = $detectorMime->file($arquivoFoto['tmp_name']);
            if (!is_string($tipoFoto) || strpos($tipoFoto, 'image/') !== 0) {
                http_response_code(422);
                exit('O arquivo anexado deve ser uma imagem.');
            }

            $dados = [
                'nome' => trim($_POST['nome'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'cpf' => preg_replace('/\D/', '', $_POST['cpf'] ?? ''),
                'telefone' => preg_replace('/\D/', '', $_POST['telefone'] ?? ''),
                'perfil' => (int) ($_POST['perfil'] ?? 0),
                'cargo' => trim($_POST['cargo'] ?? ''),
                'dias' => $_POST['diaSemana'] ?? [],
                'horarios' => $_POST['horario'] ?? [],
                'modalidade' => $_POST['modalidade'] ?? '',
            ];
            if ($this->cpfInvalido($dados['cpf'])) {
                http_response_code(422);
                exit('Informe um CPF válido.');
            }
            if (equipe::cpfExiste($dados['cpf'])) {
                http_response_code(422);
                exit('Este CPF já está cadastrado.');
            }
            if (equipe::emailExiste($dados['email'])) {
                http_response_code(422);
                exit('Este e-mail já está cadastrado. Informe outro e-mail.');
            }
            if (equipe::telefoneExiste($dados['telefone'])) {
                http_response_code(422);
                exit('Este telefone já está cadastrado. Informe outro telefone.');
            }
            $perfisValidos = array_column(equipe::listarPerfis(), 'id');
            $dados['dias'] = array_values(array_intersect($dados['dias'], ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado']));
            $dados['horarios'] = array_values(array_intersect($dados['horarios'], ['matutino', 'vespertino', 'noturno']));
            if (!$dados['nome'] || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || strlen($dados['cpf']) !== 11 || !$dados['telefone'] || !$dados['perfil'] || !$dados['cargo'] || !$dados['modalidade'] || !$dados['dias'] || !$dados['horarios']) {
                http_response_code(422);
                exit('Preencha corretamente os campos obrigatórios.');
            }
            if (!in_array($dados['perfil'], array_map('intval', $perfisValidos), true) || !in_array($dados['modalidade'], ['presencial', 'remoto', 'hibrido'], true)) {
                http_response_code(422);
                exit('Perfil ou modalidade inválidos.');
            }
            try {
                equipe::cadastrarEquipe($dados);
            } catch (\PDOException $erro) {
                $codigoMysql = (int) ($erro->errorInfo[1] ?? 0);
                if ($codigoMysql === 1062) {
                    http_response_code(422);
                    exit('E-mail ou telefone já cadastrado. Informe um valor diferente.');
                }
                if ($codigoMysql === 1452) {
                    http_response_code(422);
                    exit('O perfil selecionado não existe mais. Atualize a tela e escolha um perfil válido.');
                }

                http_response_code(500);
                $detalheMysql = $erro->errorInfo[2] ?? $erro->getMessage();
                error_log(sprintf('Falha ao cadastrar equipe [SQLSTATE %s / MySQL %d]: %s', $erro->getCode(), $codigoMysql, $detalheMysql));
                exit(sprintf('Falha no banco (SQLSTATE %s, código MySQL %d): %s', $erro->getCode(), $codigoMysql, $detalheMysql));
            } catch (\Throwable $erro) {
                http_response_code(500);
                error_log('Falha ao cadastrar membro da equipe: ' . $erro->getMessage());
                exit('Ocorreu uma falha ao salvar o cadastro. ' . $erro->getMessage());
            }
            $_SESSION['equipeSalva'] = true;
            if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => true, 'redirect' => BASE_URL . 'equipe/index']);
                return;
            }
            $this->redirecionar('/equipe/index');
        }

        public function atualizar(){
            $id = (int) ($_POST['id'] ?? 0);
            $membroAtual = $id > 0 ? equipe::buscarPorId($id) : null;
            if (!$membroAtual) {
                http_response_code(422);
                exit('Membro da equipe não encontrado. Atualize a tela e tente novamente.');
            }

            $dados = [
                'nome' => trim($_POST['nome'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'cpf' => preg_replace('/\D/', '', $_POST['cpf'] ?? ''),
                'telefone' => preg_replace('/\D/', '', $_POST['telefone'] ?? ''),
                'perfil' => (int) ($_POST['perfil'] ?? 0),
                'cargo' => trim($_POST['cargo'] ?? ''),
                'dias' => is_array($_POST['diaSemana'] ?? null) ? $_POST['diaSemana'] : [],
                'horarios' => is_array($_POST['horario'] ?? null) ? $_POST['horario'] : [],
                'modalidade' => $_POST['modalidade'] ?? '',
            ];
            $cpfSemAlteracao = $dados['cpf'] === preg_replace('/\D/', '', (string) $membroAtual['cpf']);
            if (!$cpfSemAlteracao && $this->cpfInvalido($dados['cpf'])) {
                http_response_code(422);
                exit('Informe um CPF válido.');
            }
            if (!$cpfSemAlteracao && equipe::cpfExiste($dados['cpf'], $id)) {
                http_response_code(422);
                exit('Este CPF já está cadastrado.');
            }
            if (equipe::emailExiste($dados['email'], $id)) {
                http_response_code(422);
                exit('Este e-mail já está cadastrado.');
            }
            if (equipe::telefoneExiste($dados['telefone'], $id)) {
                http_response_code(422);
                exit('Este telefone já está cadastrado.');
            }

            $perfisValidos = array_map('intval', array_column(equipe::listarPerfis(), 'id'));
            $dados['dias'] = array_values(array_intersect($dados['dias'], ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado']));
            $dados['horarios'] = array_values(array_intersect($dados['horarios'], ['matutino', 'vespertino', 'noturno']));
            if (!$dados['nome'] || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || !$dados['telefone'] || !$dados['cargo'] || !$dados['dias'] || !$dados['horarios']) {
                http_response_code(422);
                exit('Preencha corretamente os campos obrigatórios.');
            }
            if (!in_array($dados['perfil'], $perfisValidos, true) || !in_array($dados['modalidade'], ['presencial', 'remoto', 'hibrido'], true)) {
                http_response_code(422);
                exit('Perfil ou modalidade inválidos.');
            }

            try {
                equipe::atualizarEquipe($id, $dados);
            } catch (\PDOException $erro) {
                http_response_code(422);
                if ((int) ($erro->errorInfo[1] ?? 0) === 1062) exit('E-mail ou telefone já cadastrado.');
                error_log('Falha ao atualizar membro da equipe: ' . $erro->getMessage());
                exit('Não foi possível atualizar os dados da equipe.');
            } catch (\Throwable $erro) {
                http_response_code(500);
                error_log('Falha ao atualizar membro da equipe: ' . $erro->getMessage());
                exit('Não foi possível atualizar os dados da equipe. ' . $erro->getMessage());
            }
            $_SESSION['equipeSalva'] = true;
            if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['sucesso' => true, 'redirect' => BASE_URL . 'equipe/index']);
                return;
            }
            $this->redirecionar('/equipe/index');
        }

        private function cpfInvalido(string $cpf): bool
        {
            if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return true;
            for ($tamanho = 9; $tamanho < 11; $tamanho++) {
                $soma = 0;
                for ($i = 0; $i < $tamanho; $i++) $soma += (int) $cpf[$i] * (($tamanho + 1) - $i);
                $digito = ($soma * 10) % 11;
                if ($digito === 10) $digito = 0;
                if ((int) $cpf[$tamanho] !== $digito) return true;
            }
            return false;
        }
    }

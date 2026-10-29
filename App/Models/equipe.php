<?php

namespace Models;

use Core\bancoDados;

class equipe
{
    public static function cpfExiste(string $cpf, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT 1 FROM usuario WHERE cpf = :cpf';
        if ($ignorarId !== null) $sql .= ' AND id <> :id';
        $sql .= ' LIMIT 1';
        $stmt = bancoDados::conectar()->prepare($sql);
        $parametros = ['cpf' => preg_replace('/\D/', '', $cpf)];
        if ($ignorarId !== null) $parametros['id'] = $ignorarId;
        $stmt->execute($parametros);
        return (bool) $stmt->fetchColumn();
    }

    public static function emailExiste(string $email, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT 1 FROM usuario WHERE email = :email';
        if ($ignorarId !== null) $sql .= ' AND id <> :id';
        $sql .= ' LIMIT 1';
        $stmt = bancoDados::conectar()->prepare($sql);
        $parametros = ['email' => trim($email)];
        if ($ignorarId !== null) $parametros['id'] = $ignorarId;
        $stmt->execute($parametros);
        return (bool) $stmt->fetchColumn();
    }

    public static function telefoneExiste(string $telefone, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT 1 FROM usuario WHERE telefone = :telefone';
        if ($ignorarId !== null) $sql .= ' AND id <> :id';
        $sql .= ' LIMIT 1';
        $stmt = bancoDados::conectar()->prepare($sql);
        $parametros = ['telefone' => preg_replace('/\D/', '', $telefone)];
        if ($ignorarId !== null) $parametros['id'] = $ignorarId;
        $stmt->execute($parametros);
        return (bool) $stmt->fetchColumn();
    }

    public static function listarPerfis(): array
    {
        return bancoDados::conectar()->query('SELECT id, nome FROM perfil ORDER BY nome')->fetchAll();
    }

    public static function cadastrarPerfil(string $nome, array $permissoes): int
    {
        $catalogo = [
            'dashboard_adm' => 'visualizar dashboard adm',
            'dashboard_startup' => 'visualizar dashboard startup',
            'dashboard_mentores' => 'visualizar dashboard mentores',
            'area_startup' => 'visualizar área da startup',
            'area_mentores' => 'visualizar área mentores',
            'relatos' => 'visualizar relatos',
            'area_parceiros' => 'visualizar área parceiros',
            'area_staff' => 'visualizar área staff',
            'gerenciar_documentos' => 'gerenciar documentos',
            'gerenciar_parceiros' => 'gerenciar parceiros',
            'gerenciar_questionarios' => 'gerenciar questionários',
            'validar_juncao_startup_mentor' => 'validar junção startup x mentor',
            'validar_reunioes' => 'validar reuniões',
            'relatar_reunioes' => 'relatar reuniões',
            'agendar_reunioes' => 'agendar reuniões',
            'cadastrar_perfis' => 'cadastrar perfis',
            'realizar_questionario' => 'realizar questionário',
            'receber_notificacoes' => 'receber notificações'
        ];

        $db = bancoDados::conectar();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare('INSERT INTO perfil (nome) VALUES (:nome)');
            $stmt->execute(['nome' => $nome]);
            $idPerfil = (int) $db->lastInsertId();

            $chaves = array_values(array_unique(array_map('strval', $permissoes)));
            $idsPermissao = [];
            foreach ($chaves as $chave) {
                if (!isset($catalogo[$chave])) {
                    throw new \InvalidArgumentException('Uma ou mais permissões selecionadas são inválidas.');
                }

                $nomePermissao = $catalogo[$chave];
                $stmt = $db->prepare('SELECT id FROM permissao WHERE nome = :nome ORDER BY id LIMIT 1');
                $stmt->execute(['nome' => $nomePermissao]);
                $idPermissao = $stmt->fetchColumn();

                if ($idPermissao === false) {
                    $stmt = $db->prepare('INSERT INTO permissao (nome, descricao, ativa) VALUES (:nome, :descricao, 1)');
                    $stmt->execute([
                        'nome' => $nomePermissao,
                        'descricao' => 'Permissão de acesso: ' . $nomePermissao
                    ]);
                    $idPermissao = $db->lastInsertId();
                }

                $idsPermissao[] = (int) $idPermissao;
            }

            if ($idsPermissao) {
                $stmt = $db->prepare('INSERT INTO perfil_permissao (id_perfil, id_permissao) VALUES (:perfil, :permissao)');
                foreach ($idsPermissao as $idPermissao) {
                    $stmt->execute(['perfil' => $idPerfil, 'permissao' => $idPermissao]);
                }
            }

            $db->commit();
            return $idPerfil;
        } catch (\Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $erro;
        }
    }

    public static function cadastrarEquipe(array $dados): int
    {
        $db = bancoDados::conectar();
        $etapa = 'iniciar transação';
        try {
            $db->beginTransaction();

            $etapa = 'salvar dias disponíveis';
            $stmt = $db->prepare('INSERT INTO semana (domingo, segunda, terca, quarta, quinta, sexta, sabado) VALUES (0, :segunda, :terca, :quarta, :quinta, :sexta, :sabado)');
            foreach (['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'] as $dia) {
                $stmt->bindValue(':' . $dia, in_array($dia, $dados['dias'], true) ? 1 : 0, \PDO::PARAM_INT);
            }
            $stmt->execute();
            $idSemana = (int) $db->lastInsertId();

            $etapa = 'salvar horários disponíveis';
            $stmt = $db->prepare('INSERT INTO horario (matutino, vespertino, noturno) VALUES (:matutino, :vespertino, :noturno)');
            foreach (['matutino', 'vespertino', 'noturno'] as $horario) {
                $stmt->bindValue(':' . $horario, in_array($horario, $dados['horarios'], true) ? 1 : 0, \PDO::PARAM_INT);
            }
            $stmt->execute();
            $idHorario = (int) $db->lastInsertId();

            $etapa = 'salvar modalidade';
            $stmt = $db->prepare('INSERT INTO disponibilidade (id_semana, id_horario, modalidade) VALUES (:semana, :horario, :modalidade)');
            $stmt->execute(['semana' => $idSemana, 'horario' => $idHorario, 'modalidade' => strtoupper($dados['modalidade'])]);
            $idDisponibilidade = (int) $db->lastInsertId();

            $etapa = 'salvar dados do usuário';
            $stmt = $db->prepare('INSERT INTO usuario (nome, cpf, email, ativo, id_perfil, telefone) VALUES (:nome, :cpf, :email, 1, :perfil, :telefone)');
            $stmt->execute(['nome' => $dados['nome'], 'cpf' => $dados['cpf'], 'email' => $dados['email'], 'perfil' => $dados['perfil'], 'telefone' => $dados['telefone']]);
            $idUsuario = (int) $db->lastInsertId();

            $etapa = 'vincular usuário à equipe';
            $stmt = $db->prepare('INSERT INTO equipe (id_usuario, cargo, id_disponibilidade) VALUES (:usuario, :cargo, :disponibilidade)');
            $stmt->execute(['usuario' => $idUsuario, 'cargo' => $dados['cargo'], 'disponibilidade' => $idDisponibilidade]);

            $db->commit();
            return $idUsuario;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($e instanceof \PDOException && (int) ($e->errorInfo[1] ?? 0) !== 1062) {
                $detalhe = $e->errorInfo[2] ?? $e->getMessage();
                throw new \RuntimeException(
                    sprintf('Etapa: %s. SQLSTATE %s, código MySQL %s: %s', $etapa, $e->getCode(), $e->errorInfo[1] ?? 'indisponível', $detalhe),
                    0,
                    $e
                );
            }
            throw $e;
        }
    }

    public static function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT u.id, u.nome, u.cpf, u.email, u.telefone, u.id_perfil AS perfil,
                       e.cargo, d.modalidade,
                       CAST(s.segunda AS UNSIGNED) AS segunda,
                       CAST(s.terca AS UNSIGNED) AS terca,
                       CAST(s.quarta AS UNSIGNED) AS quarta,
                       CAST(s.quinta AS UNSIGNED) AS quinta,
                       CAST(s.sexta AS UNSIGNED) AS sexta,
                       CAST(s.sabado AS UNSIGNED) AS sabado,
                       CAST(h.matutino AS UNSIGNED) AS matutino,
                       CAST(h.vespertino AS UNSIGNED) AS vespertino,
                       CAST(h.noturno AS UNSIGNED) AS noturno
                FROM equipe e
                JOIN usuario u ON u.id = e.id_usuario
                JOIN disponibilidade d ON d.id = e.id_disponibilidade
                JOIN semana s ON s.id = d.id_semana
                JOIN horario h ON h.id = d.id_horario
                WHERE u.id = :id LIMIT 1';
        $stmt = bancoDados::conectar()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $membro = $stmt->fetch();
        return $membro ?: null;
    }

    public static function atualizarEquipe(int $id, array $dados): void
    {
        $db = bancoDados::conectar();
        $etapa = 'localizar disponibilidade';
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('SELECT id_disponibilidade FROM equipe WHERE id_usuario = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $relacoes = $stmt->fetch();
            if (!$relacoes) throw new \RuntimeException('Membro da equipe não encontrado.');

            $etapa = 'salvar novos dias disponíveis';
            $stmt = $db->prepare('INSERT INTO semana (domingo, segunda, terca, quarta, quinta, sexta, sabado)
                                  VALUES (0, :segunda, :terca, :quarta, :quinta, :sexta, :sabado)');
            foreach (['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'] as $dia) {
                $stmt->bindValue(':' . $dia, in_array($dia, $dados['dias'], true) ? 1 : 0, \PDO::PARAM_INT);
            }
            $stmt->execute();
            $idSemana = (int) $db->lastInsertId();

            $etapa = 'salvar novos horários disponíveis';
            $stmt = $db->prepare('INSERT INTO horario (matutino, vespertino, noturno)
                                  VALUES (:matutino, :vespertino, :noturno)');
            foreach (['matutino', 'vespertino', 'noturno'] as $horario) {
                $stmt->bindValue(':' . $horario, in_array($horario, $dados['horarios'], true) ? 1 : 0, \PDO::PARAM_INT);
            }
            $stmt->execute();
            $idHorario = (int) $db->lastInsertId();

            $etapa = 'salvar nova disponibilidade';
            $stmt = $db->prepare('INSERT INTO disponibilidade (id_semana, id_horario, modalidade)
                                  VALUES (:semana, :horario, :modalidade)');
            $stmt->execute(['semana' => $idSemana, 'horario' => $idHorario, 'modalidade' => strtoupper($dados['modalidade'])]);
            $idDisponibilidade = (int) $db->lastInsertId();

            $etapa = 'atualizar dados do usuário';
            $stmt = $db->prepare('UPDATE usuario SET nome = :nome, cpf = :cpf, email = :email,
                                  id_perfil = :perfil, telefone = :telefone WHERE id = :id');
            $stmt->execute([
                'nome' => $dados['nome'], 'cpf' => $dados['cpf'], 'email' => $dados['email'],
                'perfil' => $dados['perfil'], 'telefone' => $dados['telefone'], 'id' => $id
            ]);

            $etapa = 'atualizar cargo';
            $stmt = $db->prepare('UPDATE equipe SET cargo = :cargo, id_disponibilidade = :disponibilidade WHERE id_usuario = :id');
            $stmt->execute(['cargo' => $dados['cargo'], 'disponibilidade' => $idDisponibilidade, 'id' => $id]);

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($e instanceof \PDOException && (int) ($e->errorInfo[1] ?? 0) !== 1062) {
                $detalhe = $e->errorInfo[2] ?? $e->getMessage();
                throw new \RuntimeException(sprintf('Etapa: %s. SQLSTATE %s, código MySQL %s: %s', $etapa, $e->getCode(), $e->errorInfo[1] ?? 'indisponível', $detalhe), 0, $e);
            }
            throw $e;
        }
    }

    public static function alterarStatus(int $id, bool $ativo): bool
    {
        $stmt = bancoDados::conectar()->prepare('UPDATE usuario SET ativo = :ativo WHERE id = :id AND id IN (SELECT id_usuario FROM equipe)');
        $stmt->bindValue(':ativo', $ativo ? 1 : 0, \PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function listar(): array
    {
        $sql = 'SELECT u.id, u.nome, u.email, u.telefone, u.cpf, CAST(u.ativo AS UNSIGNED) AS ativo, e.cargo, p.nome AS perfil
                FROM equipe e JOIN usuario u ON u.id = e.id_usuario JOIN perfil p ON p.id = u.id_perfil ORDER BY u.nome';
        $itens = bancoDados::conectar()->query($sql)->fetchAll();
        foreach ($itens as &$item) {
            $telefone = preg_replace('/\D/', '', (string) $item['telefone']);
            $cpf = preg_replace('/\D/', '', (string) $item['cpf']);
            if (strlen($telefone) === 11) $item['telefone'] = sprintf('(%s) %s-%s', substr($telefone, 0, 2), substr($telefone, 2, 5), substr($telefone, 7));
            elseif (strlen($telefone) === 10) $item['telefone'] = sprintf('(%s) %s-%s', substr($telefone, 0, 2), substr($telefone, 2, 4), substr($telefone, 6));
            if (strlen($cpf) === 11) $item['cpf'] = sprintf('%s.%s.%s-%s', substr($cpf, 0, 3), substr($cpf, 3, 3), substr($cpf, 6, 3), substr($cpf, 9, 2));
        }
        unset($item);
        return $itens;
    }
}

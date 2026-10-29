<?php

namespace Models;

use Core\bancoDados;
use PDO;

class startups
{
    public static function campoEmUso(string $campo, string $valor, int $ignorarId = 0, int $startupId = 0): bool
    {
        $pdo = bancoDados::conectar();
        if ($campo === 'startupName' || $campo === 'cnpj') {
            $coluna = $campo === 'startupName' ? 'nome' : 'cnpj';
            $stmt = $pdo->prepare("SELECT 1 FROM startup WHERE {$coluna} = :valor AND id <> :id LIMIT 1");
            $stmt->execute(['valor' => $valor, 'id' => $ignorarId]);
            return (bool) $stmt->fetchColumn();
        }

        if ($campo === 'founderEmail') {
            $stmt = $pdo->prepare('
                SELECT 1 FROM usuario u
                WHERE u.email = :valor
                  AND u.id <> COALESCE((
                    SELECT f.id_usuario FROM fundador f
                    WHERE f.id = :fundador AND f.id_startup = :startup
                    LIMIT 1
                  ), 0)
                LIMIT 1
            ');
            $stmt->execute([
                'valor' => $valor,
                'fundador' => $ignorarId,
                'startup' => $startupId,
            ]);
            return (bool) $stmt->fetchColumn();
        }

        throw new \InvalidArgumentException('Campo de validação inválido.');
    }

    public static function listar(): array
    {
        $pdo  = bancoDados::conectar();
        $stmt = $pdo->query('
            SELECT id, nome, cnpj, setor_atuacao, cidade, uf, trl,
                   ativo + 0 AS ativo
            FROM startup
            ORDER BY nome ASC
        ');

        return $stmt->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $pdo  = bancoDados::conectar();
        $stmt = $pdo->prepare('SELECT * FROM startup WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $startup = $stmt->fetch();

        if (!$startup) return null;

        $fundadores = $pdo->prepare('
            SELECT f.id, f.id_usuario, f.funcao, f.temp_empresa,
                   u.nome, u.email, u.telefone
            FROM fundador f
            JOIN usuario u ON u.id = f.id_usuario
            WHERE f.id_startup = :id
            ORDER BY f.id
        ');
        $fundadores->execute(['id' => $id]);
        $startup['fundadores'] = $fundadores->fetchAll();
        return $startup;
    }

    public static function cadastrar(array $dados, array $fundadores = []): int
    {
        $pdo = bancoDados::conectar();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO startup (
                    nome, cnpj, data_fundacao, trl, setor_atuacao,
                    site, instagram, linkedin, cep, logradouro, numero,
                    complemento, bairro, uf, cidade, logo, outras_participacoes
                ) VALUES (
                    :nome, :cnpj, :data_fundacao, :trl, :setor_atuacao,
                    :site, :instagram, :linkedin, :cep, :logradouro, :numero,
                    :complemento, :bairro, :uf, :cidade, :logo, :outras_participacoes
                )
            ');

            $stmt->execute([
                'nome'                 => $dados['nome'],
                'cnpj'                 => $dados['cnpj'],
                'data_fundacao'        => $dados['data_fundacao'],
                'trl'                  => $dados['trl'],
                'setor_atuacao'        => $dados['setor_atuacao'],
                'site'                 => $dados['site'],
                'instagram'            => $dados['instagram'],
                'linkedin'             => $dados['linkedin'],
                'cep'                  => $dados['cep'],
                'logradouro'           => $dados['logradouro'],
                'numero'               => $dados['numero'],
                'complemento'          => $dados['complemento'],
                'bairro'               => $dados['bairro'],
                'uf'                   => $dados['uf'],
                'cidade'               => $dados['cidade'],
                'logo'                 => $dados['logo'],
                'outras_participacoes' => $dados['outras_participacoes'],
            ]);
            $idStartup = (int) $pdo->lastInsertId();

            if ($fundadores) {
                $perfil = $pdo->query("SELECT id FROM perfil WHERE nome = 'fundador' LIMIT 1")->fetchColumn();
                if (!$perfil) {
                    throw new \RuntimeException('O perfil de fundador não está cadastrado.');
                }
                $usuarioStmt = $pdo->prepare('INSERT INTO usuario (nome, email, id_perfil, telefone) VALUES (:nome, :email, :perfil, :telefone)');
                $fundadorStmt = $pdo->prepare('INSERT INTO fundador (id_startup, id_usuario, funcao, temp_empresa) VALUES (:startup, :usuario, :funcao, :entrada)');
                $emailStmt = $pdo->prepare('SELECT id FROM usuario WHERE email = :email LIMIT 1');

                foreach ($fundadores as $fundador) {
                    $emailStmt->execute(['email' => $fundador['email']]);
                    $emailJaCadastrado = $emailStmt->fetchColumn();
                    $emailStmt->closeCursor();
                    if ($emailJaCadastrado) {
                        throw new \RuntimeException('O e-mail de um fundador já está cadastrado.');
                    }
                    $usuarioStmt->execute([
                        'nome' => $fundador['nome'],
                        'email' => $fundador['email'],
                        'perfil' => $perfil,
                        'telefone' => $fundador['telefone'] === '' ? null : $fundador['telefone'],
                    ]);
                    $fundadorStmt->execute([
                        'startup' => $idStartup,
                        'usuario' => (int) $pdo->lastInsertId(),
                        'funcao' => $fundador['funcao'],
                        'entrada' => $fundador['temp_empresa'],
                    ]);
                }
            }

            $pdo->commit();
            return $idStartup;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function atualizar(int $id, array $dados, array $fundadores): bool
    {
        $pdo = bancoDados::conectar();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                UPDATE startup SET
                    nome = :nome, cnpj = :cnpj, data_fundacao = :data_fundacao,
                    trl = :trl, setor_atuacao = :setor_atuacao, site = :site,
                    instagram = :instagram, linkedin = :linkedin, cep = :cep,
                    logradouro = :logradouro, numero = :numero,
                    complemento = :complemento, bairro = :bairro, uf = :uf,
                    cidade = :cidade, logo = :logo,
                    outras_participacoes = :outras_participacoes
                WHERE id = :id
            ');
            $stmt->execute([
                'nome' => $dados['nome'],
                'cnpj' => $dados['cnpj'],
                'data_fundacao' => $dados['data_fundacao'],
                'trl' => $dados['trl'],
                'setor_atuacao' => $dados['setor_atuacao'],
                'site' => $dados['site'],
                'instagram' => $dados['instagram'],
                'linkedin' => $dados['linkedin'],
                'cep' => $dados['cep'],
                'logradouro' => $dados['logradouro'],
                'numero' => $dados['numero'],
                'complemento' => $dados['complemento'],
                'bairro' => $dados['bairro'],
                'uf' => $dados['uf'],
                'cidade' => $dados['cidade'],
                'logo' => $dados['logo'],
                'outras_participacoes' => $dados['outras_participacoes'],
                'id' => $id,
            ]);

            $atuais = $pdo->prepare('SELECT id, id_usuario FROM fundador WHERE id_startup = :id');
            $atuais->execute(['id' => $id]);
            $vinculos = [];
            foreach ($atuais->fetchAll() as $atual) {
                $vinculos[(int) $atual['id']] = (int) $atual['id_usuario'];
            }

            $perfilFundador = null;
            $novoUsuario = $pdo->prepare('INSERT INTO usuario (nome, email, id_perfil, telefone) VALUES (:nome, :email, :perfil, :telefone)');
            $novoFundador = $pdo->prepare('INSERT INTO fundador (id_startup, id_usuario, funcao, temp_empresa) VALUES (:startup, :usuario, :funcao, :entrada)');
            $editarUsuario = $pdo->prepare('UPDATE usuario SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id');
            $editarFundador = $pdo->prepare('UPDATE fundador SET funcao = :funcao, temp_empresa = :entrada WHERE id = :id');

            foreach ($fundadores as $fundador) {
                $idFundador = $fundador['id'];
                $telefone = $fundador['telefone'] === '' ? null : $fundador['telefone'];
                if ($idFundador > 0) {
                    if (!isset($vinculos[$idFundador])) {
                        throw new \RuntimeException('Fundador não pertence a esta startup.');
                    }
                    $editarUsuario->execute([
                        'nome' => $fundador['nome'], 'email' => $fundador['email'],
                        'telefone' => $telefone, 'id' => $vinculos[$idFundador],
                    ]);
                    $editarFundador->execute([
                        'funcao' => $fundador['funcao'], 'entrada' => $fundador['temp_empresa'],
                        'id' => $idFundador,
                    ]);
                    unset($vinculos[$idFundador]);
                    continue;
                }

                if ($perfilFundador === null) {
                    $perfilFundador = $pdo->query("SELECT id FROM perfil WHERE nome = 'fundador' LIMIT 1")->fetchColumn();
                    if (!$perfilFundador) throw new \RuntimeException('O perfil de fundador não está cadastrado.');
                }
                $novoUsuario->execute([
                    'nome' => $fundador['nome'], 'email' => $fundador['email'],
                    'perfil' => $perfilFundador, 'telefone' => $telefone,
                ]);
                $novoFundador->execute([
                    'startup' => $id, 'usuario' => (int) $pdo->lastInsertId(),
                    'funcao' => $fundador['funcao'], 'entrada' => $fundador['temp_empresa'],
                ]);
            }

            $removerFundador = $pdo->prepare('DELETE FROM fundador WHERE id = :id');
            foreach (array_keys($vinculos) as $idFundador) {
                $removerFundador->execute(['id' => $idFundador]);
            }

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function alterarStatus(int $id, bool $ativo): bool
    {
        $pdo  = bancoDados::conectar();
        $stmt = $pdo->prepare('UPDATE startup SET ativo = :ativo WHERE id = :id');
        $stmt->bindValue(':ativo', $ativo ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) return true;

        $existe = $pdo->prepare('SELECT 1 FROM startup WHERE id = :id');
        $existe->bindValue(':id', $id, PDO::PARAM_INT);
        $existe->execute();
        return (bool) $existe->fetchColumn();
    }
}

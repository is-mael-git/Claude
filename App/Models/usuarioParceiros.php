<?php

namespace Models;

use Core\bancoDados;
use PDO;

class usuarioParceiros
{

    public static function cadastrarParceiro($dados)
    {
        $db = bancoDados::conectar();

        try {
            $db->beginTransaction();

            $stmtParceiro = $db->prepare('INSERT INTO parceiro (foto, nome, cnpj, telefone, site, id_area_atuacao, instagram, linkedin, ativo)
            VALUES (:f, :n, :cnpj, :t, :s, :aa, :i, :l, 1)');

            $parametrosParceiro = [
                'f'     => $dados['foto'],
                'n'     => $dados['nome'],
                'cnpj'  => $dados['cnpj'],
                't'     => $dados['telefone'],
                's'     => $dados['site'],
                'aa'    => $dados['id_area_atuacao'],
                'i'     => $dados['instagram'],
                'l'     => $dados['linkedin']
            ];
            $stmtParceiro->execute($parametrosParceiro);

            $idParceiro = (int) $db->lastInsertId();

            $stmtRep = $db->prepare('INSERT INTO representante_parceiro (id_parceiro, nome, email, telefone, cargo)
            VALUES (:id_parceiro, :nome, :email, :telefone, :cargo)');

            $parametrosRepresentante = [
                'id_parceiro' => $idParceiro,
                'nome'        => $dados['nome_representante'],
                'email'       => $dados['email_representante'],
                'telefone'    => $dados['telefone_representante'],
                'cargo'       => $dados['cargo_representante']
            ];
            $stmtRep->execute($parametrosRepresentante);

            $db->commit();
            return $idParceiro;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function atualizarParceiro($dados, $id)
    {
        $db = bancoDados::conectar();

        try {
            $db->beginTransaction();

            $sql = "UPDATE parceiro SET
            foto = :f,
            nome = :n,
            cnpj = :cnpj,
            telefone = :t,
            site = :s,
            id_area_atuacao = :aa,
            instagram = :i,
            linkedin = :l
            WHERE id = :id";

            $stmt = $db->prepare($sql);
            $parametrosParceiro = [
                'id'    => $id,
                'f'     => $dados['foto'],
                'n'     => $dados['nome'],
                'cnpj'  => $dados['cnpj'],
                't'     => $dados['telefone'],
                's'     => $dados['site'],
                'aa'    => $dados['id_area_atuacao'],
                'i'     => $dados['instagram'],
                'l'     => $dados['linkedin']
            ];
            $stmt->execute($parametrosParceiro);

            $checkRep = $db->prepare('SELECT id FROM representante_parceiro WHERE id_parceiro = :id');
            $checkRep->execute(['id' => $id]);
            $repExiste = $checkRep->fetch();

            if ($repExiste) {
                $stmtRep = $db->prepare('UPDATE representante_parceiro SET
                nome = :nome,
                email = :email,
                telefone = :telefone,
                cargo = :cargo
                WHERE id_parceiro = :id_parceiro');
            } else {
                $stmtRep = $db->prepare('INSERT INTO representante_parceiro (id_parceiro, nome, email, telefone, cargo)
                VALUES (:id_parceiro, :nome, :email, :telefone, :cargo)');
            }

            $parametrosRepresentante = [
                'id_parceiro' => $id,
                'nome'        => $dados['nome_representante'],
                'email'       => $dados['email_representante'],
                'telefone'    => $dados['telefone_representante'],
                'cargo'       => $dados['cargo_representante']
            ];
            $stmtRep->execute($parametrosRepresentante);

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function alterarStatus(int $id, bool $ativo): bool
    {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("UPDATE parceiro SET ativo = :ativo WHERE id = :id");
        $stmt->bindValue(':ativo', $ativo ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function buscarPorId($id): ?array
    {
        $db = bancoDados::conectar();

        $sql = "SELECT
                    p.*,
                    rp.nome AS nome_representante,
                    rp.email AS email_representante,
                    rp.telefone AS telefone_representante,
                    rp.cargo AS cargo_representante
                FROM parceiro p
                LEFT JOIN representante_parceiro rp ON rp.id_parceiro = p.id
                WHERE p.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $parceiro = $stmt->fetch();

        return $parceiro ?: null;
    }

    public static function listar(): array
    {
        $db = bancoDados::conectar();

        $sql = "SELECT
                    p.id,
                    p.id_area_atuacao,
                    p.foto,
                    p.nome,
                    p.cnpj,
                    p.telefone,
                    p.site,
                    p.instagram,
                    p.linkedin,
                    CAST(p.ativo AS UNSIGNED) AS ativo,
                    a.nome AS area_nome,
                    rp.nome AS nome_representante,
                    rp.email AS email_representante,
                    rp.telefone AS telefone_representante,
                    rp.cargo AS cargo_representante
                FROM parceiro p
                INNER JOIN area_atuacao a ON a.id = p.id_area_atuacao
                LEFT JOIN representante_parceiro rp ON rp.id_parceiro = p.id";

        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }


    public static function cadastrarArea(string $nome): int
    {
        $db = bancoDados::conectar();

        $stmt = $db->prepare("SELECT id FROM area_atuacao WHERE nome = :nome");
        $stmt->execute(['nome' => $nome]);
        $area = $stmt->fetch();

        if ($area) {
            return (int) $area['id'];
        }

        $stmt = $db->prepare("INSERT INTO area_atuacao (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $nome]);

        return (int) $db->lastInsertId();
    }

    public static function listarAreas(): array
    {
        $db = bancoDados::conectar();

        $stmt = $db->query("SELECT id, nome FROM area_atuacao ORDER BY nome");

        return $stmt->fetchAll();
    }
}

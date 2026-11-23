<?php

namespace Models;

use Core\bancoDados;
use Core\model;
use Core\select;
use PDO;

class usuarioParceiros extends model
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
        return self::selecionarUm("SELECT * FROM vw_parceiros_detalhes WHERE id = :id", ['id' => $id]);
    }

    /**
     * Prepara a consulta base em vw_parceiros_detalhes com filtro opcional de área.
     *
     * @param int|string|array|null $area ID(s) ou nome(s) das áreas de atuação.
     */
    private static function montarConsultaArea($area): select
    {
        $consulta = self::select('vw_parceiros_detalhes');

        if (empty($area)) {
            return $consulta;
        }

        $areasArray = is_array($area) ? $area : explode(',', (string) $area);
        $areasArray = array_filter(array_map('trim', $areasArray), fn($v) => $v !== '');

        if (empty($areasArray)) {
            return $consulta;
        }

        $ehNumerico = true;
        foreach ($areasArray as $item) {
            if (!ctype_digit((string) $item)) {
                $ehNumerico = false;
                break;
            }
        }

        $colunaFiltro = $ehNumerico ? 'id_area_atuacao' : 'area_nome';
        $valores = $ehNumerico
            ? array_map('intval', $areasArray)
            : array_values($areasArray);

        return $consulta->whereIn($colunaFiltro, $valores, 'area');
    }

    /**
     * Conta o total de parceiros distintos conforme o filtro de área aplicado.
     *
     * @param int|string|array|null $area ID(s) ou nome(s) das áreas de atuação para filtrar.
     * @return int Total de registros encontrados.
     */
    public static function contar($area = null): int
    {
        return self::montarConsultaArea($area)->contar('DISTINCT id');
    }

    /**
     * Lista os parceiros a partir da view vw_parceiros_detalhes com suporte a ordenação,
     * filtro por área (única ou múltipla) e paginação opcional.
     *
     * @param string $orderBy Expressão SQL de ordenação (ex: "nome ASC"). Padrão: "id DESC".
     * @param int|string|array|null $area ID(s) ou nome(s) das áreas de atuação para filtrar (ex: 1, "1,2", ["1", "2"] ou "Tecnologia").
     * @param int|null $limit Quantidade máxima de registros a retornar (ou null para listar todos).
     * @param int|null $offset Deslocamento inicial de registros (offset de paginação).
     * @return array Lista de registros associativos retornados do banco.
     */
    public static function listar(string $orderBy = "id DESC", $area = null, ?int $limit = null, ?int $offset = null): array
    {
        return self::montarConsultaArea($area)
            ->distinct()
            ->orderBy($orderBy)
            ->limit($limit, $offset)
            ->buscarTodos();
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
        return self::selecionarTodos("SELECT id, nome FROM area_atuacao ORDER BY nome");
    }
}

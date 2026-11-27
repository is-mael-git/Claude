<?php

namespace Models;

use Core\model;
use Core\select;
use Throwable;

class usuarioParceiros extends model
{

    public static function cadastrarParceiro(array $dados): int
    {
        self::iniciarTransacao();

        try {
            $idParceiro = self::inserir('parceiro', [
                'foto'            => $dados['foto'],
                'nome'            => $dados['nome'],
                'cnpj'            => $dados['cnpj'],
                'telefone'        => $dados['telefone'],
                'site'            => $dados['site'],
                'id_area_atuacao' => $dados['id_area_atuacao'],
                'instagram'       => $dados['instagram'],
                'linkedin'        => $dados['linkedin'],
                'ativo'           => 1,
            ]);

            self::inserir('representante_parceiro', [
                'id_parceiro' => $idParceiro,
                'nome'        => $dados['nome_representante'],
                'email'       => $dados['email_representante'],
                'telefone'    => $dados['telefone_representante'],
                'cargo'       => $dados['cargo_representante'],
            ]);

            self::confirmarTransacao();
            return $idParceiro;
        } catch (Throwable $e) {
            self::cancelarTransacao();
            throw $e;
        }
    }

    public static function atualizarParceiro(array $dados, int $id): bool
    {
        self::iniciarTransacao();

        try {
            self::atualizarPorId('parceiro', $id, [
                'foto'            => $dados['foto'],
                'nome'            => $dados['nome'],
                'cnpj'            => $dados['cnpj'],
                'telefone'        => $dados['telefone'],
                'site'            => $dados['site'],
                'id_area_atuacao' => $dados['id_area_atuacao'],
                'instagram'       => $dados['instagram'],
                'linkedin'        => $dados['linkedin'],
            ]);

            $dadosRepresentante = [
                'nome'     => $dados['nome_representante'],
                'email'    => $dados['email_representante'],
                'telefone' => $dados['telefone_representante'],
                'cargo'    => $dados['cargo_representante'],
            ];

            if (self::existe('representante_parceiro', ['id_parceiro' => $id])) {
                self::atualizarPorId('representante_parceiro', $id, $dadosRepresentante, 'id_parceiro');
            } else {
                self::inserir('representante_parceiro', ['id_parceiro' => $id] + $dadosRepresentante);
            }

            self::confirmarTransacao();
            return true;
        } catch (Throwable $e) {
            self::cancelarTransacao();
            throw $e;
        }
    }

    public static function alterarStatus(int $id, bool $ativo): bool
    {
        return self::atualizarPorId('parceiro', $id, ['ativo' => $ativo]);
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
        $idExistente = self::selecionarValor(
            'SELECT id FROM area_atuacao WHERE nome = :nome LIMIT 1',
            ['nome' => $nome]
        );

        if ($idExistente !== false) {
            return (int) $idExistente;
        }

        return self::inserir('area_atuacao', ['nome' => $nome]);
    }

    public static function listarAreas(): array
    {
        return self::selecionarTodos("SELECT id, nome FROM area_atuacao ORDER BY nome");
    }
}

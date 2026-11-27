<?php

namespace Models;

use Core\model;
use Core\select;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class startups extends model
{
    /**
     * Verifica se nome, CNPJ ou e-mail de fundador já está em uso.
     *
     * @param string $campo Nome do campo ('startupName', 'cnpj' ou 'founderEmail').
     * @param string $valor Valor a verificar.
     * @param int $ignorarId ID da startup (ou do fundador, em founderEmail) a ignorar na edição.
     * @param int $startupId ID da startup ao validar e-mail de fundador em edição.
     * @return bool True se o valor já estiver em uso por outro registro.
     */
    public static function campoEmUso(string $campo, string $valor, int $ignorarId = 0, int $startupId = 0): bool
    {
        if ($campo === 'startupName' || $campo === 'cnpj') {
            $coluna = $campo === 'startupName' ? 'nome' : 'cnpj';
            return self::existe('startup', [$coluna => $valor], $ignorarId > 0 ? $ignorarId : null);
        }

        if ($campo === 'founderEmail') {
            return (bool) self::selecionarValor(
                'SELECT 1 FROM usuario u
                 WHERE u.email = :valor
                   AND u.id <> COALESCE((
                     SELECT f.id_usuario FROM fundador f
                     WHERE f.id = :fundador AND f.id_startup = :startup
                     LIMIT 1
                   ), 0)
                 LIMIT 1',
                [
                    'valor' => $valor,
                    'fundador' => $ignorarId,
                    'startup' => $startupId,
                ]
            );
        }

        throw new InvalidArgumentException('Campo de validação inválido.');
    }

    /**
     * Prepara a consulta base em vw_startups_detalhes com filtro opcional por setor/área de atuação.
     *
     * @param int|string|array|null $setor ID(s) ou nome(s) do setor/área de atuação (ex: 1, "1,2" ou "Tecnologia,Saúde").
     * @return select
     */
    private static function montarConsultaSetor($setor): select
    {
        $consulta = self::select('vw_startups_detalhes');

        if (empty($setor)) {
            return $consulta;
        }

        $setoresArray = is_array($setor) ? $setor : explode(',', (string) $setor);
        $setoresArray = array_values(array_filter(array_map('trim', $setoresArray), fn($v) => $v !== ''));

        if (empty($setoresArray)) {
            return $consulta;
        }

        $ehNumerico = true;
        foreach ($setoresArray as $item) {
            if (!ctype_digit((string) $item)) {
                $ehNumerico = false;
                break;
            }
        }

        $colunaFiltro = $ehNumerico ? 'id_area_atuacao' : 'setor_atuacao';
        $valores = $ehNumerico
            ? array_map('intval', $setoresArray)
            : $setoresArray;

        return $consulta->whereIn($colunaFiltro, $valores, 'setor');
    }

    /**
     * Conta o total de startups distintas conforme o filtro de setor/área aplicado.
     *
     * @param int|string|array|null $setor ID(s) ou nome(s) do setor/área de atuação para filtrar.
     * @return int Total de registros encontrados.
     */
    public static function contar($setor = null): int
    {
        return self::montarConsultaSetor($setor)->contar('DISTINCT id');
    }

    /**
     * Lista startups a partir da view vw_startups_detalhes com ordenação, filtro e paginação no servidor.
     *
     * @param string $orderBy Expressão SQL de ordenação (ex: "nome ASC").
     * @param int|string|array|null $setor ID(s) ou nome(s) do setor/área de atuação para filtrar.
     * @param int|null $limit Quantidade máxima de registros a retornar.
     * @param int|null $offset Deslocamento inicial de registros.
     * @return array Lista de registros associativos retornados do banco.
     */
    public static function listar(string $orderBy = 'nome ASC', $setor = null, ?int $limit = null, ?int $offset = null): array
    {
        return self::montarConsultaSetor($setor)
            ->colunas()
            ->distinct()
            ->orderBy($orderBy)
            ->limit($limit, $offset)
            ->buscarTodos();
    }

    /**
     * Lista startups ativas para a tela de cards com e-mail do primeiro fundador vinculado.
     *
     * @return array Lista de startups prontas para renderização em cardStartup.php.
     */
    public static function listarCards(): array
    {
        return self::selecionarTodos(
            'SELECT s.id, s.nome, s.logo, s.setor_atuacao, s.linkedin, s.instagram, s.ativo,
                    COALESCE((
                        SELECT vf.email
                        FROM vw_startups_fundadores vf
                        WHERE vf.id_startup = s.id
                        ORDER BY vf.id ASC
                        LIMIT 1
                    ), \'\') AS email_representante
             FROM vw_startups_detalhes s
             WHERE s.ativo = 1
             ORDER BY s.nome ASC'
        );
    }

    /**
     * Busca uma startup pelo ID na view vw_startups_detalhes junto com seus fundadores em vw_startups_fundadores.
     *
     * @param int $id ID da startup.
     * @return array|null Dados da startup com chave 'fundadores', ou null se não encontrada.
     */
    public static function buscarPorId(int $id): ?array
    {
        $startup = self::selecionarUm('SELECT * FROM vw_startups_detalhes WHERE id = :id', ['id' => $id]);

        if (!$startup) {
            return null;
        }

        $startup['fundadores'] = self::selecionarTodos(
            'SELECT id, id_usuario, funcao, nome, email, telefone
             FROM vw_startups_fundadores
             WHERE id_startup = :id
             ORDER BY id',
            ['id' => $id]
        );

        return $startup;
    }

    /**
     * Busca o ID de uma área de atuação pelo nome ou cadastra uma nova caso ainda não exista.
     *
     * @param string $nome Nome da área/setor de atuação.
     * @return int ID da área de atuação na tabela area_atuacao.
     */
    private static function resolverAreaAtuacao(string $nome): int
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

    /**
     * Cadastra uma startup e seus fundadores de forma atômica.
     *
     * @param array $dados Dados validados da startup.
     * @param array $fundadores Lista de fundadores vinculados.
     * @return int ID da startup cadastrada.
     */
    public static function cadastrar(array $dados, array $fundadores = []): int
    {
        self::iniciarTransacao();

        try {
            $idAreaAtuacao = isset($dados['id_area_atuacao']) && (int) $dados['id_area_atuacao'] > 0
                ? (int) $dados['id_area_atuacao']
                : self::resolverAreaAtuacao((string) ($dados['setor_atuacao'] ?? ''));

            $idStartup = self::inserir('startup', [
                'nome'                 => $dados['nome'],
                'biografia'            => $dados['biografia'],
                'cnpj'                 => $dados['cnpj'],
                'data_fundacao'        => $dados['data_fundacao'],
                'id_area_atuacao'      => $idAreaAtuacao,
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
                'ativo'                => 1,
            ]);

            if ($fundadores) {
                $perfil = self::selecionarValor("SELECT id FROM perfil WHERE nome = 'fundador' LIMIT 1");
                if (!$perfil) {
                    throw new RuntimeException('O perfil de fundador não está cadastrado.');
                }

                foreach ($fundadores as $fundador) {
                    if (self::existe('usuario', ['email' => $fundador['email']])) {
                        throw new RuntimeException('O e-mail de um fundador já está cadastrado.');
                    }

                    $idUsuario = self::inserir('usuario', [
                        'nome'     => $fundador['nome'],
                        'email'    => $fundador['email'],
                        'telefone' => $fundador['telefone'] === '' ? null : $fundador['telefone'],
                        'ativo'    => 1,
                    ]);

                    self::inserir('usuario_perfil', [
                        'id_usuario' => $idUsuario,
                        'id_perfil'  => (int) $perfil,
                    ]);

                    self::inserir('fundador', [
                        'id_startup'   => $idStartup,
                        'id_usuario'   => $idUsuario,
                        'funcao'       => $fundador['funcao'],
                        'temp_empresa' => 0,
                        'ativo'        => 1,
                    ]);
                }
            }

            self::confirmarTransacao();
            return $idStartup;
        } catch (Throwable $e) {
            self::cancelarTransacao();
            throw $e;
        }
    }

    /**
     * Atualiza uma startup e sincroniza seus fundadores (editar, adicionar e remover) em transação.
     *
     * @param int $id ID da startup.
     * @param array $dados Dados validados da startup.
     * @param array $fundadores Lista atualizada de fundadores.
     * @return bool True se a atualização for concluída.
     */
    public static function atualizar(int $id, array $dados, array $fundadores): bool
    {
        self::iniciarTransacao();

        try {
            $idAreaAtuacao = isset($dados['id_area_atuacao']) && (int) $dados['id_area_atuacao'] > 0
                ? (int) $dados['id_area_atuacao']
                : self::resolverAreaAtuacao((string) ($dados['setor_atuacao'] ?? ''));

            self::atualizarPorId('startup', $id, [
                'nome'                 => $dados['nome'],
                'biografia'            => $dados['biografia'],
                'cnpj'                 => $dados['cnpj'],
                'data_fundacao'        => $dados['data_fundacao'],
                'id_area_atuacao'      => $idAreaAtuacao,
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

            $atuais = self::selecionarTodos(
                'SELECT id, id_usuario FROM fundador WHERE id_startup = :id',
                ['id' => $id]
            );
            $vinculos = [];
            foreach ($atuais as $atual) {
                $vinculos[(int) $atual['id']] = (int) $atual['id_usuario'];
            }

            $perfilFundador = null;

            foreach ($fundadores as $fundador) {
                $idFundador = $fundador['id'];
                $telefone = $fundador['telefone'] === '' ? null : $fundador['telefone'];

                if ($idFundador > 0) {
                    if (!isset($vinculos[$idFundador])) {
                        throw new RuntimeException('Fundador não pertence a esta startup.');
                    }

                    self::atualizarPorId('usuario', $vinculos[$idFundador], [
                        'nome'     => $fundador['nome'],
                        'email'    => $fundador['email'],
                        'telefone' => $telefone,
                    ]);

                    self::atualizarPorId('fundador', $idFundador, [
                        'funcao' => $fundador['funcao'],
                    ]);

                    unset($vinculos[$idFundador]);
                    continue;
                }

                if ($perfilFundador === null) {
                    $perfilFundador = self::selecionarValor("SELECT id FROM perfil WHERE nome = 'fundador' LIMIT 1");
                    if (!$perfilFundador) {
                        throw new RuntimeException('O perfil de fundador não está cadastrado.');
                    }
                }

                $idUsuario = self::inserir('usuario', [
                    'nome'     => $fundador['nome'],
                    'email'    => $fundador['email'],
                    'telefone' => $telefone,
                    'ativo'    => 1,
                ]);

                self::inserir('usuario_perfil', [
                    'id_usuario' => $idUsuario,
                    'id_perfil'  => (int) $perfilFundador,
                ]);

                self::inserir('fundador', [
                    'id_startup'   => $id,
                    'id_usuario'   => $idUsuario,
                    'funcao'       => $fundador['funcao'],
                    'temp_empresa' => 0,
                    'ativo'        => 1,
                ]);
            }

            if ($vinculos) {
                $removerFundador = self::pegarBanco()->prepare('DELETE FROM fundador WHERE id = :id');
                foreach (array_keys($vinculos) as $idFundador) {
                    $removerFundador->execute(['id' => $idFundador]);
                }
            }

            self::confirmarTransacao();
            return true;
        } catch (Throwable $e) {
            self::cancelarTransacao();
            throw $e;
        }
    }

    /**
     * Altera o status ativo/inativo de uma startup verificando se o registro existe.
     *
     * @param int $id ID da startup.
     * @param bool $ativo Novo status (true = ativo, false = inativo).
     * @return bool True se atualizou ou o registro já estava no status solicitado; false se ID não existir.
     */
    public static function alterarStatus(int $id, bool $ativo): bool
    {
        $afetadas = self::update('startup')
            ->set('ativo', $ativo)
            ->whereId($id)
            ->executarContando();

        if ($afetadas === 1) {
            return true;
        }

        return self::existe('startup', ['id' => $id]);
    }
}

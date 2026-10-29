<?php
 
namespace Models;
 
use Core\bancoDados;
use PDO;
use Exception;
 
class usuarioMentor
{
    public static function cadastrarMentor(array $dados): int
    {
        $db = bancoDados::conectar();
 
        try {
            $db->beginTransaction();
 
            $idPerfil          = self::buscarIdPerfil($db, 'mentor');
            $idUsuario         = self::criarUsuario($db, $dados, $idPerfil);
            $idDisponibilidade = self::criarDisponibilidade($db, $dados);
            $idMentor          = self::criarMentor($db, $dados, $idUsuario, $idDisponibilidade);
 
            self::vincularCompetencias($db, $idMentor, $dados['competencias']);
 
            $db->commit();
            return $idMentor;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
 
    private static function buscarIdPerfil(PDO $db, string $nome): int
    {
        $stmt = $db->prepare('SELECT id FROM perfil WHERE nome = :n');
        $stmt->execute(['n' => $nome]);
        $id = $stmt->fetchColumn();
 
        if (!$id) {
            throw new Exception("Perfil '$nome' não encontrado.");
        }
        return (int) $id;
    }
 
    private static function criarUsuario(PDO $db, array $dados, int $idPerfil): int
    {
        $stmt = $db->prepare('INSERT INTO usuario (nome, email, telefone, ativo, id_perfil, foto)
                              VALUES (:n, :e, :t, 0, :p, :foto)');
        $stmt->execute([
            'n' => $dados['nome'],
            'e' => $dados['email'],
            't' => $dados['telefone'], // só dígitos
            'p' => $idPerfil,
            'foto' => $dados['foto'],
        ]);
        return (int) $db->lastInsertId();
    }
 
    private static function criarDisponibilidade(PDO $db, array $dados): int
    {
        $dias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
        $valoresSemana = [];
        foreach ($dias as $dia) {
            $valoresSemana[$dia] = in_array($dia, $dados['semana'], true) ? 1 : 0;
        }
 
        $stmt = $db->prepare('INSERT INTO semana (domingo, segunda, terca, quarta, quinta, sexta, sabado)
                              VALUES (:domingo, :segunda, :terca, :quarta, :quinta, :sexta, :sabado)');
        foreach ($valoresSemana as $dia => $valor) {
            $stmt->bindValue(':' . $dia, $valor, PDO::PARAM_INT);
        }
        $stmt->execute();
        $idSemana = (int) $db->lastInsertId();
 
        $turnos = ['matutino', 'vespertino', 'noturno'];
        $valoresHorario = [];
        foreach ($turnos as $turno) {
            $valoresHorario[$turno] = in_array($turno, $dados['horario'], true) ? 1 : 0;
        }
 
        $stmt = $db->prepare('INSERT INTO horario (matutino, vespertino, noturno)
                              VALUES (:matutino, :vespertino, :noturno)');
        foreach ($valoresHorario as $turno => $valor) {
            $stmt->bindValue(':' . $turno, $valor, PDO::PARAM_INT);
        }
        $stmt->execute();
        $idHorario = (int) $db->lastInsertId();
 
        $stmt = $db->prepare('INSERT INTO disponibilidade (id_semana, id_horario, modalidade)
                              VALUES (:s, :h, :m)');
        $stmt->execute([
            's' => $idSemana,
            'h' => $idHorario,
            'm' => $dados['modalidade'], // PRESENCIAL | REMOTO | HIBRIDO
        ]);
        return (int) $db->lastInsertId();
    }
 
    private static function criarMentor(PDO $db, array $dados, int $idUsuario, int $idDisponibilidade): int
    {
        $stmt = $db->prepare('INSERT INTO mentor
            (linkedin, instagram, biografia, banner, id_parceiro, id_usuario, id_disponibilidade)
            VALUES (:l, :i, :b, :fb, :id_p, :id_u, :id_d)');
        $stmt->execute([
            'l'    => $dados['linkedin'] ?: null,
            'i'    => $dados['instagram'] ?: null,
            'b'    => $dados['biografia'],
            'fb'   => $dados['banner'],
            'id_p' => $dados['id_parceiro'],
            'id_u' => $idUsuario,
            'id_d' => $idDisponibilidade,
        ]);
        return (int) $db->lastInsertId();
    }
 
    private static function vincularCompetencias(PDO $db, int $idMentor, array $idsCompetencia): void
    {
        $stmt = $db->prepare('INSERT INTO mentor_competencia (id_mentor, id_competencia)
                              VALUES (:m, :c)');
        foreach (array_unique($idsCompetencia) as $idCompetencia) {
            $stmt->execute(['m' => $idMentor, 'c' => (int) $idCompetencia]);
        }
    }
 
    public static function buscarPorId(int $idMentor): ?array
    {
        $db = bancoDados::conectar();
 
        $stmt = $db->prepare("
            SELECT
                m.id AS id_mentor,
                u.id AS id_usuario,
                u.nome,
                u.email,
                u.telefone,
                m.linkedin,
                m.instagram,
                m.biografia,
                u.foto,
                m.banner,
                m.id_parceiro,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ', ')
                    FROM mentor_competencia mc
                    INNER JOIN competencia c ON c.id = mc.id_competencia
                    WHERE mc.id_mentor = m.id
                ) AS areas_atuacao,
                d.modalidade,
                CAST(s.domingo AS UNSIGNED) AS domingo,
                CAST(s.segunda AS UNSIGNED) AS segunda,
                CAST(s.terca AS UNSIGNED) AS terca,
                CAST(s.quarta AS UNSIGNED) AS quarta,
                CAST(s.quinta AS UNSIGNED) AS quinta,
                CAST(s.sexta AS UNSIGNED) AS sexta,
                CAST(s.sabado AS UNSIGNED) AS sabado,
                CAST(h.matutino AS UNSIGNED) AS matutino,
                CAST(h.vespertino AS UNSIGNED) AS vespertino,
                CAST(h.noturno AS UNSIGNED) AS noturno
            FROM mentor m
            INNER JOIN usuario u ON u.id = m.id_usuario
            INNER JOIN disponibilidade d ON d.id = m.id_disponibilidade
            INNER JOIN semana s ON s.id = d.id_semana
            INNER JOIN horario h ON h.id = d.id_horario
            WHERE m.id = :id
        ");
        $stmt->execute(['id' => $idMentor]);
        $mentor = $stmt->fetch();
 
        if (!$mentor) {
            return null;
        }
 
        $stmtCompetencias = $db->prepare(
            'SELECT id_competencia FROM mentor_competencia WHERE id_mentor = :id'
        );
        $stmtCompetencias->execute(['id' => $idMentor]);
        $mentor['competencias'] = array_map('intval', $stmtCompetencias->fetchAll(PDO::FETCH_COLUMN));
 
        return $mentor;
    }
 
    public static function atualizarMentor(array $dados, int $idMentor): bool
    {
        $db = bancoDados::conectar();
 
        try {
            $db->beginTransaction();
 
            $stmt = $db->prepare("
                SELECT u.id AS id_usuario, d.id_semana, d.id_horario, d.id AS id_disponibilidade
                FROM mentor m
                INNER JOIN usuario u ON u.id = m.id_usuario
                INNER JOIN disponibilidade d ON d.id = m.id_disponibilidade
                WHERE m.id = :id
            ");
            $stmt->execute(['id' => $idMentor]);
            $relacoes = $stmt->fetch();
            if (!$relacoes) {
                throw new Exception('Mentor não encontrado.');
            }
 
            $stmtUsuario = $db->prepare(
                'UPDATE usuario SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id'
            );
            $stmtUsuario->execute([
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'telefone' => $dados['telefone'],
                'id' => $relacoes['id_usuario'],
            ]);
 
            $dias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            $stmtSemana = $db->prepare("
                UPDATE semana SET domingo = :domingo, segunda = :segunda, terca = :terca,
                    quarta = :quarta, quinta = :quinta, sexta = :sexta, sabado = :sabado
                WHERE id = :id
            ");
            foreach ($dias as $dia) {
                $stmtSemana->bindValue(
                    ':' . $dia,
                    in_array($dia, $dados['semana'], true) ? 1 : 0,
                    PDO::PARAM_INT
                );
            }
            $stmtSemana->bindValue(':id', (int) $relacoes['id_semana'], PDO::PARAM_INT);
            $stmtSemana->execute();
 
            $turnos = ['matutino', 'vespertino', 'noturno'];
            $stmtHorario = $db->prepare(
                'UPDATE horario SET matutino = :matutino, vespertino = :vespertino, noturno = :noturno WHERE id = :id'
            );
            foreach ($turnos as $turno) {
                $stmtHorario->bindValue(
                    ':' . $turno,
                    in_array($turno, $dados['horario'], true) ? 1 : 0,
                    PDO::PARAM_INT
                );
            }
            $stmtHorario->bindValue(':id', (int) $relacoes['id_horario'], PDO::PARAM_INT);
            $stmtHorario->execute();
 
            $stmtDisponibilidade = $db->prepare(
                'UPDATE disponibilidade SET modalidade = :modalidade WHERE id = :id'
            );
            $stmtDisponibilidade->execute([
                'modalidade' => $dados['modalidade'],
                'id' => $relacoes['id_disponibilidade'],
            ]);
 
            $stmtMentor = $db->prepare("
                UPDATE mentor
                SET linkedin = :linkedin, instagram = :instagram, biografia = :biografia,
                    banner = :banner, id_parceiro = :id_parceiro
                WHERE id = :id
            ");
            $stmtMentor->execute([
                'linkedin' => $dados['linkedin'],
                'instagram' => $dados['instagram'],
                'biografia' => $dados['biografia'],
                'banner' => $dados['banner'],
                'id_parceiro' => $dados['id_parceiro'],
                'id' => $idMentor,
            ]);
 
            $db->prepare('UPDATE usuario SET foto = :foto WHERE id = :id')
                ->execute([
                    'foto' => $dados['foto'],
                    'id' => $relacoes['id_usuario'],
                ]);
 
            $db->prepare('DELETE FROM mentor_competencia WHERE id_mentor = :id')
                ->execute(['id' => $idMentor]);
            self::vincularCompetencias($db, $idMentor, $dados['competencias']);
 
            $db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
 
    public static function listarMentores(): array
    {
        $db = bancoDados::conectar();
 
        $sql = "
        SELECT
            m.id AS id_mentor,
            u.id AS id_usuario,
            u.nome,
            u.email,
            u.telefone,
            u.ativo,
 
            m.linkedin,
            m.instagram,
            m.biografia,
            u.foto,
            m.banner,
 
            (
                SELECT GROUP_CONCAT(DISTINCT c.nome ORDER BY c.nome SEPARATOR ', ')
                FROM mentor_competencia mc
                INNER JOIN competencia c
                    ON c.id = mc.id_competencia
                WHERE mc.id_mentor = m.id
            ) AS areas_atuacao,
 
            p.nome AS perfil,
 
            d.id AS id_disponibilidade,
            d.modalidade,
 
            s.domingo,
            s.segunda,
            s.terca,
            s.quarta,
            s.quinta,
            s.sexta,
            s.sabado,
 
            h.matutino,
            h.vespertino,
            h.noturno
 
        FROM mentor m
 
        INNER JOIN usuario u
            ON u.id = m.id_usuario
 
        INNER JOIN perfil p
            ON p.id = u.id_perfil
 
        LEFT JOIN disponibilidade d
            ON d.id = m.id_disponibilidade
 
        LEFT JOIN semana s
            ON s.id = d.id_semana
 
        LEFT JOIN horario h
            ON h.id = d.id_horario
 
        WHERE p.nome = 'mentor'
 
        ORDER BY u.nome ASC
    ";
 
        $stmt = $db->prepare($sql);
        $stmt->execute();
 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
        public static function alterarStatus(int $id, bool $ativo): bool
    {
        $db = bancoDados::conectar();
 
        $stmt = $db->prepare("UPDATE usuario
        SET ativo = :ativo
        WHERE id = :id_usuario");
 
        $stmt->bindValue(':ativo', $ativo ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $id, PDO::PARAM_INT);
 
        return $stmt->execute();
    }
}
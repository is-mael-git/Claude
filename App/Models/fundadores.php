<?php

namespace Models;

use Core\bancoDados;

class fundadores
{
    public static function substituirTodos(int $idStartup, array $fundadores): void
    {
        $pdo = bancoDados::conectar();
        $pdo->beginTransaction();

        try {
            // Deleta todos os fundadores existentes para a startup
            $stmtDelete = $pdo->prepare('DELETE FROM fundadores WHERE id_startup = :id_startup');
            $stmtDelete->execute(['id_startup' => $idStartup]);

            // Insere os novos fundadores
            $stmtInsert = $pdo->prepare('INSERT INTO fundadores (id_startup, nome_fundador, email_fundador) VALUES (:id_startup, :nome_fundador, :email_fundador)');
            foreach ($fundadores as $fundador) {
                if (!empty($fundador['nome']) && !empty($fundador['email'])) {
                    $stmtInsert->execute([
                        'id_startup'     => $idStartup,
                        'nome_fundador'  => $fundador['nome'],
                        'email_fundador' => $fundador['email'],
                    ]);
                }
            }

            $pdo->commit();
        } catch (\PDOException $erro) {
            $pdo->rollBack();
            throw $erro;
        }
    }

        public static function listarPorStartup(int $idStartup): array
        {
            $pdo = bancoDados::conectar();
            $stmt = $pdo->prepare('SELECT nome_fundador, email_fundador FROM fundadores WHERE id_startup = :id_startup');
            $stmt->execute(['id_startup' => $idStartup]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        

    }

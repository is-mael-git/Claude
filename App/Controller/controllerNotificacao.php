<?php

namespace Controller;

use Core\auth;
use Models\notificacao;
use Throwable;

class controllerNotificacao
{
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        $usuarioId = auth::id();

        if (!$usuarioId) {
            http_response_code(401);
            echo json_encode(['sucesso' => false]);
            exit;
        }

        $nivelBuffer = ob_get_level();

        try {
            $notificacoes = notificacao::buscarPorUsuario($usuarioId);
            echo json_encode(self::montarDados($notificacoes), JSON_UNESCAPED_UNICODE);
        } catch (Throwable $erro) {
            if (ob_get_level() > $nivelBuffer) {
                ob_end_clean();
            }
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível buscar notificações.']);
        }

        exit;
    }

    public function eventos(): void
    {
        $usuarioId = auth::id();

        if (!$usuarioId) {
            http_response_code(401);
            exit;
        }

        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache, no-transform');
        header('X-Accel-Buffering: no');

        // A conexão fica aberta; libera a sessão para as demais requisições da página.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        echo "retry: 2000\n\n";
        flush();

        $assinaturaAnterior = null;

        for ($segundo = 0; $segundo < 25 && !connection_aborted(); $segundo++) {
            try {
                $notificacoes = notificacao::buscarPorUsuario($usuarioId);
                $assinatura = hash('sha256', json_encode($notificacoes, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));

                if ($assinatura !== $assinaturaAnterior) {
                    $dados = self::montarDados($notificacoes);
                    echo 'data: ' . json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . "\n\n";
                    $assinaturaAnterior = $assinatura;
                } elseif ($segundo % 10 === 0) {
                    echo ": aguardando\n\n";
                }

                flush();
            } catch (Throwable $erro) {
                error_log('Falha ao acompanhar notificações: ' . $erro->getMessage());
                break;
            }

            sleep(1);
        }

        exit;
    }

    private static function montarDados(array $notificacoes): array
    {
        $naoLidas = 0;

        ob_start();
        foreach ($notificacoes as $notificacao) {
            $naoLidas += $notificacao['status'] === 'entrada'
                && (int) $notificacao['visualizado'] === 0 ? 1 : 0;
            require __DIR__ . '/../Views/Componentes/Menu/Secoes/itemNotificacaoMenuLateral.php';
        }
        $html = ob_get_clean();

        return [
            'sucesso' => true,
            'html' => $html,
            'nao_lidas' => $naoLidas
        ];
    }

    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $usuarioId = auth::id();

        if (!$usuarioId) {
            http_response_code(401);
            echo json_encode(['sucesso' => false]);
            exit;
        }

        if (($_POST['abrir'] ?? '') === '1') {
            try {
                $notificacoes = notificacao::buscarPorUsuario($usuarioId);
                $ids = [];

                foreach ($notificacoes as $item) {
                    if ($item['status'] === 'entrada' && (int) $item['visualizado'] === 0) {
                        $ids[] = (int) $item['id'];
                    }
                }

                if ($ids) {
                    notificacao::marcarVisualizadas($usuarioId, $ids);
                }

                echo json_encode(['sucesso' => true, 'ids' => $ids]);
            } catch (Throwable $erro) {
                http_response_code(500);
                echo json_encode(['sucesso' => false]);
            }

            exit;
        }

        $ids = $_POST['ids'] ?? [];

        if (!is_array($ids)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false]);
            exit;
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));

        if (!$ids) {
            echo json_encode(['sucesso' => true]);
            exit;
        }

        try {
            notificacao::marcarVisualizadas($usuarioId, $ids);
            echo json_encode(['sucesso' => true]);
        } catch (Throwable $erro) {
            http_response_code(500);
            echo json_encode(['sucesso' => false]);
        }

        exit;
    }

    public function alterarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $usuarioId = auth::id();
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$usuarioId) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Faça login para alterar a notificação.']);
            exit;
        }

        if ($id <= 0 || !in_array($status, ['entrada', 'arquivado', 'lixeira'], true)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Notificação ou destino inválido.']);
            exit;
        }

        try {
            if (!notificacao::alterarStatus($id, $usuarioId, $status)) {
                http_response_code(404);
                echo json_encode(['sucesso' => false, 'mensagem' => 'Notificação não encontrada.']);
                exit;
            }

            echo json_encode(['sucesso' => true, 'status' => $status]);
        } catch (Throwable $erro) {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível mover a notificação.']);
        }

        exit;
    }
}

<?php

namespace Core;

class controller{

    protected function mostrarTela(string $name, array $data = []) : void {
        extract($data);

        $viewFile = __DIR__ . '/../Views/' . $name . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            exit('Página não existe');
        }

        require $viewFile;
    }
    protected function redirecionar(string $url){
        $urlRedirecionamento = (strpos($url, "http") === 0) ? $url : BASE_URL . ltrim($url, "/");
        header("Location: " . $urlRedirecionamento);
        exit;
    }
    /**
     * Valida e sanitiza os parâmetros de ordenação (orderBy e direction) vindos de $_GET
     * contra uma lista de colunas permitidas.
     *
     * @param array $colunasPermitidas Lista de colunas SQL autorizadas para ordenação (whitelist).
     * @param string $padrao Nome da coluna padrão caso o parâmetro seja inválido ou ausente. Padrão: 'id'.
     * @param string $direcaoPadrao Direção padrão ('ASC' ou 'DESC'). Padrão: 'DESC'.
     * @return string Expressão SQL segura para ordenação (ex: "nome ASC").
     */
    protected function ordenacaoTabela(array $colunasPermitidas, string $padrao = 'id', string $direcaoPadrao = 'DESC') : string{
        $coluna = $_GET['orderBy'] ?? $padrao;
        if (!in_array($coluna, $colunasPermitidas, true)) {
            $coluna = $padrao;
        }
        $direcao = strtoupper($_GET['direction'] ?? $direcaoPadrao);
        if (!in_array($direcao, ['ASC', 'DESC'], true)) {
            $direcao = $direcaoPadrao;
        }
        return "$coluna $direcao";
    }
}
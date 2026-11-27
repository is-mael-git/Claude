<?php

namespace Controller;

use Core\controller;
use Core\auth;
use Core\validador;
use Core\hashSenha;
use Models\usuario;

class controllerAuth extends controller{
    public function renovarSessao() : void {
        header('Content-Type: application/json; charset=utf-8');

        if (!auth::renovarSessaoPorAtividade()) {
            http_response_code(401);
            echo json_encode(['sucesso' => false]);
            return;
        }

        echo json_encode(['sucesso' => true]);
    }

    public function loginForm() : void {
        if (auth::checar()) {
            $this->redirecionar('/areaTrabalho/adm');
        }

        $marcadorSessaoAnterior = auth::consumirMarcadorAutenticacaoAnterior();
        $sessaoExpirada = ($_GET['sessaoExpirada'] ?? '') === '1'
            || $marcadorSessaoAnterior;

        $this->mostrarTela('/login', [
            'erro'            => auth::pegarFlash('erro'),
            'sucesso'         => auth::pegarFlash('sucesso'),
            'sessaoExpirada'  => $sessaoExpirada
        ]);
    }

    public function login() : void {
        if(!auth::validarCsrf($_POST['csrfToken'] ?? null)) {
            http_response_code(419);
            exit('CSRF inválido');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if(!validador::required($email) || !validador::required($senha)) {
            auth::flash('erro', 'Email ou senha errada.');
            $this->redirecionar('/login');
        }

        if(!validador::email($email)) {
            auth::flash('erro', 'Email inválido');
            $this->redirecionar('/login');
        }

        $usuario = usuario::buscarEmail($email);

        if(!$usuario || !hashSenha::verificar($senha, $usuario['senha'])) {
            auth::flash('erro', 'Email ou senha errada.');
            $this->redirecionar('/login');
        }

        $lembrarAcesso = isset($_POST['lembrarAcesso']);
        auth::login($usuario, $lembrarAcesso);
        auth::flash('sucesso', 'login com sucesso');
        $this->redirecionar('/areaTrabalho/adm');
    }

    public function logout() : void{
        auth::requerLogin();

        if(!auth::validarCsrf($_POST['csrfToken'] ?? null)) {
            http_response_code(419);
            exit('CSRF inválido');
        }

        auth::logout();
        auth::flash('sucesso', 'logout realizado');
        $this->redirecionar('/login');
    }
}

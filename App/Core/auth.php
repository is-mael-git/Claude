<?php

namespace Core;

use Throwable;

class auth {
    public static function configJWT() : array {
        $config = require __DIR__ . '/../../Config/aplicativo.php';

        return $config['jwt'];
    }

    public static function removerCookie() : void {
        $config = self::configJWT();

        setcookie($config['cookie_name'], '', [
            'expires'   => time() - 3600,
            'path'      => BASE_URL,
            'secure'    => (bool) $config['cookie_secure'],
            'httponly'  => true,
            'samesite'  => 'Lax'
        ]);
        unset($_COOKIE[$config['cookie_name']]);
    }
    public static function login(array $usuario, bool $lembrarAcesso = false) : void {
        $config  = self::configJWT();
        $agora   = time();
        
        $ttl = $lembrarAcesso ? 60 * 60 * 24 * 30 : (int) $config['ttl'];

        $payload = [
            'sub'       => (int) $usuario['id'],
            'email'     => $usuario['email'],
            'perfil'     => $usuario['perfil'],
            'permissao' => $usuario['permissao'] ?? [],
            'sid'       => bin2hex(random_bytes(16)),
            'iat'       => $agora,
            'ttl'       => $ttl,
            'exp'       => $agora + $ttl
            ];

        $token = jwtToken::encode($payload, $config['secret']);
            
        setcookie($config['cookie_name'], $token, [
            'expires'   => $payload['exp'],
            'path'      => BASE_URL,
            'secure'    => (bool) $config['cookie_secure'],
            'httponly'  => true,
            'samesite'  => 'Lax'
        ]);
                
        session_regenerate_id(true);
        $_SESSION['autenticacaoAtiva'] = true;
    }

    public static function usuario() : ?array {
        $config = self::configJWT();
        $token  = $_COOKIE[$config['cookie_name']] ?? null;

        if(!$token) {
            return null;
        }

        try {
            $payload = jwtToken::decode($token, $config['secret']);
            $_SESSION['autenticacaoAtiva'] = true;
            
            return[
                'id'        => (int) $payload['sub'],
                'email'     => $payload['email'] ?? '',
                'perfil'     => $payload['perfil'] ?? '',
                'permissao' => $payload['permissao'] ?? ''
            ];
        } catch(Throwable $erro) {
            self::removerCookie();
            return null;
        }
    }

    public static function renovarSessaoPorAtividade() : bool {
        $config = self::configJWT();
        $token = $_COOKIE[$config['cookie_name']] ?? null;

        if (!$token) {
            return false;
        }

        try {
            $payload = jwtToken::decode($token, $config['secret']);
            $ttl = max(1, (int) ($payload['ttl'] ?? ((int) $payload['exp'] - (int) ($payload['iat'] ?? time()))));
            $payload['iat'] = time();
            $payload['ttl'] = $ttl;
            $payload['exp'] = $payload['iat'] + $ttl;
            $tokenRenovado = jwtToken::encode($payload, $config['secret']);

            setcookie($config['cookie_name'], $tokenRenovado, [
                'expires'  => $payload['exp'],
                'path'     => BASE_URL,
                'secure'   => (bool) $config['cookie_secure'],
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            return true;
        } catch (Throwable $erro) {
            self::removerCookie();
            return false;
        }
    }

    public static function ttlSessaoAtual() : int {
        $config = self::configJWT();
        $token = $_COOKIE[$config['cookie_name']] ?? null;

        if (!$token) {
            return max(1, (int) $config['ttl']);
        }

        try {
            $payload = jwtToken::decode($token, $config['secret']);
            return max(1, (int) ($payload['ttl'] ?? ((int) $payload['exp'] - (int) ($payload['iat'] ?? time()))));
        } catch (Throwable $erro) {
            return max(1, (int) $config['ttl']);
        }
    }

    public static function idSessaoAtual() : string {
        $config = self::configJWT();
        $token = $_COOKIE[$config['cookie_name']] ?? null;

        if (!$token) {
            return '';
        }

        try {
            $payload = jwtToken::decode($token, $config['secret']);
            if (!empty($payload['sid'])) {
                return (string) $payload['sid'];
            }

            return hash('sha256', (string) ($payload['sub'] ?? '') . ':legacy');
        } catch (Throwable $erro) {
            return '';
        }
    }

    public static function consumirMarcadorAutenticacaoAnterior() : bool {
        $autenticadoAnteriormente = !empty($_SESSION['autenticacaoAtiva']);
        unset($_SESSION['autenticacaoAtiva']);
        return $autenticadoAnteriormente;
    }
    
    public static function id(): ?int {
        return self::usuario()['id'] ?? null;
    }

    public static function checar() : bool {
        return self::usuario() !== null;
    }
    
    public static function convidado() : bool {
        return !self::checar();
    }
    
    public static function pode(string $permissao) : bool {
        $usuario = self::usuario();

        return $usuario !== null && in_array(
            $permissao,
            $usuario['permissao'],
            true
        );
    }

    public static function temCargo(string $cargo) : bool {
        $usuario = self::usuario();

        return $usuario !== null && in_array(
            $cargo,
            $usuario['cargo'],
            true
        );
    }
    
    public static function requerLogin() : void {
        $config = self::configJWT();
        $cookieExistia = !empty($_COOKIE[$config['cookie_name']]);
        $sessaoAutenticada = !empty($_SESSION['autenticacaoAtiva']);

        if(!self::checar()) {
            unset($_SESSION['autenticacaoAtiva']);

            if ($cookieExistia || $sessaoAutenticada) {
                header('Location: ' . BASE_URL . 'login?sessaoExpirada=1');
            } else {
                self::flash('erro', 'Faz login ae');
                header('Location: ' . BASE_URL . 'login');
            }
            exit();
        }
    }
    
    public static function requerPermissao(string $permissao) : void {
        self::requerLogin();

        if(!self::pode($permissao)) {
            header('Location: ' . BASE_URL . '403');
            exit;
        }
    }
    public static function logout() : void {
        self::removerCookie();
        unset($_SESSION['autenticacaoAtiva']);
        session_regenerate_id(true);
        }
    public static function flash(string $tipo, string $mensagem) : void {
        $_SESSION['flash'][$tipo] = $mensagem;
    }

    public static function pegarFlash(string $tipo) : ?string {
        $mensagem = $_SESSION['flash'][$tipo] ?? [];
        unset($_SESSION['flash'][$tipo]);
        return is_string($mensagem) ? $mensagem : null;
    }

    public static function csrfToken() : string {
        if(empty($_SESSION['csrfToken'])){
            $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrfToken'];
    }
    public static function validarCsrf() : string {
        if(empty($_SESSION['csrfToken'])) {
            $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrfToken'];
    }
}

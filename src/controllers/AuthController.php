<?php

class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            header('Location: /trivial/dashboard');
            exit;
        }

        $googleHelper = new GoogleHelper();
        $googleLoginUrl = $googleHelper->getAuthUrl();

        require_once __DIR__ . '/../views/trivial/auth/login.php';
    }

    public function login(): void
    {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirectWithError('/trivial/auth/login', 'Token de seguridad inválido.');
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $this->redirectWithError('/trivial/auth/login', 'Email y contraseña son obligatorios.');
            return;
        }

        $model = new UsuarioModel();
        $user  = $model->findByEmail($email);

        if (!$user || !Auth::verifyPassword($password, $user['password'])) {
            $this->redirectWithError('/trivial/auth/login', 'Email o contraseña incorrectos.');
            return;
        }

        Auth::login($user);
        header('Location: /trivial/dashboard');
        exit;
    }

    public function googleCallback(): void
    {
        $code = $_GET['code'] ?? null;
        if (!$code) {
            $this->redirectWithError('/trivial/auth/login', 'Fallo al autenticar con Google.');
            return;
        }

        $googleHelper = new GoogleHelper();
        $token = $googleHelper->getAccessToken($code);
        if (!$token) {
            $this->redirectWithError('/trivial/auth/login', 'No se pudo obtener el token de Google.');
            return;
        }

        $userInfo = $googleHelper->getUserInfo($token);
        if (!$userInfo || !isset($userInfo['email'])) {
            $this->redirectWithError('/trivial/auth/login', 'No se pudo obtener la información del usuario.');
            return;
        }

        $model = new UsuarioModel();
        
        // 1. Buscar por Google ID
        $user = $model->findByGoogleId($userInfo['sub']);

        // 2. Si no existe, buscar por Email
        if (!$user) {
            $user = $model->findByEmail($userInfo['email']);
            
            // Si existe por email pero no tenía Google ID, lo vinculamos
            if ($user) {
                $model->update($user['id'], ['google_id' => $userInfo['sub']]);
            }
        }

        // 3. Si sigue sin existir, lo creamos automáticamente (Registro Público)
        if (!$user) {
            $userId = $model->create([
                'nombre'    => $userInfo['name'] ?? $userInfo['given_name'],
                'email'     => $userInfo['email'],
                'google_id' => $userInfo['sub'],
                'nivel'     => 'participante'
            ]);
            $user = $model->findById($userId);
        }

        if ($user) {
            Auth::login($user);
            header('Location: /trivial/dashboard');
        } else {
            $this->redirectWithError('/trivial/auth/login', 'Error al crear o recuperar el usuario.');
        }
        exit;
    }

    public function registroForm(): void
    {
        if (Auth::check()) {
            header('Location: /trivial/dashboard');
            exit;
        }
        $googleHelper = new GoogleHelper();
        $googleLoginUrl = $googleHelper->getAuthUrl();
        require_once __DIR__ . '/../views/trivial/auth/registro.php';
    }

    public function registro(): void
    {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->redirectWithError('/auth/registro', 'Token de seguridad inválido.');
            return;
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$nombre || !$email || !$password) {
            $this->redirectWithError('/auth/registro', 'Todos los campos son obligatorios.');
            return;
        }

        $model = new UsuarioModel();
        if ($model->emailExists($email)) {
            $this->redirectWithError('/auth/registro', 'El email ya está registrado.');
            return;
        }

        $userId = $model->create([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => $password,
            'nivel'    => 'participante'
        ]);

        if ($userId) {
            $_SESSION['flash_success'] = 'Registro completado. Ya puedes iniciar sesión.';
            header('Location: /trivial/auth/login');
        } else {
            $this->redirectWithError('/auth/registro', 'Error al registrar el usuario.');
        }
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }

    private function redirectWithError(string $url, string $msg): void
    {
        $_SESSION['flash_error'] = $msg;
        header("Location: {$url}");
        exit;
    }
}

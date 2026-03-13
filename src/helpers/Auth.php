<?php

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] === 'admin';
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'     => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'],
            'email'  => $_SESSION['user_email'],
            'nivel'  => $_SESSION['user_nivel'],
        ];
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_nivel']  = $user['nivel'];
    }

    public static function logout(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /auth/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function csrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

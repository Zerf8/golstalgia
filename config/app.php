<?php

// Load .env
function loadEnv(): void
{
    $envFile = dirname(__DIR__) . '/.env';
    if (!file_exists($envFile)) return;

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
        putenv(trim($key) . '=' . trim($value));
    }
}

loadEnv();

define('APP_ENV',    $_ENV['APP_ENV']    ?? 'production');
define('APP_URL',    $_ENV['APP_URL']    ?? '');
define('APP_SECRET', $_ENV['APP_SECRET'] ?? 'secret');

// Session config
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
if (APP_ENV === 'production') {
    ini_set('session.cookie_secure', '1');
}

session_start();

// Autoload
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../src/models/',
        __DIR__ . '/../src/controllers/',
        __DIR__ . '/../src/helpers/',
        __DIR__ . '/../config/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Error display
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

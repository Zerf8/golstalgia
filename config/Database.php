<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $env = self::loadEnv();

            $host = $env['DB_HOST'] ?? 'localhost';
            $port = $env['DB_PORT'] ?? '3306';
            $name = $env['DB_NAME'] ?? 'golstalgia';
            $user = $env['DB_USER'] ?? 'root';
            $pass = $env['DB_PASS'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log("DB Connection failed: " . $e->getMessage());
                http_response_code(500);
                die(json_encode(['error' => 'Database connection failed']));
            }
        }

        return self::$instance;
    }

    private static function loadEnv(): array
    {
        $envFile = dirname(__DIR__) . '/.env';
        $env = [];

        if (!file_exists($envFile)) {
            return $env;
        }

        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }

        return $env;
    }
}

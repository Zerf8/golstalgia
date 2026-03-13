<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = rtrim($uri, '/') ?: '/';

        // Try exact match
        if (isset($this->routes[$method][$uri])) {
            $this->call($this->routes[$method][$uri]);
            return;
        }

        // Try dynamic segments (:param)
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = preg_replace('/:[a-zA-Z_]+/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                $this->call($handler, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        require_once __DIR__ . '/../src/views/404.php';
    }

    private function call(callable|array $handler, array $params = []): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method(...$params);
        } else {
            $handler(...$params);
        }
    }
}

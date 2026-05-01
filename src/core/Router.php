<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = strtok($_SERVER['REQUEST_URI'], '?');
        $uri    = rtrim($uri, '/') ?: '/';

        $handler = $this->routes[$method][$uri] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:40px"><h1>404 Not Found</h1></body></html>';
            return;
        }

        if ($method === 'POST' && !CsrfToken::verify()) {
            http_response_code(403);
            echo '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:40px"><h1>403 Forbidden</h1><p>Invalid or missing CSRF token.</p></body></html>';
            return;
        }

        $handler();
    }
}

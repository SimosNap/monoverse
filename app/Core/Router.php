<?php
declare(strict_types=1);

namespace Monoverse\Core;

class Router
{
    private array $routes = [];

    public function __construct(
        private Request $request,
        private Response $response
    ) {
    }

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            $parameters = $this->matchRoute(
                $route['path'],
                $path
            );

            if ($parameters === false) {
                continue;
            }

            call_user_func_array(
                $route['handler'],
                $parameters
            );

            return;
        }

        $this->response
            ->status(404)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->send('Pagina non trovata');
    }

    private function addRoute(
        string $method,
        string $path,
        callable $handler
    ): void {
        $normalizedPath = $this->normalizePath($path);

        $this->routes[$method][] = [
            'path' => $normalizedPath,
            'handler' => $handler,
        ];
    }

    private function matchRoute(
        string $routePath,
        string $requestPath
    ): array|false {
        $routePath = $this->normalizePath($routePath);
        $requestPath = $this->normalizePath($requestPath);

        if ($routePath === '/') {
            return $requestPath === '/'
                ? []
                : false;
        }

        $routeSegments = explode(
            '/',
            trim($routePath, '/')
        );

        $patternSegments = [];

        foreach ($routeSegments as $segment) {
            if (
                preg_match(
                    '/^\{[a-zA-Z_][a-zA-Z0-9_]*\}$/',
                    $segment
                )
            ) {
                $patternSegments[] = '([^/]+)';
                continue;
            }

            $patternSegments[] = preg_quote(
                $segment,
                '#'
            );
        }

        $pattern = '#^/'
            . implode('/', $patternSegments)
            . '$#u';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return false;
        }

        array_shift($matches);

        return array_map(
            static fn (string $value): string => rawurldecode($value),
            $matches
        );
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        $normalized = '/' . trim($path, '/');

        return $normalized === ''
            ? '/'
            : $normalized;
    }
}

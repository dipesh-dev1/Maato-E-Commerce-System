<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var Route[] */
    private array $routes = [];
    private array $prefixStack = [];
    private array $middlewareStack = [];

    public function get(string $path, array $handler): Route { return $this->add(['GET', 'HEAD'], $path, $handler); }
    public function post(string $path, array $handler): Route { return $this->add(['POST'], $path, $handler); }
    public function patch(string $path, array $handler): Route { return $this->add(['PATCH'], $path, $handler); }
    public function delete(string $path, array $handler): Route { return $this->add(['DELETE'], $path, $handler); }

    public function group(string $prefix, array $middleware, callable $routes): void
    {
        $this->prefixStack[] = $prefix;
        $this->middlewareStack[] = $middleware;
        $routes($this);
        array_pop($this->prefixStack);
        array_pop($this->middlewareStack);
    }

    private function add(array $methods, string $path, array $handler): Route
    {
        $full = rtrim(implode('', $this->prefixStack) . $path, '/') ?: '/';
        $route = new Route($methods, $full, $handler, array_merge([], ...$this->middlewareStack));
        $this->routes[] = $route;
        return $route;
    }

    /** @return array{0: Route, 1: array<string,string>} */
    public function match(Request $request): array
    {
        $pathMatched = false;
        foreach ($this->routes as $route) {
            if (!preg_match($route->regex, $request->path(), $m)) {
                continue;
            }
            $pathMatched = true;
            if (in_array($request->method(), $route->methods, true)) {
                return [$route, array_intersect_key($m, array_flip($route->paramNames))];
            }
        }
        throw new HttpException($pathMatched ? 405 : 404);
    }

    public function url(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->name !== $name) {
                continue;
            }
            $path = preg_replace_callback('/\{(\w+)(?::[^}]+)?\}/', static function (array $m) use (&$params, $name): string {
                if (!array_key_exists($m[1], $params)) {
                    throw new \InvalidArgumentException("Missing parameter {$m[1]} for route {$name}");
                }
                $value = rawurlencode((string) $params[$m[1]]);
                unset($params[$m[1]]);
                return $value;
            }, $route->path);
            return url($path, $params);
        }
        throw new \InvalidArgumentException("Unknown route {$name}");
    }
}

<?php
declare(strict_types=1);

namespace App\Core;

final class Route
{
    public ?string $name = null;
    public string $regex;
    /** @var string[] */
    public array $paramNames = [];

    public function __construct(
        public array $methods,
        public string $path,
        public array $handler,
        public array $middleware = [],
    ) {
        $this->regex = $this->compile($path);
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function middleware(string ...$middleware): self
    {
        $this->middleware = array_merge($this->middleware, $middleware);
        return $this;
    }

    /** "/product/{slug}" or "/orders/{id:\d+}" become anchored named-group regexes. */
    private function compile(string $path): string
    {
        $regex = preg_replace_callback('/\{(\w+)(?::([^}]+))?\}/', function (array $m): string {
            $this->paramNames[] = $m[1];
            return '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')';
        }, $path);
        return '#^' . $regex . '$#u';
    }
}

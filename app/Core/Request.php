<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $params = [];
    private ?array $json = null;

    public function __construct(
        private string $method,
        private string $path,
        private array $query,
        private array $body,
        private array $files,
        private array $server,
    ) {
    }

    public static function capture(): self
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = (string) parse_url(config('app.url'), PHP_URL_PATH);
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $path = '/' . trim(rawurldecode($uri), '/');

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper((string) $_POST['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $spoofed;
            }
        }
        return new self($method, $path, $_GET, $_POST, $_FILES, $_SERVER);
    }

    public function method(): string { return $this->method; }
    public function path(): string { return $this->path; }

    public function isMethod(string ...$methods): bool
    {
        return in_array($this->method, $methods, true);
    }

    public function setParams(array $params): void { $this->params = $params; }
    public function param(string $key, mixed $default = null): mixed { return $this->params[$key] ?? $default; }

    public function query(string $key, mixed $default = null): mixed
    {
        $value = $this->query[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public function allQuery(): array { return $this->query; }

    /** Body input: form fields or a decoded JSON body. */
    public function input(string $key, mixed $default = null): mixed
    {
        $value = $this->all()[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public function all(): array
    {
        if ($this->isJson()) {
            if ($this->json === null) {
                $decoded = json_decode((string) file_get_contents('php://input'), true);
                $this->json = is_array($decoded) ? $decoded : [];
            }
            return $this->json;
        }
        return $this->body;
    }

    public function only(array $keys): array
    {
        $all = $this->all();
        $out = [];
        foreach ($keys as $k) {
            $v = $all[$k] ?? null;
            $out[$k] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    /** Normalises a multi-file input (name="images[]") into a list of single-file arrays. */
    public function files(string $key): array
    {
        $raw = $this->files[$key] ?? null;
        if (!is_array($raw) || !is_array($raw['name'] ?? null)) {
            return [];
        }
        $list = [];
        foreach ($raw['name'] as $i => $name) {
            if ($raw['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $list[] = [
                'name' => $name, 'type' => $raw['type'][$i], 'tmp_name' => $raw['tmp_name'][$i],
                'error' => $raw['error'][$i], 'size' => $raw['size'][$i],
            ];
        }
        return $list;
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }

    public function isJson(): bool
    {
        return str_contains((string) ($this->server['CONTENT_TYPE'] ?? ''), 'application/json');
    }

    public function wantsJson(): bool
    {
        return str_contains((string) $this->header('Accept'), 'application/json')
            || $this->header('X-Requested-With') === 'fetch';
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function isSecure(): bool
    {
        return ($this->server['HTTPS'] ?? '') === 'on' || (int) ($this->server['SERVER_PORT'] ?? 80) === 443;
    }

    public function userAgent(): string
    {
        return substr((string) ($this->server['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }
}

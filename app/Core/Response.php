<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    private array $headers = [];

    public function __construct(private string $body = '', private int $status = 200, array $headers = [])
    {
        $this->headers = $headers;
    }

    public static function html(string $html, int $status = 200): self
    {
        return new self($html, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function view(string $template, array $data = [], int $status = 200, ?string $layout = 'layouts/store'): self
    {
        return self::html(View::render($template, $data, $layout), $status);
    }

    /** The single JSON envelope used by every API endpoint: {ok, data, error}. */
    public static function json(mixed $data = null, int $status = 200, ?string $error = null, array $extra = []): self
    {
        $payload = ['ok' => $status < 400 && $error === null, 'data' => $data, 'error' => $error] + $extra;
        return new self(
            (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8', 'Cache-Control' => 'no-store']
        );
    }

    public static function error(string $message, int $status, array $extra = []): self
    {
        return self::json(null, $status, $message, $extra);
    }

    public static function redirect(string $to, int $status = 302): self
    {
        return new self('', $status, ['Location' => $to]);
    }

    public static function back(string $fallback = '/'): self
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $base = config('app.url');
        return self::redirect(str_starts_with($ref, $base) ? $ref : url($fallback));
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function status(): int { return $this->status; }

    public function send(): void
    {
        if (!headers_sent()) {
            // Apache turns status codes it does not know (419) into 500 unless the reason phrase is explicit.
            $reason = [419 => 'Page Expired'][$this->status] ?? null;
            $reason !== null ? header("HTTP/1.1 {$this->status} {$reason}", true, $this->status) : http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->body;
    }
}

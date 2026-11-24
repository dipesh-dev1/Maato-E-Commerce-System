<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;
use App\Services\Settings;

function config(string $key, mixed $default = null): mixed
{
    static $loaded = [];
    $parts = explode('.', $key);
    $file = array_shift($parts);
    if (!array_key_exists($file, $loaded)) {
        $path = BASE_PATH . "/config/{$file}.php";
        $loaded[$file] = is_file($path) ? require $path : [];
    }
    $value = $loaded[$file];
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path_url(): string
{
    return (string) parse_url(config('app.url'), PHP_URL_PATH);
}

/** Root-relative URL inside the app, e.g. url('/shop', ['page' => 2]) => /suman-ecom/shop?page=2 */
function url(string $path = '/', array $query = []): string
{
    $query = array_filter($query, static fn ($v) => $v !== null && $v !== '' && $v !== []);
    $path = '/' . ltrim($path, '/');
    return base_path_url() . ($path === '/' ? '/' : $path) . ($query ? '?' . http_build_query($query) : '');
}

function route(string $name, array $params = []): string
{
    return App::router()->url($name, $params);
}

function asset(string $path): string
{
    $file = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? filemtime($file) : 0;
    return url('/assets/' . ltrim($path, '/')) . '?v=' . $version;
}

/** Public URL for a file stored under storage/uploads (served by MediaController). */
function media(?string $path, string $fallback = 'img/placeholder.svg'): string
{
    return $path ? url('/media/' . ltrim($path, '/')) : asset($fallback);
}

function icon(string $name, string $class = ''): string
{
    $href = e(asset('icons.svg')) . '#i-' . e($name);
    return '<svg class="icon ' . e($class) . '" aria-hidden="true" focusable="false"><use href="' . $href . '"></use></svg>';
}

function partial(string $template, array $data = []): void
{
    echo View::capture($template, $data);
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

function old(string $key, mixed $default = ''): mixed
{
    $old = Session::getFlash('old', []);
    return array_key_exists($key, $old) ? $old[$key] : $default;
}

function error_for(string $key): ?string
{
    return Session::getFlash('errors', [])[$key] ?? null;
}

/** Renders the inline error for a field with an id the input can reference via aria-describedby. */
function field_error(string $key): string
{
    $message = error_for($key);
    return $message === null ? '' : '<p class="field-error" id="err-' . e($key) . '">' . e($message) . '</p>';
}

function aria_invalid(string $key): string
{
    return error_for($key) === null ? '' : ' aria-invalid="true" aria-describedby="err-' . e($key) . '"';
}

function flash(string $type, string $message): void
{
    Session::flash('toast', ['type' => $type, 'message' => $message]);
}

function auth_user(): ?array
{
    return Auth::user();
}

function can(string $ability): bool
{
    return Auth::can($ability);
}

function setting(string $key, mixed $default = null): mixed
{
    return Settings::get($key, $default);
}

/** Groups digits the Nepali/Indian way: 1,25,000 and 12,34,567. */
function nepali_group(string $integerDigits): string
{
    if (strlen($integerDigits) <= 3) {
        return $integerDigits;
    }
    $last3 = substr($integerDigits, -3);
    $rest = substr($integerDigits, 0, -3);
    return preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3;
}

function money(float|int|string|null $amount, bool $withSymbol = true): string
{
    $amount = round((float) $amount, 2);
    $negative = $amount < 0;
    [$int, $dec] = explode('.', number_format(abs($amount), 2, '.', ''));
    $formatted = nepali_group($int) . ($dec !== '00' ? '.' . $dec : '');
    $symbol = $withSymbol ? Settings::get('currency_symbol', 'Rs.') . ' ' : '';
    return ($negative ? '−' : '') . $symbol . $formatted;
}

function format_date(?string $datetime, string $format = 'j M Y'): string
{
    return $datetime ? date($format, strtotime($datetime)) : '—';
}

function plural(int $count, string $singular, ?string $plural = null): string
{
    return $count . ' ' . ($count === 1 ? $singular : ($plural ?? $singular . 's'));
}

function is_current(string $path, bool $prefix = false): bool
{
    $current = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $target = rtrim(url($path), '/');
    return $prefix ? str_starts_with($current, $target) : $current === $target;
}

function slugify(string $text): string
{
    $text = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text), '-'));
    return $text !== '' ? $text : 'item';
}

function str_limit(string $text, int $limit): string
{
    return mb_strlen($text) > $limit ? rtrim(mb_substr($text, 0, $limit - 1)) . '…' : $text;
}

/** Sortable admin table header. Keeps current filters, flips direction on the active column. */
function sort_th(string $label, string $key, array $filters, string $class = ''): string
{
    $active = ($filters['sort'] ?? '') === $key;
    $dir = $active && ($filters['dir'] ?? 'desc') === 'asc' ? 'desc' : 'asc';
    $query = array_merge(array_filter($filters, static fn ($v) => $v !== null && $v !== ''), ['sort' => $key, 'dir' => $dir]);
    unset($query['page']);
    $href = strtok($_SERVER['REQUEST_URI'] ?? '', '?') . '?' . http_build_query($query);
    $aria = $active ? ' aria-sort="' . (($filters['dir'] ?? 'desc') === 'asc' ? 'ascending' : 'descending') . '"' : '';
    $arrow = $active ? icon(($filters['dir'] ?? 'desc') === 'asc' ? 'chevron-up' : 'chevron-down') : '';
    return '<th scope="col" class="' . e($class) . '"' . $aria . '><a href="' . e($href) . '" data-table-link>' . e($label) . $arrow . '</a></th>';
}

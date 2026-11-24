<?php
declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(Request $request): void
    {
        $cfg = config('app.session');
        $secure = $cfg['secure'] === 'auto' ? $request->isSecure() : filter_var($cfg['secure'], FILTER_VALIDATE_BOOLEAN);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', (string) ($cfg['lifetime'] * 60));
        session_name($cfg['name']);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => (parse_url(config('app.url'), PHP_URL_PATH) ?: '') . '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        $last = $_SESSION['_last_activity'] ?? null;
        if ($last !== null && time() - $last > $cfg['lifetime'] * 60) {
            self::invalidate();
        }
        $_SESSION['_last_activity'] = time();

        // Flash data lives for exactly one subsequent request.
        $_SESSION['_flash_old'] = $_SESSION['_flash_new'] ?? [];
        $_SESSION['_flash_new'] = [];
    }

    public static function get(string $key, mixed $default = null): mixed { return $_SESSION[$key] ?? $default; }
    public static function set(string $key, mixed $value): void { $_SESSION[$key] = $value; }
    public static function forget(string $key): void { unset($_SESSION[$key]); }

    public static function flash(string $key, mixed $value): void { $_SESSION['_flash_new'][$key] = $value; }
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_new'][$key] ?? $_SESSION['_flash_old'][$key] ?? $default;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
        unset($_SESSION['_csrf']);
    }

    /** Wipes everything (logout, idle timeout) and issues a fresh id. */
    public static function invalidate(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}

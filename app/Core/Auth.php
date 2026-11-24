<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\UserRepository;

final class Auth
{
    private static ?array $user = null;
    private static bool $resolved = false;

    public static function user(): ?array
    {
        if (!self::$resolved) {
            self::$resolved = true;
            $id = Session::get('user_id');
            if ($id !== null) {
                $user = (new UserRepository())->find((int) $id);
                // A blocked or deleted account loses its session on the very next request.
                if ($user === null || $user['is_blocked']) {
                    self::logout();
                    return null;
                }
                self::$user = $user;
            }
        }
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        self::$user = $user;
        self::$resolved = true;
    }

    public static function logout(): void
    {
        Session::invalidate();
        self::$user = null;
        self::$resolved = true;
    }

    public static function refresh(): void
    {
        self::$resolved = false;
        self::$user = null;
    }

    public static function role(): ?string
    {
        return self::user()['role_slug'] ?? null;
    }

    public static function isBackOffice(): bool
    {
        return in_array(self::role(), ['admin', 'staff'], true);
    }

    public static function can(string $ability): bool
    {
        $granted = config('permissions.' . (self::role() ?? 'guest'), []);
        return in_array('*', $granted, true) || in_array($ability, $granted, true);
    }
}

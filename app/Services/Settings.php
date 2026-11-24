<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\SettingRepository;

final class Settings
{
    private static ?array $cache = null;

    public static function all(): array
    {
        return self::$cache ??= (new SettingRepository())->allPairs();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::all()[$key] ?? null;
        return $value === null || $value === '' ? $default : $value;
    }

    public static function save(array $pairs): void
    {
        (new SettingRepository())->setMany($pairs);
        self::$cache = null;
    }

    public static function taxRate(): float
    {
        return (float) self::get('tax_rate', 0);
    }

    public static function valleyDistricts(): array
    {
        return array_map('trim', explode(',', (string) self::get('valley_districts', 'Kathmandu,Lalitpur,Bhaktapur')));
    }
}

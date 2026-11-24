<?php
declare(strict_types=1);

namespace App\Models;

final class SettingRepository extends Repository
{
    public function allPairs(): array
    {
        return $this->run('SELECT `key`, value FROM settings')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function setMany(array $pairs): void
    {
        $stmt = $this->db->prepare('INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
        foreach ($pairs as $key => $value) {
            $stmt->execute([$key, $value === null ? null : (string) $value]);
        }
    }
}

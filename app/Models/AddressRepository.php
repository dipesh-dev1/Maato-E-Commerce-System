<?php
declare(strict_types=1);

namespace App\Models;

/** Every read and write is scoped by user_id so one customer can never touch another's address. */
final class AddressRepository extends Repository
{
    public const FIELDS = ['label', 'recipient_name', 'phone', 'line1', 'line2', 'city', 'district', 'province', 'postal_code'];

    public function forUser(int $userId): array
    {
        return $this->all('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id', [$userId]);
    }

    public function findForUser(int $id, int $userId): ?array
    {
        return $this->one('SELECT * FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public function create(int $userId, array $data): int
    {
        $isFirst = !$this->value('SELECT 1 FROM addresses WHERE user_id = ? LIMIT 1', [$userId]);
        $id = $this->insert('addresses', ['user_id' => $userId, 'is_default' => $isFirst ? 1 : 0] + $this->pick($data));
        if (!$isFirst && !empty($data['is_default'])) {
            $this->setDefault($id, $userId);
        }
        return $id;
    }

    public function update(int $id, int $userId, array $data): void
    {
        $fields = $this->pick($data);
        $set = implode(', ', array_map(static fn ($c) => "`{$c}` = ?", array_keys($fields)));
        $this->run("UPDATE addresses SET {$set} WHERE id = ? AND user_id = ?", [...array_values($fields), $id, $userId]);
        if (!empty($data['is_default'])) {
            $this->setDefault($id, $userId);
        }
    }

    public function delete(int $id, int $userId): void
    {
        $wasDefault = (bool) $this->value('SELECT is_default FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId]);
        $this->run('DELETE FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId]);
        if ($wasDefault) {
            $this->run('UPDATE addresses SET is_default = 1 WHERE user_id = ? ORDER BY id LIMIT 1', [$userId]);
        }
    }

    public function setDefault(int $id, int $userId): void
    {
        $this->run('UPDATE addresses SET is_default = (id = ?) WHERE user_id = ?', [$id, $userId]);
    }

    private function pick(array $data): array
    {
        $out = [];
        foreach (self::FIELDS as $f) {
            $out[$f] = ($data[$f] ?? null) === '' ? null : ($data[$f] ?? null);
        }
        return $out;
    }
}

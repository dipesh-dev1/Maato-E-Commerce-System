<?php
declare(strict_types=1);

namespace App\Models;

final class CouponRepository extends Repository
{
    public function findByCode(string $code, bool $lock = false): ?array
    {
        return $this->one('SELECT * FROM coupons WHERE code = ?' . ($lock ? ' FOR UPDATE' : ''), [strtoupper(trim($code))]);
    }

    public function find(int $id): ?array
    {
        return $this->one('SELECT * FROM coupons WHERE id = ?', [$id]);
    }

    public function codeExists(string $code, int $exceptId = 0): bool
    {
        return (bool) $this->value('SELECT 1 FROM coupons WHERE code = ? AND id <> ?', [strtoupper($code), $exceptId]);
    }

    public function incrementUsage(int $id): void
    {
        $this->run('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?', [$id]);
    }

    public function decrementUsage(int $id): void
    {
        $this->run('UPDATE coupons SET used_count = GREATEST(used_count - 1, 0) WHERE id = ?', [$id]);
    }

    public function adminList(array $f, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(code LIKE ? OR description LIKE ?)';
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            array_push($params, $like, $like);
        }
        $state = $f['state'] ?? '';
        if ($state === 'active') {
            $where[] = 'is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (usage_limit IS NULL OR used_count < usage_limit)';
        } elseif ($state === 'expired') {
            $where[] = '(expires_at <= NOW() OR (usage_limit IS NOT NULL AND used_count >= usage_limit))';
        }
        $sorts = ['code' => 'code', 'used' => 'used_count', 'expires' => 'expires_at', 'created' => 'created_at'];
        $order = ($sorts[$f['sort'] ?? ''] ?? 'created_at') . ' ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', id DESC';
        return $this->paginate('*', 'FROM coupons WHERE ' . implode(' AND ', $where), $params, $order, $page, 20);
    }

    public function create(array $data): int
    {
        return $this->insert('coupons', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->updateRow('coupons', $id, $data);
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM coupons WHERE id = ?', [$id]);
    }
}

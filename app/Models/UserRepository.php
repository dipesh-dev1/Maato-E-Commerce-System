<?php
declare(strict_types=1);

namespace App\Models;

final class UserRepository extends Repository
{
    private const SELECT = 'SELECT u.*, r.slug AS role_slug, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id';

    public function find(int $id): ?array
    {
        return $this->one(self::SELECT . ' WHERE u.id = ?', [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->one(self::SELECT . ' WHERE u.email = ?', [mb_strtolower($email)]);
    }

    public function emailTaken(string $email, ?int $exceptId = null): bool
    {
        return (bool) $this->value('SELECT 1 FROM users WHERE email = ? AND id <> ?', [mb_strtolower($email), $exceptId ?? 0]);
    }

    public function create(string $name, string $email, string $passwordHash, ?string $phone = null): int
    {
        $roleId = (int) $this->value("SELECT id FROM roles WHERE slug = 'customer'");
        return $this->insert('users', [
            'role_id' => $roleId, 'name' => $name, 'email' => mb_strtolower($email),
            'phone' => $phone, 'password_hash' => $passwordHash,
        ]);
    }

    public function updateProfile(int $id, string $name, ?string $phone): void
    {
        $this->updateRow('users', $id, ['name' => $name, 'phone' => $phone]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $this->updateRow('users', $id, ['password_hash' => $hash]);
    }

    public function updateAvatar(int $id, ?string $path): void
    {
        $this->updateRow('users', $id, ['avatar_path' => $path]);
    }

    public function touchLogin(int $id): void
    {
        $this->run('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    public function setBlocked(int $id, bool $blocked): void
    {
        $this->updateRow('users', $id, ['is_blocked' => $blocked ? 1 : 0]);
    }

    public function setRole(int $id, string $roleSlug): void
    {
        $this->run('UPDATE users SET role_id = (SELECT id FROM roles WHERE slug = ?) WHERE id = ?', [$roleSlug, $id]);
    }

    public function roles(): array
    {
        return $this->all('SELECT * FROM roles ORDER BY id');
    }

    public function countSince(string $since, string $role = 'customer'): int
    {
        return (int) $this->value(
            'SELECT COUNT(*) FROM users u JOIN roles r ON r.id = u.role_id WHERE r.slug = ? AND u.created_at >= ?',
            [$role, $since]
        );
    }

    public function adminList(array $f, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if (!empty($f['role'])) {
            $where[] = 'r.slug = ?';
            $params[] = $f['role'];
        }
        if (($f['state'] ?? '') === 'blocked') {
            $where[] = 'u.is_blocked = 1';
        }
        $sorts = [
            'name' => 'u.name', 'email' => 'u.email', 'created' => 'u.created_at',
            'orders' => 'order_count', 'spent' => 'total_spent', 'login' => 'u.last_login_at',
        ];
        $order = ($sorts[$f['sort'] ?? ''] ?? 'u.created_at') . ' ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', u.id DESC';
        $select = "u.id, u.name, u.email, u.phone, u.is_blocked, u.created_at, u.last_login_at, r.slug AS role_slug, r.name AS role_name,
            (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count,
            (SELECT COALESCE(SUM(o.grand_total), 0) FROM orders o WHERE o.user_id = u.id AND o.status <> 'cancelled') AS total_spent";
        $from = 'FROM users u JOIN roles r ON r.id = u.role_id WHERE ' . implode(' AND ', $where);
        return $this->paginate($select, $from, $params, $order, $page, 20);
    }
}

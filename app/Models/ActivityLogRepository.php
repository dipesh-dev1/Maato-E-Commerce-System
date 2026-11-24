<?php
declare(strict_types=1);

namespace App\Models;

final class ActivityLogRepository extends Repository
{
    public function create(array $entry): void
    {
        $this->insert('activity_logs', $entry);
    }

    public function adminList(array $f, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(a.description LIKE ? OR a.action LIKE ? OR u.name LIKE ?)';
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            array_push($params, $like, $like, $like);
        }
        if (!empty($f['entity'])) {
            $where[] = 'a.entity_type = ?';
            $params[] = $f['entity'];
        }
        if (!empty($f['user'])) {
            $where[] = 'a.user_id = ?';
            $params[] = (int) $f['user'];
        }
        $order = 'a.created_at ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', a.id DESC';
        return $this->paginate(
            'a.*, u.name AS user_name, r.slug AS role_slug',
            'FROM activity_logs a LEFT JOIN users u ON u.id = a.user_id LEFT JOIN roles r ON r.id = u.role_id WHERE ' . implode(' AND ', $where),
            $params,
            $order,
            $page,
            25
        );
    }

    public function entityTypes(): array
    {
        return $this->run('SELECT DISTINCT entity_type FROM activity_logs ORDER BY entity_type')->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function actors(): array
    {
        return $this->all('SELECT DISTINCT u.id, u.name FROM activity_logs a JOIN users u ON u.id = a.user_id ORDER BY u.name');
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

final class ReviewRepository extends Repository
{
    public function approvedForProduct(int $productId, int $limit = 20): array
    {
        return $this->all(
            "SELECT r.*, u.name AS author FROM reviews r JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ? AND r.status = 'approved' ORDER BY r.created_at DESC LIMIT " . (int) $limit,
            [$productId]
        );
    }

    /** @return array{avg: float, count: int, bars: array<int,int>} */
    public function summary(int $productId): array
    {
        $rows = $this->run(
            "SELECT rating, COUNT(*) FROM reviews WHERE product_id = ? AND status = 'approved' GROUP BY rating",
            [$productId]
        )->fetchAll(\PDO::FETCH_KEY_PAIR);
        $bars = [];
        $count = 0;
        $sum = 0;
        for ($star = 5; $star >= 1; $star--) {
            $n = (int) ($rows[$star] ?? 0);
            $bars[$star] = $n;
            $count += $n;
            $sum += $n * $star;
        }
        return ['avg' => $count ? round($sum / $count, 1) : 0.0, 'count' => $count, 'bars' => $bars];
    }

    public function findByUser(int $productId, int $userId): ?array
    {
        return $this->one('SELECT * FROM reviews WHERE product_id = ? AND user_id = ?', [$productId, $userId]);
    }

    /** Reviews are only open to customers who have had the product delivered. */
    public function userReceivedProduct(int $userId, int $productId): bool
    {
        return (bool) $this->value(
            "SELECT 1 FROM order_items oi JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = ? AND o.status = 'delivered' AND oi.product_id = ? LIMIT 1",
            [$userId, $productId]
        );
    }

    public function create(int $productId, int $userId, int $rating, string $title, string $body): int
    {
        return $this->insert('reviews', [
            'product_id' => $productId, 'user_id' => $userId, 'rating' => $rating,
            'title' => $title, 'body' => $body, 'status' => 'pending',
        ]);
    }

    public function adminList(array $f, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (in_array($f['status'] ?? '', ['pending', 'approved', 'rejected'], true)) {
            $where[] = 'r.status = ?';
            $params[] = $f['status'];
        }
        if (!empty($f['q'])) {
            $where[] = '(p.name LIKE ? OR u.name LIKE ? OR r.title LIKE ? OR r.body LIKE ?)';
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            array_push($params, $like, $like, $like, $like);
        }
        $sorts = ['rating' => 'r.rating', 'product' => 'p.name', 'date' => 'r.created_at', 'status' => 'r.status'];
        $order = ($sorts[$f['sort'] ?? ''] ?? 'r.created_at') . ' ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', r.id DESC';
        return $this->paginate(
            'r.*, p.name AS product_name, p.slug AS product_slug, u.name AS author, u.email AS author_email',
            'FROM reviews r JOIN products p ON p.id = r.product_id JOIN users u ON u.id = r.user_id WHERE ' . implode(' AND ', $where),
            $params,
            $order,
            $page,
            20
        );
    }

    public function setStatus(array $ids, string $status): int
    {
        $in = implode(',', array_fill(0, count($ids), '?'));
        return $this->run("UPDATE reviews SET status = ? WHERE id IN ({$in})", [$status, ...$ids])->rowCount();
    }

    public function delete(array $ids): int
    {
        $in = implode(',', array_fill(0, count($ids), '?'));
        return $this->run("DELETE FROM reviews WHERE id IN ({$in})", $ids)->rowCount();
    }

    public function pendingCount(): int
    {
        return (int) $this->value("SELECT COUNT(*) FROM reviews WHERE status = 'pending'");
    }
}

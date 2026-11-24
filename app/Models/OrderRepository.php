<?php
declare(strict_types=1);

namespace App\Models;

final class OrderRepository extends Repository
{
    public function create(array $data): int
    {
        return $this->insert('orders', $data);
    }

    public function addItem(int $orderId, array $item): void
    {
        $this->insert('order_items', ['order_id' => $orderId] + $item);
    }

    public function addHistory(int $orderId, string $status, ?string $note, ?int $changedBy): void
    {
        $this->insert('order_status_history', [
            'order_id' => $orderId, 'status' => $status, 'note' => $note, 'changed_by' => $changedBy,
        ]);
    }

    public function numberExists(string $number): bool
    {
        return (bool) $this->value('SELECT 1 FROM orders WHERE order_number = ?', [$number]);
    }

    /** Scoped lookup for the customer area: another customer's order number simply 404s. */
    public function findForUser(string $number, int $userId): ?array
    {
        return $this->one('SELECT * FROM orders WHERE order_number = ? AND user_id = ?', [$number, $userId]);
    }

    public function find(int $id, bool $lock = false): ?array
    {
        return $this->one('SELECT * FROM orders WHERE id = ?' . ($lock ? ' FOR UPDATE' : ''), [$id]);
    }

    public function findByNumber(string $number): ?array
    {
        return $this->one('SELECT * FROM orders WHERE order_number = ?', [$number]);
    }

    public function items(int $orderId): array
    {
        return $this->all(
            'SELECT oi.*, p.slug AS product_slug FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id AND p.deleted_at IS NULL AND p.status = \'published\'
             WHERE oi.order_id = ? ORDER BY oi.id',
            [$orderId]
        );
    }

    public function history(int $orderId): array
    {
        return $this->all(
            'SELECT h.*, u.name AS changed_by_name FROM order_status_history h
             LEFT JOIN users u ON u.id = h.changed_by WHERE h.order_id = ? ORDER BY h.created_at, h.id',
            [$orderId]
        );
    }

    public function setStatus(int $id, string $status): void
    {
        $extra = match ($status) {
            'delivered' => ', delivered_at = NOW()',
            'cancelled' => ', cancelled_at = NOW()',
            default     => '',
        };
        $this->run("UPDATE orders SET status = ?{$extra} WHERE id = ?", [$status, $id]);
    }

    public function listForUser(int $userId, int $page): array
    {
        return $this->paginate(
            'o.*, (SELECT SUM(quantity) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
             (SELECT image_path FROM order_items oi WHERE oi.order_id = o.id ORDER BY oi.id LIMIT 1) AS image',
            'FROM orders o WHERE o.user_id = ?',
            [$userId],
            'o.created_at DESC, o.id DESC',
            $page,
            10
        );
    }

    /** @return array{0:string,1:array} WHERE clause and params shared by the admin table and the CSV export */
    private function adminWhere(array $f): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.ship_phone LIKE ?)';
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if (isset(OrderStatus::LABELS[$f['status'] ?? ''])) {
            $where[] = 'o.status = ?';
            $params[] = $f['status'];
        }
        if (isset(OrderStatus::PAYMENT_LABELS[$f['payment'] ?? ''])) {
            $where[] = 'o.payment_method = ?';
            $params[] = $f['payment'];
        }
        if (!empty($f['from']) && strtotime($f['from'])) {
            $where[] = 'o.created_at >= ?';
            $params[] = date('Y-m-d 00:00:00', strtotime($f['from']));
        }
        if (!empty($f['to']) && strtotime($f['to'])) {
            $where[] = 'o.created_at <= ?';
            $params[] = date('Y-m-d 23:59:59', strtotime($f['to']));
        }
        if (!empty($f['user_id'])) {
            $where[] = 'o.user_id = ?';
            $params[] = (int) $f['user_id'];
        }
        return [implode(' AND ', $where), $params];
    }

    public function adminList(array $f, int $page, int $perPage = 20): array
    {
        [$where, $params] = $this->adminWhere($f);
        $sorts = ['number' => 'o.order_number', 'customer' => 'o.customer_name', 'total' => 'o.grand_total', 'status' => 'o.status', 'date' => 'o.created_at'];
        $order = ($sorts[$f['sort'] ?? ''] ?? 'o.created_at') . ' ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', o.id DESC';
        return $this->paginate(
            'o.id, o.order_number, o.user_id, o.status, o.payment_method, o.grand_total, o.customer_name, o.customer_email, o.ship_city, o.created_at,
             (SELECT SUM(quantity) FROM order_items oi WHERE oi.order_id = o.id) AS item_count',
            "FROM orders o WHERE {$where}",
            $params,
            $order,
            $page,
            $perPage
        );
    }

    /** Streams rows for CSV export without loading the whole table into memory. */
    public function exportCursor(array $f): \PDOStatement
    {
        [$where, $params] = $this->adminWhere($f);
        return $this->run(
            "SELECT o.order_number, o.created_at, o.status, o.payment_method, o.customer_name, o.customer_email, o.ship_phone,
                    o.ship_city, o.ship_district, o.subtotal, o.discount_total, o.shipping_total, o.tax_total, o.grand_total, o.coupon_code,
                    (SELECT SUM(quantity) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
             FROM orders o WHERE {$where} ORDER BY o.created_at DESC",
            $params
        );
    }

    // ---- Dashboard ----

    public function revenueSince(string $since): float
    {
        return (float) $this->value("SELECT COALESCE(SUM(grand_total), 0) FROM orders WHERE status <> 'cancelled' AND created_at >= ?", [$since]);
    }

    public function countSince(string $since): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM orders WHERE created_at >= ?', [$since]);
    }

    public function countByStatus(): array
    {
        return array_map('intval', $this->run('SELECT status, COUNT(*) FROM orders GROUP BY status')->fetchAll(\PDO::FETCH_KEY_PAIR));
    }

    /** Daily revenue and order counts for the last $days days, zero-filled. */
    public function dailySales(int $days): array
    {
        $since = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $rows = $this->all(
            "SELECT DATE(created_at) AS day, COALESCE(SUM(grand_total), 0) AS revenue, COUNT(*) AS orders
             FROM orders WHERE status <> 'cancelled' AND created_at >= ? GROUP BY DATE(created_at)",
            [$since]
        );
        $byDay = array_column($rows, null, 'day');
        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $day = date('Y-m-d', strtotime("{$since} +{$i} days"));
            $series[] = [
                'date' => $day,
                'revenue' => round((float) ($byDay[$day]['revenue'] ?? 0), 2),
                'orders' => (int) ($byDay[$day]['orders'] ?? 0),
            ];
        }
        return $series;
    }

    public function topProducts(int $days, int $limit = 5): array
    {
        return $this->all(
            "SELECT oi.product_id, oi.product_name, SUM(oi.quantity) AS units, SUM(oi.line_total) AS revenue,
                    MAX(oi.image_path) AS image
             FROM order_items oi JOIN orders o ON o.id = oi.order_id
             WHERE o.status <> 'cancelled' AND o.created_at >= ?
             GROUP BY oi.product_id, oi.product_name ORDER BY revenue DESC LIMIT " . (int) $limit,
            [date('Y-m-d', strtotime("-{$days} days"))]
        );
    }

    public function recent(int $limit = 6): array
    {
        return $this->all(
            'SELECT id, order_number, status, grand_total, customer_name, created_at FROM orders ORDER BY created_at DESC LIMIT ' . (int) $limit
        );
    }
}

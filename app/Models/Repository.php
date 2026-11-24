<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOStatement;

/**
 * Thin PDO helpers. Table and column names passed to insert()/updateRow() always come from code,
 * never from request input; values are always bound.
 */
abstract class Repository
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::pdo();
    }

    protected function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    protected function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    protected function value(string $sql, array $params = []): mixed
    {
        $value = $this->run($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    protected function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(static fn ($c) => "`{$c}`", $columns)),
            implode(', ', array_fill(0, count($columns), '?'))
        );
        $this->run($sql, array_values($data));
        return (int) $this->db->lastInsertId();
    }

    protected function updateRow(string $table, int $id, array $data): void
    {
        if (!$data) {
            return;
        }
        $set = implode(', ', array_map(static fn ($c) => "`{$c}` = ?", array_keys($data)));
        $this->run("UPDATE {$table} SET {$set} WHERE id = ?", [...array_values($data), $id]);
    }

    /**
     * @param string $select columns
     * @param string $from   FROM ... WHERE ... (no ORDER/LIMIT)
     * @param string $order  a whitelisted ORDER BY clause body
     */
    protected function paginate(string $select, string $from, array $params, string $order, int $page, int $perPage): array
    {
        $total = (int) $this->value("SELECT COUNT(*) FROM (SELECT 1 {$from}) counted", $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;
        $items = $this->all("SELECT {$select} {$from} ORDER BY {$order} LIMIT " . (int) $perPage . ' OFFSET ' . (int) $offset, $params);
        return ['items' => $items, 'total' => $total, 'page' => $page, 'pages' => $pages, 'per_page' => $perPage];
    }
}

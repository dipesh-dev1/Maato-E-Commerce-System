<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Models\ActivityLogRepository;

/** Audit trail for back-office actions: who did what, to which record, and when. */
final class ActivityLogger
{
    public static function log(string $action, string $entityType, ?int $entityId, string $description, array $meta = []): void
    {
        (new ActivityLogRepository())->create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => mb_substr($description, 0, 255),
            'meta'        => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'ip_address'  => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
        ]);
    }
}

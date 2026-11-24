<?php
declare(strict_types=1);

namespace App\Services;

/** Local-development mailer: messages are appended to storage/logs/mail.log instead of being sent. */
final class Mailer
{
    public static function send(string $to, string $subject, string $body): void
    {
        $entry = sprintf(
            "=== %s ===\nTo: %s\nSubject: %s\n\n%s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $body
        );
        file_put_contents(BASE_PATH . '/storage/logs/mail.log', $entry, FILE_APPEND | LOCK_EX);
    }
}

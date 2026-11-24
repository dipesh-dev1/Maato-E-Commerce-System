<?php
declare(strict_types=1);

namespace App\Models;

final class LoginAttemptRepository extends Repository
{
    public function record(string $email, string $ip, bool $successful): void
    {
        $this->insert('login_attempts', ['email' => mb_strtolower($email), 'ip_address' => $ip, 'successful' => $successful ? 1 : 0]);
    }

    /** Failures for this email from this IP since the window opened or since the last success, whichever is later. */
    public function recentFailures(string $email, string $ip, string $since): int
    {
        $email = mb_strtolower($email);
        return (int) $this->value(
            'SELECT COUNT(*) FROM login_attempts
             WHERE email = ? AND ip_address = ? AND successful = 0
               AND attempted_at > GREATEST(?, COALESCE((SELECT MAX(attempted_at) FROM login_attempts
                    WHERE email = ? AND ip_address = ? AND successful = 1), \'1970-01-01\'))',
            [$email, $ip, $since, $email, $ip]
        );
    }

    public function recentIpFailures(string $ip, string $since): int
    {
        return (int) $this->value(
            "SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND successful = 0 AND attempted_at > ? AND email NOT LIKE 'reset:%'",
            [$ip, $since]
        );
    }

    public function recentResetRequests(string $ip, string $since): int
    {
        return (int) $this->value(
            "SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > ? AND email LIKE 'reset:%'",
            [$ip, $since]
        );
    }

    public function oldestFailureSince(string $email, string $ip, string $since): ?string
    {
        return $this->value(
            'SELECT MIN(attempted_at) FROM login_attempts WHERE email = ? AND ip_address = ? AND successful = 0 AND attempted_at > ?',
            [mb_strtolower($email), $ip, $since]
        );
    }

    public function purgeOlderThan(string $before): void
    {
        $this->run('DELETE FROM login_attempts WHERE attempted_at < ?', [$before]);
    }
}

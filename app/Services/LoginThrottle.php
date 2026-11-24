<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LoginAttemptRepository;

/** Locks out an email+IP pair after repeated failures, and an IP that sprays many emails. */
final class LoginThrottle
{
    private LoginAttemptRepository $attempts;

    public function __construct()
    {
        $this->attempts = new LoginAttemptRepository();
    }

    /** @return int seconds until the next attempt is allowed; 0 when not locked */
    public function lockedFor(string $email, string $ip): int
    {
        $cfg = config('app.auth');
        $windowStart = date('Y-m-d H:i:s', time() - $cfg['window_minutes'] * 60);
        $locked = $this->attempts->recentFailures($email, $ip, $windowStart) >= $cfg['max_attempts']
            || $this->attempts->recentIpFailures($ip, $windowStart) >= $cfg['max_ip_attempts'];
        if (!$locked) {
            return 0;
        }
        $oldest = $this->attempts->oldestFailureSince($email, $ip, $windowStart);
        $unlockAt = ($oldest ? strtotime($oldest) : time()) + $cfg['window_minutes'] * 60;
        return max(60, $unlockAt - time());
    }

    public function hit(string $email, string $ip, bool $success): void
    {
        $this->attempts->record($email, $ip, $success);
        if (random_int(1, 50) === 1) {
            $this->attempts->purgeOlderThan(date('Y-m-d H:i:s', strtotime('-7 days')));
        }
    }
}

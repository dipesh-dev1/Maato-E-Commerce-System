<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LoginAttemptRepository;
use App\Models\PasswordResetRepository;
use App\Models\UserRepository;

final class PasswordResetService
{
    private const MAX_REQUESTS_PER_IP = 5;

    /** Always behaves the same from the outside, whether or not the email has an account. */
    public function request(string $email, string $ip): bool
    {
        $email = mb_strtolower(trim($email));
        $attempts = new LoginAttemptRepository();
        $since = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        if ($attempts->recentResetRequests($ip, $since) >= self::MAX_REQUESTS_PER_IP) {
            return false;
        }
        $attempts->record('reset:' . $email, $ip, false);

        $user = (new UserRepository())->findByEmail($email);
        if ($user === null || $user['is_blocked']) {
            return true;
        }

        $token = bin2hex(random_bytes(32));
        $ttl = (int) config('app.auth.reset_ttl_minutes');
        (new PasswordResetRepository())->create((int) $user['id'], hash('sha256', $token), date('Y-m-d H:i:s', time() + $ttl * 60));

        $link = config('app.url') . '/reset-password/' . $token;
        Mailer::send(
            $user['email'],
            'Reset your ' . Settings::get('store_name', 'Maato') . ' password',
            "Hi {$user['name']},\n\nSomeone (hopefully you) asked to reset the password for this account.\n"
            . "Open this link within {$ttl} minutes to choose a new one:\n\n{$link}\n\n"
            . "If it wasn't you, you can ignore this email; your password won't change."
        );
        return true;
    }

    public function isValid(string $token): bool
    {
        return ctype_xdigit($token) && (new PasswordResetRepository())->findValid(hash('sha256', $token)) !== null;
    }

    public function reset(string $token, string $password): bool
    {
        if (!ctype_xdigit($token)) {
            return false;
        }
        $resets = new PasswordResetRepository();
        $row = $resets->findValid(hash('sha256', $token));
        if ($row === null || !$resets->consume((int) $row['id'])) {
            return false;
        }
        (new UserRepository())->updatePassword((int) $row['user_id'], AuthService::hash($password));
        return true;
    }
}

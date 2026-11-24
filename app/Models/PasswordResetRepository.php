<?php
declare(strict_types=1);

namespace App\Models;

final class PasswordResetRepository extends Repository
{
    public function create(int $userId, string $tokenHash, string $expiresAt): void
    {
        // Only the newest link should work.
        $this->run('DELETE FROM password_resets WHERE user_id = ?', [$userId]);
        $this->insert('password_resets', ['user_id' => $userId, 'token_hash' => $tokenHash, 'expires_at' => $expiresAt]);
    }

    public function findValid(string $tokenHash): ?array
    {
        return $this->one(
            'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()',
            [$tokenHash]
        );
    }

    public function consume(int $id): bool
    {
        return $this->run('UPDATE password_resets SET used_at = NOW() WHERE id = ? AND used_at IS NULL', [$id])->rowCount() === 1;
    }
}

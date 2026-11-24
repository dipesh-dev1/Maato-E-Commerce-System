<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Session;
use App\Models\UserRepository;

final class AuthService
{
    public const BAD_CREDENTIALS = "That email and password don't match our records.";

    /** A real bcrypt hash of a random string: verifying against it costs the same time as a real account. */
    private const DUMMY_HASH = '$2y$12$t.fP16be222325fW8bKwDuiId2OLvuZpFEjEB2VgitY79Z0VhtQ02';

    private UserRepository $users;
    private LoginThrottle $throttle;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->throttle = new LoginThrottle();
    }

    public static function hash(string $password): string
    {
        return password_hash($password, self::algorithm());
    }

    private static function algorithm(): string|int
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    }

    /** @return string|null an error message, or null on success */
    public function attempt(string $email, string $password, string $ip): ?string
    {
        $email = mb_strtolower(trim($email));
        $wait = $this->throttle->lockedFor($email, $ip);
        if ($wait > 0) {
            return sprintf('Too many attempts. Please try again in %d minutes.', (int) ceil($wait / 60));
        }

        $user = $this->users->findByEmail($email);
        $valid = password_verify($password, $user['password_hash'] ?? self::DUMMY_HASH) && $user !== null;

        if (!$valid) {
            $this->throttle->hit($email, $ip, false);
            return self::BAD_CREDENTIALS;
        }
        if ($user['is_blocked']) {
            $this->throttle->hit($email, $ip, false);
            return 'This account is on hold. Please contact us at ' . Settings::get('support_email', '') . '.';
        }

        $this->throttle->hit($email, $ip, true);
        if (password_needs_rehash($user['password_hash'], self::algorithm())) {
            $this->users->updatePassword((int) $user['id'], self::hash($password));
        }
        $this->login($user);
        return null;
    }

    public function register(string $name, string $email, string $password, ?string $phone): array
    {
        $id = $this->users->create($name, $email, self::hash($password), $phone);
        $user = $this->users->find($id);
        $this->login($user);
        return $user;
    }

    /** Regenerates the session, then folds the guest bag (and any applied code) into the account. */
    private function login(array $user): void
    {
        $cart = new CartService();
        $guestItems = $cart->guestItems();
        $coupon = Session::get('coupon_code');
        Auth::login($user);
        $this->users->touchLogin((int) $user['id']);
        $cart->mergeGuestItems((int) $user['id'], $guestItems);
        if ($coupon !== null) {
            Session::set('coupon_code', $coupon);
        }
    }

    public function changePassword(int $userId, string $current, string $new): ?string
    {
        $user = $this->users->find($userId);
        if (!$user || !password_verify($current, $user['password_hash'])) {
            return "Your current password isn't right.";
        }
        $this->users->updatePassword($userId, self::hash($new));
        Session::regenerate();
        return null;
    }
}

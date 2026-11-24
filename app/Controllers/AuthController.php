<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\UserRepository;
use App\Services\AuthService;
use App\Services\PasswordResetService;

final class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        return $this->view('auth/login', ['title' => 'Sign in'], 200, 'layouts/auth');
    }

    public function login(Request $request): Response
    {
        $data = $this->validate($request, ['email' => 'required|email', 'password' => 'required|string|max:200'], ['email' => 'your email', 'password' => 'your password']);
        $error = (new AuthService())->attempt($data['email'], $data['password'], $request->ip());
        if ($error !== null) {
            Session::flash('errors', ['form' => $error]);
            Session::flash('old', ['email' => $data['email']]);
            return $this->redirect(route('login'));
        }
        $user = Auth::user();
        $intended = Session::get('intended');
        Session::forget('intended');
        flash('success', 'Welcome back, ' . explode(' ', $user['name'])[0] . '.');
        if (Auth::isBackOffice() && $intended === null) {
            return $this->redirect(url('/admin'));
        }
        return $this->redirect($this->safeIntended($intended) ?? route('account'));
    }

    public function showRegister(Request $request): Response
    {
        return $this->view('auth/register', ['title' => 'Create an account'], 200, 'layouts/auth');
    }

    public function register(Request $request): Response
    {
        $data = $this->validate($request, [
            'name'     => 'required|string|min:2|max:120',
            'email'    => 'required|email',
            'phone'    => 'nullable|phone',
            'password' => 'required|password|max:200|confirmed',
        ], ['name' => 'your name', 'email' => 'your email', 'password' => 'a password']);

        if ((new UserRepository())->emailTaken($data['email'])) {
            // Deliberately vague: points to sign-in and reset without confirming the account exists.
            Session::flash('errors', ['email' => "We can't create a new account with that email. If you've shopped with us before, sign in or reset your password."]);
            Session::flash('old', ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone']]);
            return $this->redirect(route('register'));
        }
        (new AuthService())->register($data['name'], $data['email'], $data['password'], $data['phone']);
        flash('success', "Welcome to Maato. Your account is ready.");
        $intended = $this->safeIntended(Session::get('intended'));
        Session::forget('intended');
        return $this->redirect($intended ?? route('account'));
    }

    public function logout(Request $request): Response
    {
        Auth::logout();
        flash('info', "You're signed out. See you soon.");
        return $this->redirect(route('home'));
    }

    public function showForgot(Request $request): Response
    {
        return $this->view('auth/forgot', ['title' => 'Reset your password'], 200, 'layouts/auth');
    }

    public function sendReset(Request $request): Response
    {
        $data = $this->validate($request, ['email' => 'required|email'], ['email' => 'your email']);
        $accepted = (new PasswordResetService())->request($data['email'], $request->ip());
        if (!$accepted) {
            Session::flash('errors', ['form' => 'Too many reset requests from this connection. Please wait 15 minutes.']);
            return $this->redirect(route('password.forgot'));
        }
        Session::flash('sent', $data['email']);
        return $this->redirect(route('password.forgot'));
    }

    public function showReset(Request $request): Response
    {
        $token = (string) $request->param('token');
        return $this->view('auth/reset', [
            'title' => 'Choose a new password',
            'token' => $token,
            'valid' => (new PasswordResetService())->isValid($token),
        ], 200, 'layouts/auth');
    }

    public function reset(Request $request): Response
    {
        $token = (string) $request->param('token');
        $data = $this->validate($request, ['password' => 'required|password|max:200|confirmed'], ['password' => 'a new password']);
        if (!(new PasswordResetService())->reset($token, $data['password'])) {
            flash('error', 'That reset link has expired or was already used. Request a new one below.');
            return $this->redirect(route('password.forgot'));
        }
        flash('success', 'Password updated. Sign in with your new password.');
        return $this->redirect(route('login'));
    }

    /** Only same-site paths are honoured, so ?intended can't become an open redirect. */
    private function safeIntended(?string $url): ?string
    {
        if ($url === null || !str_starts_with($url, base_path_url() . '/') || str_starts_with($url, '//')) {
            return null;
        }
        return $url;
    }
}

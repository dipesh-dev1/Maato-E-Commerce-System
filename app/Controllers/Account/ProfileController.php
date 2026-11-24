<?php
declare(strict_types=1);

namespace App\Controllers\Account;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AddressRepository;
use App\Models\OrderRepository;
use App\Models\UserRepository;
use App\Services\AuthService;
use App\Services\ImageUploader;
use App\Services\UploadException;

final class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $this->user();
        return $this->view('account/profile', [
            'title'     => 'Your account',
            'user'      => $user,
            'recent'    => (new OrderRepository())->listForUser((int) $user['id'], 1)['items'],
            'addresses' => (new AddressRepository())->forUser((int) $user['id']),
        ]);
    }

    public function update(Request $request): Response
    {
        $user = $this->user();
        $data = $this->validate($request, [
            'name'  => 'required|string|min:2|max:120',
            'phone' => 'nullable|phone',
        ], ['name' => 'your name']);
        (new UserRepository())->updateProfile((int) $user['id'], $data['name'], $data['phone']);
        flash('success', 'Profile saved.');
        return $this->redirect(route('account') . '#profile');
    }

    public function password(Request $request): Response
    {
        $user = $this->user();
        $data = $this->validate($request, [
            'current_password' => 'required|string|max:200',
            'password'         => 'required|password|max:200|confirmed',
        ], ['current_password' => 'your current password', 'password' => 'a new password']);
        $error = (new AuthService())->changePassword((int) $user['id'], $data['current_password'], $data['password']);
        if ($error !== null) {
            Session::flash('errors', ['current_password' => $error]);
            return $this->redirect(route('account') . '#security');
        }
        flash('success', 'Password changed.');
        return $this->redirect(route('account') . '#security');
    }

    public function avatar(Request $request): Response
    {
        $user = $this->user();
        $file = $request->file('avatar');
        if ($file === null) {
            Session::flash('errors', ['avatar' => 'Choose a photo first.']);
            return $this->redirect(route('account') . '#profile');
        }
        $uploader = new ImageUploader();
        try {
            $stored = $uploader->store($file, 'avatars', 400, 160);
        } catch (UploadException $e) {
            Session::flash('errors', ['avatar' => $e->getMessage()]);
            return $this->redirect(route('account') . '#profile');
        }
        $this->deleteAvatarFiles($user['avatar_path']);
        (new UserRepository())->updateAvatar((int) $user['id'], $stored['thumb']);
        $uploader->delete($stored['path']);
        flash('success', 'New photo saved.');
        return $this->redirect(route('account') . '#profile');
    }

    public function removeAvatar(Request $request): Response
    {
        $user = $this->user();
        $this->deleteAvatarFiles($user['avatar_path']);
        (new UserRepository())->updateAvatar((int) $user['id'], null);
        flash('success', 'Photo removed.');
        return $this->redirect(route('account') . '#profile');
    }

    private function deleteAvatarFiles(?string $path): void
    {
        if ($path !== null && str_starts_with($path, 'avatars/')) {
            (new ImageUploader())->delete($path);
        }
    }
}

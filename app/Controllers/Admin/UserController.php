<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\AddressRepository;
use App\Models\OrderRepository;
use App\Models\UserRepository;
use App\Services\ActivityLogger;

final class UserController extends AdminController
{
    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['q', 'role', 'state']);
        $users = new UserRepository();
        return $this->table($request, 'users/index', 'users/_table', [
            'title'   => 'Users',
            'crumbs'  => [['Users', null]],
            'filters' => $filters,
            'result'  => $users->adminList($filters, $this->pageNumber($request)),
            'roles'   => $users->roles(),
        ]);
    }

    public function show(Request $request): Response
    {
        $users = new UserRepository();
        $user = $this->notFoundUnless($users->find((int) $request->param('id')));
        $orders = (new OrderRepository())->adminList(['user_id' => $user['id']], $this->pageNumber($request), 10);
        return $this->page('users/show', [
            'title'     => $user['name'],
            'crumbs'    => [['Users', route('admin.users')], [$user['name'], null]],
            'user'      => $user,
            'roles'     => $users->roles(),
            'orders'    => $orders,
            'addresses' => (new AddressRepository())->forUser((int) $user['id']),
            'isSelf'    => (int) $user['id'] === Auth::id(),
        ]);
    }

    public function toggleBlock(Request $request): Response
    {
        $user = $this->target($request);
        if ($user === null) {
            return $this->fail($request, "You can't block your own account.", route('admin.users'));
        }
        $block = !$user['is_blocked'];
        (new UserRepository())->setBlocked((int) $user['id'], $block);
        ActivityLogger::log($block ? 'user.blocked' : 'user.unblocked', 'user', (int) $user['id'], ($block ? 'Blocked ' : 'Unblocked ') . $user['email']);
        $message = $block ? "{$user['name']} is blocked and signed out everywhere." : "{$user['name']} can sign in again.";
        return $this->done($request, $message, route('admin.users.show', ['id' => $user['id']]));
    }

    public function role(Request $request): Response
    {
        $user = $this->target($request);
        if ($user === null) {
            return $this->fail($request, "You can't change your own role. Ask another admin.", route('admin.users'));
        }
        $data = $this->validate($request, ['role' => 'required|in:customer,staff,admin'], ['role' => 'a role']);
        if ($data['role'] === $user['role_slug']) {
            return $this->done($request, 'Role unchanged.', route('admin.users.show', ['id' => $user['id']]));
        }
        (new UserRepository())->setRole((int) $user['id'], $data['role']);
        ActivityLogger::log('user.role', 'user', (int) $user['id'], "Changed {$user['email']} from {$user['role_slug']} to {$data['role']}");
        return $this->done($request, "{$user['name']} is now " . ($data['role'] === 'admin' ? 'an admin' : 'a ' . $data['role']) . '.', route('admin.users.show', ['id' => $user['id']]));
    }

    /** The user being acted on, or null when it's the signed-in admin (no self-lockout). */
    private function target(Request $request): ?array
    {
        $user = $this->notFoundUnless((new UserRepository())->find((int) $request->param('id')));
        return (int) $user['id'] === Auth::id() ? null : $user;
    }
}

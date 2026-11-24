<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class RedirectIfAuthenticated
{
    public function handle(Request $request, callable $next, ?string $arg = null): Response
    {
        if (Auth::check()) {
            return Response::redirect(Auth::isBackOffice() ? url('/admin') : url('/account'));
        }
        return $next($request);
    }
}

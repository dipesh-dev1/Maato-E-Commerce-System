<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;

/** Gate for the whole /admin area: signed in, and admin or staff. */
final class BackOffice
{
    public function handle(Request $request, callable $next, ?string $arg = null): Response
    {
        if (!Auth::check()) {
            throw new HttpException(401);
        }
        if (!Auth::isBackOffice()) {
            throw new HttpException(403);
        }
        return $next($request);
    }
}

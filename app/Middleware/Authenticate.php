<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;

final class Authenticate
{
    public function handle(Request $request, callable $next, ?string $arg = null): Response
    {
        if (!Auth::check()) {
            throw new HttpException(401);
        }
        return $next($request);
    }
}

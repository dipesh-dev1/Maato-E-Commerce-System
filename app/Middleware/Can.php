<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;

final class Can
{
    public function handle(Request $request, callable $next, ?string $ability = null): Response
    {
        if ($ability === null || !Auth::can($ability)) {
            throw new HttpException(403);
        }
        return $next($request);
    }
}

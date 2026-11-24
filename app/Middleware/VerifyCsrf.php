<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;

final class VerifyCsrf
{
    public function handle(Request $request, callable $next, ?string $arg = null): Response
    {
        if ($request->isMethod('POST', 'PUT', 'PATCH', 'DELETE') && !Csrf::verify($request)) {
            throw new HttpException(419);
        }
        return $next($request);
    }
}

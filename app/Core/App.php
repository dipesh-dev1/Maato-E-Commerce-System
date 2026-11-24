<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\MediaController;
use App\Middleware\SecurityHeaders;
use App\Middleware\VerifyCsrf;
use Throwable;

final class App
{
    private static ?Router $router = null;

    /** Route-level middleware aliases. "can:orders.manage" passes "orders.manage" as the argument. */
    private const MIDDLEWARE = [
        'auth'       => \App\Middleware\Authenticate::class,
        'guest'      => \App\Middleware\RedirectIfAuthenticated::class,
        'backoffice' => \App\Middleware\BackOffice::class,
        'can'        => \App\Middleware\Can::class,
    ];

    public static function run(): void
    {
        $request = Request::capture();

        // Media is streamed before the session starts: no cookies, no locks, cacheable.
        if (str_starts_with($request->path(), '/media/')) {
            (new MediaController())->show(substr($request->path(), 7))->send();
            return;
        }

        Session::start($request);
        $response = self::handle($request);
        SecurityHeaders::apply($response);
        $response->send();
    }

    public static function router(): Router
    {
        if (self::$router === null) {
            $router = new Router();
            require BASE_PATH . '/app/routes.php';
            self::$router = $router;
        }
        return self::$router;
    }

    private static function handle(Request $request): Response
    {
        try {
            [$route, $params] = self::router()->match($request);
            $request->setParams($params);
            $pipeline = array_merge([VerifyCsrf::class], $route->middleware);
            return self::through($pipeline, $request, static function (Request $req) use ($route): Response {
                [$class, $method] = $route->handler;
                return (new $class())->$method($req);
            });
        } catch (ValidationException $e) {
            return self::validationFailed($request, $e);
        } catch (HttpException $e) {
            return self::errorResponse($request, $e->status, $e->getMessage());
        } catch (Throwable $e) {
            self::log($e);
            return self::errorResponse($request, 500, config('app.debug') ? $e->getMessage() : '');
        }
    }

    private static function through(array $pipeline, Request $request, callable $core): Response
    {
        $next = $core;
        foreach (array_reverse($pipeline) as $entry) {
            [$alias, $arg] = array_pad(explode(':', $entry, 2), 2, null);
            $class = self::MIDDLEWARE[$alias] ?? $alias;
            $next = static fn (Request $r): Response => (new $class())->handle($r, $next, $arg);
        }
        return $next($request);
    }

    private static function validationFailed(Request $request, ValidationException $e): Response
    {
        if ($request->wantsJson()) {
            return Response::error(reset($e->errors) ?: 'Please check the form.', 422, ['errors' => $e->errors]);
        }
        Session::flash('errors', $e->errors);
        $old = $request->all();
        unset($old['password'], $old['password_confirmation'], $old['current_password'], $old['_token']);
        Session::flash('old', $old);
        return Response::back();
    }

    public static function errorResponse(Request $request, int $status, string $detail = ''): Response
    {
        $messages = [
            401 => 'Please sign in to continue.',
            403 => "This area isn't open to your account.",
            404 => "We couldn't find that page.",
            405 => "That action isn't allowed here.",
            419 => 'Your session timed out. Refresh the page and try again.',
            429 => 'Too many attempts. Please wait a few minutes and try again.',
            500 => 'Something went wrong on our side.',
        ];
        $message = $detail !== '' && $status !== 500 ? $detail : ($messages[$status] ?? 'Something went wrong.');

        if ($request->wantsJson()) {
            // A guest tapping "save" gets sent to sign in by the browser; bring them back to the page they were on.
            $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
            if ($status === 401 && str_starts_with($referer, config('app.url') . '/')) {
                Session::set('intended', (string) parse_url($referer, PHP_URL_PATH));
            }
            return Response::error($status === 500 && $detail !== '' ? $detail : $message, $status);
        }
        if ($status === 401) {
            Session::set('intended', url($request->path()));
            Session::flash('toast', ['type' => 'info', 'message' => $message]);
            return Response::redirect(url('/login'));
        }
        // A 500 never goes through the store layout: the layout may depend on whatever just broke.
        if ($status !== 500) {
            try {
                $template = $status === 404 ? 'errors/404' : 'errors/generic';
                return Response::view($template, ['title' => $message, 'status' => $status, 'message' => $message], $status);
            } catch (Throwable $e) {
                self::log($e);
            }
        }
        return Response::html(View::capture('errors/500', ['detail' => config('app.debug') ? $detail : '']), 500);
    }

    public static function log(Throwable $e): void
    {
        $line = sprintf("[%s] %s: %s in %s:%d\n%s\n\n", date('c'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        error_log($line, 3, BASE_PATH . '/storage/logs/app.log');
    }
}

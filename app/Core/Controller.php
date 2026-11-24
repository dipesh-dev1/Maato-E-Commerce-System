<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = [], int $status = 200, ?string $layout = 'layouts/store'): Response
    {
        return Response::view($template, $data, $status, $layout);
    }

    protected function json(mixed $data = null, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function back(string $fallback = '/'): Response
    {
        return Response::back($fallback);
    }

    /** @throws ValidationException */
    protected function validate(Request $request, array $rules, array $labels = []): array
    {
        return Validator::validate($request->all(), $rules, $labels);
    }

    protected function user(): array
    {
        $user = Auth::user();
        if ($user === null) {
            throw new HttpException(401);
        }
        return $user;
    }

    protected function authorize(string $ability): void
    {
        if (!Auth::can($ability)) {
            throw new HttpException(403);
        }
    }

    protected function notFoundUnless(mixed $value): mixed
    {
        if (!$value) {
            throw new HttpException(404);
        }
        return $value;
    }
}

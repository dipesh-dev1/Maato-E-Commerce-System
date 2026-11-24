<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

abstract class AdminController extends Controller
{
    protected function page(string $template, array $data = []): Response
    {
        return $this->view('admin/' . $template, $data, 200, 'layouts/admin');
    }

    /** Index pages render fully on first load; the data table re-requests just its body as JSON. */
    protected function table(Request $request, string $template, string $tableTemplate, array $data): Response
    {
        if ($request->wantsJson()) {
            return $this->json(['html' => View::capture('admin/' . $tableTemplate, $data)]);
        }
        return $this->page($template, $data);
    }

    /** Reads whitelisted query filters (always strings, trimmed, length-capped). */
    protected function filters(Request $request, array $keys): array
    {
        $out = [];
        foreach (array_merge($keys, ['sort', 'dir']) as $key) {
            $out[$key] = mb_substr((string) $request->query($key, ''), 0, 80);
        }
        return $out;
    }

    protected function pageNumber(Request $request): int
    {
        return max(1, (int) $request->query('page', 1));
    }

    /** IDs posted from a bulk-action form, as positive ints. */
    protected function ids(Request $request): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('ids', [])), static fn ($id) => $id > 0)));
        return array_slice($ids, 0, 200);
    }

    /** Slide-over forms post via fetch and expect {redirect}; plain posts get a normal redirect. */
    protected function done(Request $request, string $message, string $redirect): Response
    {
        flash('success', $message);
        return $request->wantsJson() ? $this->json(['redirect' => $redirect]) : $this->redirect($redirect);
    }

    protected function fail(Request $request, string $message, string $redirect): Response
    {
        if ($request->wantsJson()) {
            return Response::error($message, 422);
        }
        flash('error', $message);
        return $this->redirect($redirect);
    }
}

<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\ReviewRepository;
use App\Services\ActivityLogger;

final class ReviewController extends AdminController
{
    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['q', 'status']);
        // The moderation queue opens on pending reviews; "all" is an explicit choice.
        $filters['status'] = $filters['status'] === '' ? 'pending' : $filters['status'];
        return $this->table($request, 'reviews/index', 'reviews/_table', [
            'title'   => 'Reviews',
            'crumbs'  => [['Reviews', null]],
            'filters' => $filters,
            'result'  => (new ReviewRepository())->adminList($filters, $this->pageNumber($request)),
        ]);
    }

    public function bulk(Request $request): Response
    {
        $ids = $this->ids($request);
        $action = (string) $request->input('action');
        if (!$ids || !in_array($action, ['approve', 'reject', 'delete'], true)) {
            return $this->fail($request, 'Choose at least one review and an action.', route('admin.reviews'));
        }
        $repo = new ReviewRepository();
        $count = $action === 'delete' ? $repo->delete($ids) : $repo->setStatus($ids, $action === 'approve' ? 'approved' : 'rejected');
        ActivityLogger::log("review.{$action}", 'review', count($ids) === 1 ? $ids[0] : null, ucfirst($action) . ' ' . plural(count($ids), 'review'), ['ids' => $ids]);
        $verb = ['approve' => 'approved and now visible', 'reject' => 'rejected', 'delete' => 'deleted'][$action];
        return $this->done($request, plural(max($count, count($ids)), 'review') . " {$verb}.", url('/admin/reviews', array_filter(['status' => $request->input('return_status')])));
    }
}

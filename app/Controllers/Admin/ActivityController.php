<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\ActivityLogRepository;

final class ActivityController extends AdminController
{
    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['q', 'entity', 'user']);
        $repo = new ActivityLogRepository();
        return $this->table($request, 'activity/index', 'activity/_table', [
            'title'    => 'Activity log',
            'crumbs'   => [['Activity log', null]],
            'filters'  => $filters,
            'result'   => $repo->adminList($filters, $this->pageNumber($request)),
            'entities' => $repo->entityTypes(),
            'actors'   => $repo->actors(),
        ]);
    }
}

<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class PageController extends Controller
{
    private const PAGES = [
        'our-story'        => 'Our story',
        'shipping-returns' => 'Delivery & returns',
        'size-guide'       => 'Size guide',
        'contact'          => 'Contact us',
    ];

    public function show(Request $request): Response
    {
        $slug = (string) $request->param('page');
        $title = $this->notFoundUnless(self::PAGES[$slug] ?? null);
        return $this->view('store/pages/' . $slug, ['title' => $title, 'slug' => $slug]);
    }
}

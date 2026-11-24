<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\ProductRepository;
use App\Models\ReviewRepository;

final class ReviewController extends Controller
{
    public function store(Request $request): Response
    {
        $user = $this->user();
        $product = $this->notFoundUnless((new ProductRepository())->findPublishedBySlug((string) $request->param('slug')));
        $data = $this->validate($request, [
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'required|string|min:3|max:120',
            'body'   => 'required|string|min:20|max:2000',
        ], ['body' => 'your review', 'title' => 'a short headline', 'rating' => 'a rating']);

        $reviews = new ReviewRepository();
        $back = route('product', ['slug' => $product['slug']]) . '#reviews';
        if (!$reviews->userReceivedProduct((int) $user['id'], (int) $product['id'])) {
            flash('error', 'Reviews open once your order has been delivered.');
            return $this->redirect($back);
        }
        if ($reviews->findByUser((int) $product['id'], (int) $user['id']) !== null) {
            flash('info', "You've already reviewed this piece. Thank you!");
            return $this->redirect($back);
        }
        $reviews->create((int) $product['id'], (int) $user['id'], $data['rating'], $data['title'], $data['body']);
        flash('success', "Thanks for the review. It'll appear here once we've read it, usually within a day.");
        return $this->redirect($back);
    }
}

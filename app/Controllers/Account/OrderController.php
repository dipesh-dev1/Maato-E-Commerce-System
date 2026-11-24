<?php
declare(strict_types=1);

namespace App\Controllers\Account;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\OrderRepository;
use App\Services\CartException;
use App\Services\OrderService;

final class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('account/orders', [
            'title'  => 'Your orders',
            'result' => (new OrderRepository())->listForUser((int) Auth::id(), max(1, (int) $request->query('page', 1))),
        ]);
    }

    public function show(Request $request): Response
    {
        $orders = new OrderRepository();
        $order = $this->notFoundUnless($orders->findForUser((string) $request->param('number'), (int) Auth::id()));
        return $this->view('account/order', [
            'title'   => 'Order ' . $order['order_number'],
            'order'   => $order,
            'items'   => $orders->items((int) $order['id']),
            'history' => $orders->history((int) $order['id']),
            'justPlaced' => $request->query('placed') === '1',
        ]);
    }

    public function cancel(Request $request): Response
    {
        $order = $this->notFoundUnless((new OrderRepository())->findForUser((string) $request->param('number'), (int) Auth::id()));
        try {
            (new OrderService())->cancelByCustomer((int) $order['id'], (int) Auth::id());
            flash('success', "Order {$order['order_number']} is cancelled. Nothing is owed.");
        } catch (CartException $e) {
            flash('error', $e->getMessage());
        }
        return $this->redirect(route('account.order', ['number' => $order['order_number']]));
    }
}

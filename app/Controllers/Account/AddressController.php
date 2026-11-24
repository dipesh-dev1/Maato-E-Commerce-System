<?php
declare(strict_types=1);

namespace App\Controllers\Account;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AddressRepository;

final class AddressController extends Controller
{
    public const PROVINCES = ['Koshi', 'Madhesh', 'Bagmati', 'Gandaki', 'Lumbini', 'Karnali', 'Sudurpashchim'];
    private const MAX_ADDRESSES = 10;

    private const RULES = [
        'label'          => 'required|string|max:40',
        'recipient_name' => 'required|string|min:2|max:120',
        'phone'          => 'required|phone',
        'line1'          => 'required|string|min:3|max:160',
        'line2'          => 'nullable|string|max:160',
        'city'           => 'required|string|max:80',
        'district'       => 'required|string|max:80',
        'province'       => 'required|in:Koshi,Madhesh,Bagmati,Gandaki,Lumbini,Karnali,Sudurpashchim',
        'postal_code'    => 'nullable|string|max:10',
        'is_default'     => 'boolean',
    ];

    private const LABELS = [
        'label' => 'a label', 'recipient_name' => "the recipient's name", 'phone' => 'a phone number',
        'line1' => 'the street address', 'city' => 'the city or town', 'district' => 'the district', 'province' => 'province',
    ];

    public function index(Request $request): Response
    {
        return $this->view('account/addresses', [
            'title'     => 'Addresses',
            'addresses' => (new AddressRepository())->forUser((int) Auth::id()),
            'provinces' => self::PROVINCES,
            'returnTo'  => $request->query('return') === 'checkout' ? 'checkout' : null,
        ]);
    }

    public function store(Request $request): Response
    {
        $userId = (int) Auth::id();
        $repo = new AddressRepository();
        if (count($repo->forUser($userId)) >= self::MAX_ADDRESSES) {
            flash('error', 'You can keep up to ' . self::MAX_ADDRESSES . ' addresses. Remove one to add another.');
            return $this->back('/account/addresses');
        }
        $this->openFormOnError('new');
        $id = $repo->create($userId, $this->validate($request, self::RULES, self::LABELS));
        flash('success', 'Address added.');
        if ($request->input('return') === 'checkout') {
            return $this->redirect(url('/checkout', ['address' => $id]));
        }
        return $this->redirect(route('account.addresses'));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new AddressRepository();
        $this->notFoundUnless($repo->findForUser($id, (int) Auth::id()));
        $this->openFormOnError((string) $id);
        $repo->update($id, (int) Auth::id(), $this->validate($request, self::RULES, self::LABELS));
        flash('success', 'Address updated.');
        return $this->redirect(route('account.addresses'));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new AddressRepository();
        $this->notFoundUnless($repo->findForUser($id, (int) Auth::id()));
        $repo->delete($id, (int) Auth::id());
        flash('success', 'Address removed. Past orders keep their original address.');
        return $this->redirect(route('account.addresses'));
    }

    public function makeDefault(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new AddressRepository();
        $this->notFoundUnless($repo->findForUser($id, (int) Auth::id()));
        $repo->setDefault($id, (int) Auth::id());
        flash('success', 'Default address updated.');
        return $this->redirect(route('account.addresses'));
    }

    /** Tells the view which address form to reopen if validation fails and we bounce back. */
    private function openFormOnError(string $which): void
    {
        Session::flash('address_form', $which);
    }
}

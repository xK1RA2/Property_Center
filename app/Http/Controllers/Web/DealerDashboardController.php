<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Checkout;
use App\Models\Property;
use Illuminate\Http\Request;

/** Routes are behind the role:2 middleware. */
class DealerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $dealerId = $request->user()->id;

        return view('home.dash', [
            'Dealer' => $request->user(),
            'Properties' => Property::where('Dealer_id', $dealerId)->get(),
            'Requests' => Booking::where('dealer_id', $dealerId)->orderByDesc('status')->with(['propertyRef', 'User'])->get(),
            'Checkouts' => Checkout::where('dealer_id', $dealerId)->get(),
        ]);
    }
}

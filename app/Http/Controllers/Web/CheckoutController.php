<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, Property $property, CheckoutService $checkout)
    {
        $data = $request->validate([
            'card_last4' => ['required', 'digits:4'],
            'address' => ['nullable', 'string', 'max:255'],
            'From' => ['nullable', 'date'],
            'To' => ['nullable', 'date', 'after_or_equal:From'],
        ]);

        try {
            $checkout->pay(
                $request->user(),
                $property,
                $data['card_last4'],
                $data['address'] ?? null,
                $data['From'] ?? null,
                $data['To'] ?? null
            );
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('CheckoutSuccess', 'Checkout Completed');
    }
}

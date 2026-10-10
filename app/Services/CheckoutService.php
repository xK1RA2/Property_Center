<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * There is no payment gateway yet, so this records that a purchase happened.
 * Card number, expiry and CVV are never received or stored, only the last 4 digits.
 * Plug a real provider (Stripe, ...) in here later.
 */
class CheckoutService
{
    /** @throws \DomainException when the property can no longer be bought */
    public function pay(User $buyer, Property $property, string $cardLast4, ?string $address = null, ?string $from = null, ?string $to = null): Checkout
    {
        return DB::transaction(function () use ($buyer, $property, $cardLast4, $address, $from, $to) {
            // re-read inside the transaction so two buyers can't both succeed
            $fresh = Property::query()->lockForUpdate()->findOrFail($property->id);

            if ($fresh->status !== 'Available') {
                throw new \DomainException('This property is no longer available');
            }

            $checkout = Checkout::updateOrCreate(
                ['user_id' => $buyer->id, 'property_id' => $fresh->id],
                [
                    'dealer_id' => $fresh->Dealer_id,
                    'price' => $fresh->price,
                    'card_last4' => $cardLast4,
                    'from' => $from,
                    'to' => $to,
                    'address' => $address ?? '',
                    'date' => now(),
                ]
            );

            $fresh->update(['status' => 'Not Available']);

            return $checkout;
        });
    }
}

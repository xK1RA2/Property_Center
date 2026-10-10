<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function pay(User $buyer, Property $property, string $cardLast4, ?string $address = null, ?string $from = null, ?string $to = null): Checkout
    {
        return DB::transaction(function () use ($buyer, $property, $cardLast4, $address, $from, $to) {
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

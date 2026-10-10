<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Carbon\CarbonInterface;

class BookingService
{
    public const APPROVED = 'Approved';
    public const REJECTED = 'Rejected';
    public const PENDING = 'Pending';

    public function __construct(private PushNotifier $push)
    {
    }

    /** One booking per user and property: asking again moves the request and resets it to Pending. */
    public function request(User $user, Property $property, CarbonInterface|string $date, ?string $note = null): Booking
    {
        $booking = Booking::updateOrCreate(
            ['property_id' => $property->id, 'user_id' => $user->id],
            [
                'dealer_id' => $property->Dealer_id,
                'date' => $date,
                'description' => filled($note) ? $note : 'Empty',
                'status' => self::PENDING,
            ]
        );

        if ($dealer = $property->Dealer) {
            $this->notifyAfterResponse(
                $dealer,
                'New visit request',
                "{$user->name} would like to visit {$this->label($property)}.",
                $booking
            );
        }

        return $booking;
    }

    public function decide(Booking $booking, string $status): Booking
    {
        abort_unless(in_array($status, [self::APPROVED, self::REJECTED], true), 422);

        $booking->update(['status' => $status]);

        if ($requester = $booking->User) {
            $property = $booking->propertyRef;
            $verdict = $status === self::APPROVED ? 'approved' : 'declined';

            $this->notifyAfterResponse(
                $requester,
                "Visit {$verdict}",
                'Your visit request for ' . ($property ? $this->label($property) : 'the property') . " was {$verdict}.",
                $booking
            );
        }

        return $booking;
    }

    /** Sent once the HTTP response has gone out, so a slow push service never delays the user. */
    private function notifyAfterResponse(User $to, string $title, string $body, Booking $booking): void
    {
        $push = $this->push;
        $data = ['screen' => 'bookings', 'booking_id' => $booking->id];

        dispatch(fn () => $push->toUser($to, $title, $body, $data))->afterResponse();
    }

    private function label(Property $property): string
    {
        return preg_replace('/\s*\n\s*/', ', ', trim($property->address)) ?: "property #{$property->id}";
    }
}

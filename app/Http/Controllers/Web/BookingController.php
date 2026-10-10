<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookings)
    {
    }

    public function store(Request $request, Property $property)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after:now'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $booking = $this->bookings->request($request->user(), $property, $data['date'], $data['description'] ?? null);

        return redirect()->back()->with(
            $booking->wasRecentlyCreated ? 'BookingSuccess' : 'BookingUpdated',
            $booking->wasRecentlyCreated ? 'Booking Has Been Submited' : 'Booking Has Been Updated'
        );
    }

    public function accept(Request $request, Booking $booking)
    {
        $this->ownedBy($request, $booking);
        $this->bookings->decide($booking, BookingService::APPROVED);

        return redirect()->back()->with('success', 'Request Accepted');
    }

    public function refuse(Request $request, Booking $booking)
    {
        $this->ownedBy($request, $booking);
        $this->bookings->decide($booking, BookingService::REJECTED);

        return redirect()->back()->with('Rejected', 'Request Rejected');
    }

    private function ownedBy(Request $request, Booking $booking): void
    {
        abort_unless($booking->dealer_id == $request->user()->id, 403);
    }
}

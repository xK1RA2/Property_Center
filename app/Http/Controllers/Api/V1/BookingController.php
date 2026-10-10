<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /** GET /bookings : users see their own, dealers see bookings on their properties, admins see all */
    public function index(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::query()
            ->when($user->role_id == User::ROLE_USER, fn ($q) => $q->where('user_id', $user->id))
            ->when($user->role_id == User::ROLE_DEALER, fn ($q) => $q->where('dealer_id', $user->id))
            ->with(['User', 'propertyRef'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return BookingResource::collection($bookings);
    }

    /** POST /properties/{property}/bookings : create, or update the user's existing booking */
    public function store(Request $request, Property $property, BookingService $bookings): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after:now'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $booking = $bookings->request($request->user(), $property, $data['date'], $data['description'] ?? null);

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode($booking->wasRecentlyCreated ? 201 : 200);
    }
    /** PATCH /bookings/{booking} : the property's dealer approves or rejects */
    public function update(Request $request, Booking $booking, BookingService $bookings): BookingResource
    {
        abort_unless($booking->dealer_id == $request->user()->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:Approved,Rejected'],
        ]);

        return new BookingResource($bookings->decide($booking, $data['status'])->load(['User', 'propertyRef']));
    }}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Property;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('home.maps', ['locations' => Location::with('property')->get()]);
    }

    /** Pins the dealer's newly created listing (or, failing that, their most recent one). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $dealerListings = Property::where('Dealer_id', $request->user()->id);

        $newId = $request->session()->pull('new_property_id');
        $property = $newId ? (clone $dealerListings)->find($newId) : null;
        $property ??= $dealerListings->latest('id')->firstOrFail();

        $property->location()->updateOrCreate([], [
            'name' => $property->address ?: 'Location',
            'lat' => $data['lat'],
            'lng' => $data['lng'],
        ]);

        return redirect()->route('property');
    }
}

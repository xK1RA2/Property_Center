<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $properties = $request->user()
            ->Favourite_Property()
            ->with(['PrimaryImage', 'City', 'propertyType'])
            ->paginate(5);

        return view('property.wishlist', ['Properties' => $properties]);
    }

    public function toggle(Request $request, Property $property)
    {
        $request->user()->Favourite_Property()->toggle($property->id);

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Property $property)
    {
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5']]);

        $rating = Rating::updateOrCreate(
            ['user_id' => $request->user()->id, 'Property_id' => $property->id],
            ['Rate' => $data['rating']]
        );

        return redirect()->back()->with(
            $rating->wasRecentlyCreated ? 'RateSubmit' : 'update',
            $rating->wasRecentlyCreated ? 'Thanks For Rating' : 'Rate Upated'
        );
    }
}

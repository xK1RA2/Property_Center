<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /** PUT /properties/{property}/rating : one rating per user, re-sending updates it */
    public function upsert(Request $request, Property $property): JsonResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        Rating::updateOrCreate(
            ['user_id' => $request->user()->id, 'Property_id' => $property->id],
            ['Rate' => $data['rating']]
        );

        return response()->json([
            'rating' => $data['rating'],
            'rating_avg' => round((float) $property->PropertyRate()->avg('Rate'), 1),
            'rating_count' => $property->PropertyRate()->count(),
        ]);
    }
}

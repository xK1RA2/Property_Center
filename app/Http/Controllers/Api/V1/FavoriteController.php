<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return PropertyResource::collection(
            $request->user()->Favourite_Property()
                ->with(['PrimaryImage', 'City', 'propertyType'])
                ->paginate($request->integer('per_page', 12))
        );
    }

    public function store(Request $request, Property $property): JsonResponse
    {
        $request->user()->Favourite_Property()->syncWithoutDetaching([$property->id]);

        return response()->json(['favorite' => true]);
    }

    public function destroy(Request $request, Property $property): JsonResponse
    {
        $request->user()->Favourite_Property()->detach($property->id);

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function states(): JsonResponse
    {
        return response()->json(['data' => State::with('cities:id,name,state_id')->get(['id', 'name'])]);
    }

    public function cities(): JsonResponse
    {
        return response()->json(['data' => City::get(['id', 'name', 'state_id'])]);
    }

    public function propertyTypes(): JsonResponse
    {
        return response()->json(['data' => PropertyType::orderBy('name')->get(['id', 'name'])]);
    }

    public function dealers()
    {
        return UserResource::collection(User::where('role_id', User::ROLE_DEALER)->paginate(12));
    }
}

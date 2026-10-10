<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /** POST /devices : register (or move) this phone's push token to the signed-in user. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255', 'regex:/^(Exponent|Expo)PushToken\[.+\]$/'],
            'platform' => ['nullable', 'in:ios,android,web'],
        ]);

        // a token belongs to one device; if someone else signs in on it, it moves to them
        $device = DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            ['user_id' => $request->user()->id, 'platform' => $data['platform'] ?? null]
        );

        return response()->json(['id' => $device->id], $device->wasRecentlyCreated ? 201 : 200);
    }

    /** DELETE /devices : called on sign-out so the phone stops getting this account's pushes. */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string']]);

        DeviceToken::where('token', $data['token'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(null, 204);
    }
}

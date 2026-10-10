<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\request_trader;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraderRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = request_trader::with('user')->latest()->get()->map(fn ($r) => [
            'id' => $r->id,
            'user' => new UserResource($r->user),
            'created_at' => $r->created_at,
        ]);

        return response()->json(['data' => $requests]);
    }

    /** PATCH /admin/trader-requests/{id} {action: approve|reject} */
    public function update(Request $request, request_trader $traderRequest): JsonResponse
    {
        $data = $request->validate(['action' => ['required', 'in:approve,reject']]);

        if ($data['action'] === 'approve') {
            User::where('id', $traderRequest->user_id)->update(['role_id' => User::ROLE_DEALER]);
        }

        $traderRequest->delete();

        return response()->json(['message' => $data['action'] === 'approve' ? 'Request approved' : 'Request rejected']);
    }
}

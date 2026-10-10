<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TraderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraderRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $trader = TraderRequest::firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json(
            ['id' => $trader->id, 'user_id' => $trader->user_id, 'created_at' => $trader->created_at],
            $trader->wasRecentlyCreated ? 201 : 200
        );
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return UserResource::collection(
            User::with('role')->orderBy('id')->paginate($request->integer('per_page', 20))
        );
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_if(
            $user->role_id == User::ROLE_ADMIN || $user->id === $request->user()->id,
            422,
            'This user can not be deleted.'
        );

        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }
}

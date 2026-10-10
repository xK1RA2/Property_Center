<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private const TYPE_NORMAL = 1;
    private const TYPE_ANONYMOUS = 2;

    public function index(Property $property)
    {
        return CommentResource::collection(
            Comment::where('Property_id', $property->id)->with('user')->latest()->paginate(20)
        );
    }

    public function store(Request $request, Property $property): JsonResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'anonymous' => ['sometimes', 'boolean'],
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'Property_id' => $property->id,
            'comment_type_id' => ($data['anonymous'] ?? false) ? self::TYPE_ANONYMOUS : self::TYPE_NORMAL,
            'Description' => $data['description'],
        ]);

        return (new CommentResource($comment->load('user')))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();
        abort_unless($comment->user_id == $user->id || $user->role_id == User::ROLE_ADMIN, 403);

        $comment->delete();

        return response()->json(null, 204);
    }
}

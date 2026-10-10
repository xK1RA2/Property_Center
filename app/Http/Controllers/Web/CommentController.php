<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Property;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private const TYPE_NORMAL = 1;
    private const TYPE_ANONYMOUS = 2;

    public function store(Request $request, Property $property)
    {
        $data = $request->validate(['Description' => ['required', 'string', 'max:1000']]);

        Comment::create([
            'user_id' => $request->user()->id,
            'Property_id' => $property->id,
            // the form's checkbox posts the literal value "Ananymous"
            'comment_type_id' => $request->input('Ananymous') === 'Ananymous' ? self::TYPE_ANONYMOUS : self::TYPE_NORMAL,
            'Description' => $data['Description'],
        ]);

        return redirect()->back()->with('success', 'Comment Has Been Submited');
    }

    public function destroy(Request $request, Comment $comment)
    {
        abort_unless($comment->user_id === $request->user()->id, 403);
        $comment->delete();

        return redirect()->back()->with('Delete', 'Comment Has Been Deleted');
    }
}

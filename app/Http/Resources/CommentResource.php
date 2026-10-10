<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public const TYPE_ANONYMOUS = 2;

    public function toArray(Request $request): array
    {
        $anonymous = $this->comment_type_id == self::TYPE_ANONYMOUS;

        return [
            'id' => $this->id,
            'property_id' => $this->Property_id,
            'description' => $this->Description,
            'anonymous' => $anonymous,
            // never leak the author of an anonymous comment
            'author' => $anonymous ? null : new UserResource($this->whenLoaded('user')),
            'is_mine' => $request->user('sanctum')?->id === $this->user_id,
            'created_at' => $this->created_at,
        ];
    }
}

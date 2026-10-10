<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->when($this->isOwnerOrAdmin($request), $this->email),
            'phone' => $this->phone,
            'profile_image' => $this->profile_image,
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role', fn () => $this->role->name),
            'created_at' => $this->created_at,
        ];
    }

    private function isOwnerOrAdmin(Request $request): bool
    {
        $viewer = $request->user();

        return $viewer && ($viewer->id === $this->id || $viewer->role_id == \App\Models\User::ROLE_ADMIN);
    }
}

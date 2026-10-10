<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** Set by auth responses, where the viewer is the user but no token is resolved yet. */
    private bool $revealEmail = false;

    public function withEmail(): static
    {
        $this->revealEmail = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->when($this->isOwnerOrAdmin($request), $this->email),
            'phone' => $this->phone,
            'profile_image' => $this->profile_image
                ? (str_starts_with($this->profile_image, 'http') ? $this->profile_image : url('storage/' . ltrim($this->profile_image, '/')))
                : null,
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role', fn () => $this->role->name),
            'created_at' => $this->created_at,
        ];
    }

    private function isOwnerOrAdmin(Request $request): bool
    {
        if ($this->revealEmail) {
            return true;
        }

        $viewer = $request->user('sanctum');

        return $viewer && ($viewer->id === $this->id || $viewer->role_id == \App\Models\User::ROLE_ADMIN);
    }
}

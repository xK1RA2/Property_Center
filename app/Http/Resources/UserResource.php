<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * What a user looks like to other people.
 *
 * Public:           id, name, profile photo, role
 * Dealers' contact: phone and username (that is the point of a dealer listing)
 * Owner or admin:   email, phone, username of ANY user
 *
 * Commenters, requesters and bookers are ordinary users, so their phone number and
 * username must not appear in public responses.
 */
class UserResource extends JsonResource
{
    /** Set by auth responses, where the viewer is the user but no token is resolved yet. */
    private bool $revealPrivate = false;

    public function withEmail(): static
    {
        $this->revealPrivate = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $private = $this->canSeePrivateFields($request);
        $contact = $private || $this->role_id === User::ROLE_DEALER;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->when($contact, $this->username),
            'email' => $this->when($private, $this->email),
            'phone' => $this->when($contact, $this->phone),
            'profile_image' => $this->profile_image
                ? (str_starts_with($this->profile_image, 'http') ? $this->profile_image : url('storage/' . ltrim($this->profile_image, '/')))
                : null,
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role', fn () => $this->role->name),
            'created_at' => $this->created_at,
        ];
    }

    private function canSeePrivateFields(Request $request): bool
    {
        if ($this->revealPrivate) {
            return true;
        }

        $viewer = $request->user('sanctum') ?? $request->user();

        return $viewer && ($viewer->id === $this->id || $viewer->role_id == User::ROLE_ADMIN);
    }
}

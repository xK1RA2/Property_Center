<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function update(User $user, Property $property): bool
    {
        return $user->role_id == User::ROLE_ADMIN
            || ($user->role_id == User::ROLE_DEALER && $property->Dealer_id == $user->id);
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }
}

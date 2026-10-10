<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $this->user_id,
            'dealer_id' => $this->dealer_id,
            'date' => $this->date,
            'description' => $this->description,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('User')),
            'created_at' => $this->created_at,
        ];
    }
}

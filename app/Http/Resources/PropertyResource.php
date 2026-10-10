<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_type' => $this->PurchaseType,
            'price' => $this->price,
            'year' => $this->year,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'city' => $this->whenLoaded('City', fn () => [
                'id' => $this->City?->id,
                'name' => $this->City?->name,
            ]),
            'type' => $this->whenLoaded('propertyType', fn () => [
                'id' => $this->propertyType?->id,
                'name' => $this->propertyType?->name,
            ]),
            'dealer' => new UserResource($this->whenLoaded('Dealer')),
            'features' => $this->whenLoaded('Features', fn () => $this->Features),
            'images' => $this->whenLoaded('PropertyImages', fn () => $this->PropertyImages
                ->sortBy('position')->values()
                ->map(fn ($img) => ['id' => $img->id, 'url' => url($img->getUrl()), 'position' => $img->position])),
            'primary_image' => $this->whenLoaded('PrimaryImage', fn () => $this->PrimaryImage ? url($this->PrimaryImage->getUrl()) : null),
            'location' => $this->whenLoaded('location', fn () => $this->location
                ? ['name' => $this->location->name, 'lat' => (float) $this->location->lat, 'lng' => (float) $this->location->lng]
                : null),
            'rating_avg' => $this->when(
                array_key_exists('property_rate_avg_rate', $this->resource->getAttributes()),
                fn () => $this->property_rate_avg_rate === null ? null : round((float) $this->property_rate_avg_rate, 1)
            ),
            'rating_count' => $this->when(
                array_key_exists('property_rate_count', $this->resource->getAttributes()),
                fn () => (int) $this->property_rate_count
            ),
            // only set by show() for a signed-in viewer
            'is_favorite' => $this->when(array_key_exists('is_favorite', $this->resource->getAttributes()), fn () => (bool) $this->is_favorite),
            'my_rating' => $this->when(array_key_exists('my_rating', $this->resource->getAttributes()), fn () => $this->my_rating),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

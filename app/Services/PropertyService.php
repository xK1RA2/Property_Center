<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * The single place that knows how a listing is created, changed and removed.
 * Used by both the REST API and the Blade site so they cannot drift apart.
 */
class PropertyService
{
    /**
     * @param  array<string,mixed>  $attrs     columns on `properties` (PurchaseType, property_type_id, city_id, price, year, address, ...)
     * @param  array<string,mixed>  $features  columns on `property_features`; null values are ignored
     * @param  UploadedFile[]  $images
     * @param  array{name:string,lat:float|string,lng:float|string}|null  $location
     */
    public function create(User $dealer, array $attrs, array $features = [], array $images = [], ?array $location = null): Property
    {
        return DB::transaction(function () use ($dealer, $attrs, $features, $images, $location) {
            $publishedAt = $attrs['published_at'] ?? now();
            unset($attrs['published_at']);

            $property = Property::create($attrs + [
                'Dealer_id' => $dealer->id,
                'status' => 'Available',
            ]);

            // not mass-assignable on the model
            $property->forceFill(['published_at' => $publishedAt])->save();

            $property->Features()->create($this->present($features));

            if ($location) {
                $property->location()->create($location);
            }

            $this->addImages($property, $images);

            return $property;
        });
    }

    /** Only the keys present in the arrays are touched; everything else is left as is. */
    public function update(Property $property, array $attrs, array $features = [], array $images = [], ?array $location = null): Property
    {
        return DB::transaction(function () use ($property, $attrs, $features, $images, $location) {
            if (array_key_exists('published_at', $attrs)) {
                $property->forceFill(['published_at' => $attrs['published_at']]);
                unset($attrs['published_at']);
            }

            $property->fill($this->present($attrs, keepEmptyStrings: true))->save();

            if ($features = $this->present($features)) {
                $property->Features()->updateOrCreate([], $features);
            }

            if ($location) {
                $property->location()->updateOrCreate([], $location);
            }

            $this->addImages($property, $images);

            return $property;
        });
    }

    public function delete(Property $property): void
    {
        DB::transaction(function () use ($property) {
            $property->location()->delete();
            $property->delete(); // soft delete: bookings / orders keep their reference
        });
    }

    /** Appends photos after the existing ones. */
    public function addImages(Property $property, array $images): void
    {
        $position = (int) $property->PropertyImages()->max('position');

        foreach ($images as $image) {
            $property->PropertyImages()->create([
                'image_path' => $image->store('images', 'public'),
                'position' => ++$position,
            ]);
        }
    }

    /** Drops nulls so a missing form field never overwrites a NOT NULL column. */
    private function present(array $values, bool $keepEmptyStrings = false): array
    {
        return array_filter($values, fn ($v) => $v !== null && ($keepEmptyStrings || $v !== ''));
    }
}

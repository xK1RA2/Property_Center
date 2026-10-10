<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PropertyController extends Controller
{
    /** GET /properties : public, filterable, paginated */
    public function index(Request $request)
    {
        $request->validate([
            'city_id' => ['sometimes', 'integer'],
            'property_type_id' => ['sometimes', 'integer'],
            'purchase_type' => ['sometimes', 'in:Sell,Rent'],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0'],
            'search' => ['sometimes', 'string', 'max:100'],
            'sort' => ['sometimes', 'in:newest,price_asc,price_desc'],
            'per_page' => ['sometimes', 'integer', 'between:1,50'],
        ]);

        $properties = Property::query()
            ->where('status', 'Available')
            ->where('published_at', '<', now())
            ->when($request->city_id, fn ($q, $v) => $q->where('city_id', $v))
            ->when($request->property_type_id, fn ($q, $v) => $q->where('property_type_id', $v))
            ->when($request->purchase_type, fn ($q, $v) => $q->where('PurchaseType', $v))
            ->when($request->min_price, fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($request->max_price, fn ($q, $v) => $q->where('price', '<=', $v))
            ->when($request->search, fn ($q, $v) => $q->where(
                fn ($w) => $w->where('address', 'like', "%{$v}%")->orWhere('description', 'like', "%{$v}%")
            ))
            ->with(['PrimaryImage', 'City', 'propertyType'])
            ->withAvg('PropertyRate', 'Rate')
            ->withCount('PropertyRate');

        match ($request->sort) {
            'price_asc' => $properties->orderBy('price'),
            'price_desc' => $properties->orderByDesc('price'),
            default => $properties->orderByDesc('published_at'),
        };

        return PropertyResource::collection(
            $properties->paginate($request->integer('per_page', 12))->withQueryString()
        );
    }

    /** GET /my/properties : the dealer's own listings (any status) */
    public function mine(Request $request)
    {
        return PropertyResource::collection(
            $request->user()->Properties()
                ->with(['PrimaryImage', 'City', 'propertyType'])
                ->withAvg('PropertyRate', 'Rate')
                ->withCount('PropertyRate')
                ->latest()
                ->paginate($request->integer('per_page', 12))
        );
    }

    public function show(Property $property): PropertyResource
    {
        $property->load(['City.state', 'propertyType', 'Dealer', 'Features', 'PropertyImages', 'location'])
            ->loadAvg('PropertyRate', 'Rate')
            ->loadCount('PropertyRate');

        return new PropertyResource($property);
    }

    public function store(PropertyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $property = Property::create([
            'Dealer_id' => $request->user()->id,
            'PurchaseType' => $data['purchase_type'],
            'property_type_id' => $data['property_type_id'],
            'city_id' => $data['city_id'],
            'price' => $data['price'],
            'year' => $data['year'],
            'address' => $data['address'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'Available',
        ]);
        // published_at is not mass-assignable on the model
        $property->forceFill(['published_at' => $data['published_at'] ?? now()])->save();

        $property->Features()->create($data['features'] ?? []);
        $this->storeImages($property, $request->file('images', []));

        return (new PropertyResource($this->loadFull($property)))
            ->response()->setStatusCode(201);
    }

    public function update(PropertyRequest $request, Property $property): PropertyResource
    {
        Gate::authorize('update', $property);
        $data = $request->validated();

        $fields = array_filter([
            'PurchaseType' => $data['purchase_type'] ?? null,
            'property_type_id' => $data['property_type_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'price' => $data['price'] ?? null,
            'year' => $data['year'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => $data['status'] ?? null,
        ], fn ($v) => $v !== null);

        if (array_key_exists('description', $data)) {
            $fields['description'] = $data['description'];
        }

        $property->update($fields);

        if (array_key_exists('published_at', $data)) {
            $property->forceFill(['published_at' => $data['published_at']])->save();
        }
        if (!empty($data['features'])) {
            $property->Features()->updateOrCreate([], $data['features']);
        }
        $this->storeImages($property, $request->file('images', []));

        return new PropertyResource($this->loadFull($property));
    }

    public function destroy(Property $property): JsonResponse
    {
        Gate::authorize('delete', $property);
        $property->delete();

        return response()->json(null, 204);
    }

    private function storeImages(Property $property, array $images): void
    {
        $position = (int) $property->PropertyImages()->max('position');

        foreach ($images as $image) {
            $property->PropertyImages()->create([
                'image_path' => $image->store('images', 'public'),
                'position' => ++$position,
            ]);
        }
    }

    private function loadFull(Property $property): Property
    {
        return $property->refresh()->load(['City', 'propertyType', 'Dealer', 'Features', 'PropertyImages']);
    }
}

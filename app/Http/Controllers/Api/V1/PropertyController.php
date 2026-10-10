<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PropertyController extends Controller
{
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

        if ($viewer = request()->user('sanctum')) {
            $property->setAttribute('is_favorite', $viewer->Favourite_Property()->whereKey($property->id)->exists());
            $property->setAttribute('my_rating', \App\Models\Rating::where(['user_id' => $viewer->id, 'Property_id' => $property->id])->value('Rate'));
        }

        return new PropertyResource($property);
    }

    public function store(PropertyRequest $request, PropertyService $properties): JsonResponse
    {
        $data = $request->validated();

        $property = $properties->create(
            $request->user(),
            $this->attributes($data) + ['status' => $data['status'] ?? 'Available'],
            $data['features'] ?? [],
            $request->file('images', []),
            $data['location'] ?? null,
        );

        return (new PropertyResource($this->loadFull($property)))
            ->response()->setStatusCode(201);
    }

    public function update(PropertyRequest $request, Property $property, PropertyService $properties): PropertyResource
    {
        Gate::authorize('update', $property);
        $data = $request->validated();

        $properties->update(
            $property,
            $this->attributes($data, onlyPresent: true),
            $data['features'] ?? [],
            $request->file('images', []),
            $data['location'] ?? null,
        );

        return new PropertyResource($this->loadFull($property));
    }

    public function destroy(Property $property, PropertyService $properties): JsonResponse
    {
        Gate::authorize('delete', $property);
        $properties->delete($property);

        return response()->json(null, 204);
    }

    private function attributes(array $data, bool $onlyPresent = false): array
    {
        $map = [
            'purchase_type' => 'PurchaseType',
            'property_type_id' => 'property_type_id',
            'city_id' => 'city_id',
            'price' => 'price',
            'year' => 'year',
            'address' => 'address',
            'description' => 'description',
            'status' => 'status',
            'published_at' => 'published_at',
        ];

        $out = [];
        foreach ($map as $field => $column) {
            if (array_key_exists($field, $data)) {
                $out[$column] = $data[$field];
            }
        }

        if (!$onlyPresent) {
            unset($out['status']);
        }

        return $out;
    }

    private function loadFull(Property $property): Property
    {
        return $property->refresh()->load(['City', 'propertyType', 'Dealer', 'Features', 'PropertyImages', 'location']);
    }
}

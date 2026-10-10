<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Comment;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Dealer-facing listing management plus the public detail pages. */
class PropertyController extends Controller
{
    public function __construct(private PropertyService $properties)
    {
    }

    // ---------- public ----------

    public function details(Property $property)
    {
        $comments = Comment::where('Property_id', $property->id)->with('user')->get();

        return view('home.Details', ['Property' => $property, 'Comments' => $comments]);
    }

    public function show(Property $property)
    {
        return redirect()->route('property.details', $property);
    }

    // ---------- dealer ----------

    public function index(Request $request)
    {
        $properties = $request->user()
            ->Properties()
            ->with(['PrimaryImage', 'PropertyRate'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('property.index', ['Properties' => $properties]);
    }

    public function create()
    {
        return view('property.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $property = $this->properties->create(
            $request->user(),
            $this->attributes($data, $request) + ['year' => $data['year'] ?? (int) date('Y')],
            $this->features($request),
            $request->file('images') ?: [],
        );

        // the next page lets the dealer drop a pin for this exact listing
        $request->session()->put('new_property_id', $property->id);

        return redirect()->route('locations.create')
            ->with('success', "Property has been added you can add it's location on the map");
    }

    public function edit(Property $property)
    {
        Gate::authorize('update', $property);

        return view('property.Edit', ['Property' => $property] + $this->formData());
    }

    public function update(Request $request, Property $property)
    {
        Gate::authorize('update', $property);
        $data = $this->validated($request);

        $this->properties->update(
            $property,
            $this->attributes($data, $request),
            $this->features($request),
            $request->file('images') ?: [],
        );

        $target = $request->user()->role_id === User::ROLE_ADMIN ? 'ManageProperty' : 'property.edit';

        return redirect()->route($target, $property)->with('success', 'Property was updated');
    }

    public function destroy(Property $property)
    {
        Gate::authorize('delete', $property);
        $this->properties->delete($property);

        return redirect()->route('myProperties');
    }

    // ---------- helpers ----------

    private function validated(Request $request): array
    {
        return $request->validate([
            'PropertyPurchase' => ['required', 'in:Sell,Rent'],
            'PropertyName' => ['required', 'integer', 'exists:property_types,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'between:1800,' . (date('Y') + 5)],
            'Description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
        ]);
    }

    /** Form field names -> column names. Absent optional fields are simply not sent. */
    private function attributes(array $data, Request $request): array
    {
        return array_filter([
            'PurchaseType' => $data['PropertyPurchase'],
            'property_type_id' => $data['PropertyName'],
            'city_id' => $data['city_id'],
            'price' => $data['price'],
            'description' => $data['Description'] ?? null,
            'address' => $request->has('address') ? ($data['address'] ?? '') : null,
            'year' => $data['year'] ?? null,
        ], fn ($v) => $v !== null);
    }

    private function features(Request $request): array
    {
        return [
            'Area' => $request->input('area'),
            'Bedrooms' => $request->input('Bedrooms'),
            'Bathrooms' => $request->input('Bathrooms'),
            'Rooms' => $request->input('rooms'),
            'Kitchen' => $request->input('Kitchen'),
            'Air_Conditioner' => $request->boolean('Air_Conditioner') ? 1 : 0,
            'Parking' => $request->boolean('Parking') ? 1 : 0,
            'Heating' => $request->boolean('Heating') ? 1 : 0,
        ];
    }

    private function formData(): array
    {
        return [
            'propertyType' => PropertyType::orderBy('name')->get(),
            'Cities' => City::orderBy('name')->get()->unique('name')->values(),
            'Purchases' => Property::select('PurchaseType')->distinct()->whereNotNull('PurchaseType')->get(),
        ];
    }
}

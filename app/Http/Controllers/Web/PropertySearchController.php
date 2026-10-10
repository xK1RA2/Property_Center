<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    private const SORTABLE = ['published_at', '-published_at', 'price', '-price'];

    public function index()
    {
        $properties = Property::where('status', 'Available')
            ->with(['PrimaryImage', 'city', 'Features', 'PropertyRate'])
            ->orderBy('published_at', 'desc')
            ->get();

        return view('property.search', [
            'Properties' => $properties,
            'dealers' => User::where('role_id', User::ROLE_DEALER)->limit(4)->get(),
            'propertyType' => PropertyType::orderBy('name')->get()->unique('name')->values(),
            'Locations' => City::orderBy('name')->get()->unique('name')->values(),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'sort' => ['nullable', 'in:' . implode(',', self::SORTABLE)],
        ]);

        // "-column" means descending, the format the search form already submits
        $sort = $request->input('sort', '-published_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        $properties = Property::with(['PrimaryImage', 'City', 'propertyType', 'Features'])
            ->when($request->integer('city_id'), fn ($q, $v) => $q->where('city_id', $v))
            ->when($request->integer('Property_type_id'), fn ($q, $v) => $q->where('property_type_id', $v))
            ->when($request->integer('price_from'), fn ($q, $v) => $q->where('price', '>=', $v))
            ->when($request->integer('price_to'), fn ($q, $v) => $q->where('price', '<=', $v))
            ->when(
                in_array($request->input('Purchase_Type'), ['Sell', 'Rent'], true),
                fn ($q) => $q->where('PurchaseType', $request->input('Purchase_Type'))
            )
            ->orderBy($column, $direction)
            ->get();

        return view('property.search', [
            'Properties' => $properties,
            'propertyType' => PropertyType::get(),
            'Locations' => City::orderBy('name')->get()->unique('name')->values(),
        ]);
    }
}

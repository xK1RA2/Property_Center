<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'purchase_type' => [$required, 'in:Sell,Rent'],
            'property_type_id' => [$required, 'integer', 'exists:property_types,id'],
            'city_id' => [$required, 'integer', 'exists:cities,id'],
            'price' => [$required, 'integer', 'min:0'],
            'year' => [$required, 'integer', 'between:1800,' . (date('Y') + 5)],
            'address' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:Available,Not Available'],
            'published_at' => ['nullable', 'date'],

            'features' => ['sometimes', 'array'],
            'features.Bedrooms' => ['integer', 'min:0'],
            'features.Bathrooms' => ['integer', 'min:0'],
            'features.Kitchen' => ['integer', 'min:0'],
            'features.Rooms' => ['integer', 'min:0'],
            'features.Area' => ['integer', 'min:0'],
            'features.Heating' => ['boolean'],
            'features.Air_Conditioner' => ['boolean'],
            'features.Parking' => ['boolean'],

            'location' => ['sometimes', 'array'],
            'location.name' => ['required_with:location', 'string', 'max:255'],
            'location.lat' => ['required_with:location', 'numeric', 'between:-90,90'],
            'location.lng' => ['required_with:location', 'numeric', 'between:-180,180'],

            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => [File::image()->max(2048)->dimensions(Rule::dimensions()->maxWidth(6000)->maxHeight(6000))],
        ];
    }
}

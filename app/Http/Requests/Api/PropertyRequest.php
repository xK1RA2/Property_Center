<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role / ownership are enforced by route middleware and PropertyPolicy
    }

    public function rules(): array
    {
        // On update (PUT/PATCH) every field is optional.
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'purchase_type' => [$required, 'in:Sell,Rent'],
            'property_type_id' => [$required, 'integer', 'exists:Property_Type,id'],
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

            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => [File::image()->max(2048)],
        ];
    }
}

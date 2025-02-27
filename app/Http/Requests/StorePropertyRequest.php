<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'property_type'=>['required'],
            'price' => ['required','integer','min:0'],
            'state_id'=>['required','exists:states,id'],
            'city_id'=>['required','exists:cities,id'],
            'address'=>['required','string'],
            'features'=>['array'],
            'features.*'=>['string'],
            'description'=>['nullable','string'],
            'published_at' => 'nullable|string',
            'images'=>['array'],
            'images.*'=>File::image()->max(2048)
        ];
    }

    public function messages()
    {
        return 
        [
            'required' => 'This field is required'
        ];
    }
    public function attributes()
    {
        return[
          
            'property_type_id' => 'car type',
            
            'city_id' => 'region',
        ];
    }
  
}
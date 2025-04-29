<?php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Rate;
use App\Models\Role;
use Illuminate\Http\Request;

class RatingController 
{

    public function store(Request $request, Property $property)
    {

      $rate = Rate::where(['user_id'=>$request->user()->id])->first();
      if(!$rate){
        $request->validate([
            'rating' => 'required|integer|between:1,5'
        ]);

            Rate::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'Rate'=>$request['rating'],
                'Property_id'  =>$property->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        );
    }
        return redirect()->back();
    }
    
}
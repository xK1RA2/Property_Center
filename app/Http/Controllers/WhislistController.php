<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhislistController
{
    public function index()
    {
        // To render cars in wishList for authenticated users
        $Properties =User::find(3)
        ->Favourite_Property()
        ->with(['PrimaryImage','City','propertyType'])
        ->paginate(5);
       ;
        return view('property.wishlist',['Properties' => $Properties]);
    }

    public function storeDestroy(Property $Property )
    {
 
        $user = Auth::user();

       $PropertyExists = $user->favourite_Property()->where('Property_id',$Property->id)->exists(); 

        
        if ($PropertyExists) {
            $user->favourite_Property()->detach($property);
            return response()->json(
                [
                'added' => false,
                'message' => 'Property was removed from watchlist'
            ]);
        }

    
        $user->favourite_Property()->attach($car);
        return response()->json([
            'added' => true,
            'message' => 'Property was added to watchlist'
        ]);
    }
}

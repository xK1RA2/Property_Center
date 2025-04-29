<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhislistController
{
    public function index(Request $request)
    {
        $user = $request->user()->role_id;
        if($user !== 1){
            return redirect()->route("login");
        }
        // To render cars in wishList for authenticated users
        $id = $request->user()->id;
        $Properties =User::find($id)
        ->Favourite_Property()
        ->with(['PrimaryImage','City','propertyType'])
        ->paginate(5);
       ;
        return view('property.wishlist',['Properties' => $Properties]);
    }

    public function storeDestroy(Property $Property , Request $request )
    {
        $user = User::Where('id',$request->user()->id)->first();
        
        $user->Favourite_Property()->toggle($Property->id);
        return redirect()->back();
     

     
        
    
    }
}

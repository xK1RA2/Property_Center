<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Property;
class mapsController
{
    public function index(){
        $Locations = Location::with('property')->get();
        return view('home.maps',['locations' =>$Locations]);
    }
    public function LocationStore(Request $request)  {
        $property = Property::orderBy('id', 'desc')->first();
        $location = Location::create([
            'name' => $request->name,
            'property_id' => $property->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            
        ]);
        return redirect()->route('property');
    }
}

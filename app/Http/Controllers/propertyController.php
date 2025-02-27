<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Requests\StorePropertyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class propertyController

{
    public function index(){
        $Properties = User::find(7)
        ->Properties()
        ->with(['PrimaryImage'])
        ->orderBy("created_at",'desc')
        ->paginate(5);
        return view("property.index" , ['Properties'=>$Properties ]);
    }
    public function create()
    {
        $propertyType = PropertyType::groupBy('name')->get();
        $Cities = City::groupBy('name')->get();
        
        return view("property.create" , ['propertyType'=>$propertyType , 'Cities' => $Cities]);
    }
    public function store(Request $request)
    {
    

    dd($request);
       $data =[
        'Property_type_id' => $request['PropertyName'],
        'city_id' => $request['city_id'],
        'price' => $request['price'],
        'Dealer_id'=>2,
        'year'=>2000,
        'description'=>$request['Description'],
        'address'=>"hello",
        'status'=>"Available",

       ];
       $featuresData = [

        'Area' =>$request['area'],
        'Bedrooms' =>$request['Bedrooms'],
        'Bathrooms' =>$request['Bathrooms'],
        'Rooms' =>$request['rooms'],
        'Kitchen' =>$request['Kitchen'],
        'Air_Conditioner' =>$request['Air_Conditioner'],
        'Parking' =>$request['Parking'],
        'Heating' =>$request['Heating'],


       ];

     
        
        $images = $request->file('images')?:[];

        $request['dealer_id']=Auth::id();
       
        $property = Property::create($data);
        $property->features()->create($featuresData);
        
        foreach($images as $i => $image)
        {
     
            $path = $image->store('images','public');
            $property->PropertyImages()->create(['image_path'=>$path,'position'=>$i+1]);
        }
        return redirect()->back()->with('success','property was created');
    }
}

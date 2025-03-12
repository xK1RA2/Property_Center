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
use Illuminate\Database\Eloquent\Builder;

use App\Models\PropertyFeatures;


class propertyController

{
    public function index(){
        $Properties = User::find(7)
        ->Properties()
        ->with(['PrimaryImage','PropertyRate'])
        ->orderBy("created_at",'desc')
        ->paginate(5);
        return view("property.index" , ['Properties'=>$Properties ]);
    }

    

    
    public function create()
    {
        $propertyType = PropertyType::orderBy('name')->get();
        $Cities = City::groupBy('name')->get();
        $Purchases = Property::groupBy('PurchaseType')->get()->wherenotnull('PurchaseType');
        
        return view("property.create" , ['propertyType'=>$propertyType , 'Cities' => $Cities ,'Purchases' => $Purchases]);
    }
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'PropertyPurchase' => 'required|string',  // Ensure it's required and a string
            'PropertyName' => 'required|integer',
            'city_id' => 'required|integer',
            'price' => 'required|numeric',
            'Description' => 'nullable|string',
        ]);
        
       $data =[
        'PurchaseType' => $request->input('PropertyPurchase'),
    'Property_type_id' => $request->input('PropertyName'),
    'city_id' => $request->input('city_id'),
    'price' => $request->input('price'),
    'Dealer_id' => 2,
    'year' => 2000,
    'description' => $request->input('Description'),
    'address' => "hello",
    'status' => "Available",
    'created_at' => now(),
    'updated_at' => now(),
       ];
       
       $featuresData = [

        'Area' =>$request['area'],
        'Bedrooms' =>$request['Bedrooms'],
        'Bathrooms' =>$request['Bathrooms'],
        'Rooms' =>$request['rooms'],
        'Kitchen' =>$request['Kitchen'],

        'Air_Conditioner' =>$request['Air_Conditioner']?:0,
        'Parking' =>$request['Parking']?:0,
        'Heating' =>$request['Heating']?:0,


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



    public function show(Property $Property){
        return view("property.details",['Property'=>$Property]);
    }



    public function Destroy(Property $property){

     
        $property->delete();
        return redirect()->route('property.index');

    }
}

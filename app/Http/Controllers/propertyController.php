<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\checkout;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\Comment;
use App\Models\City;
use App\View\Components\comments;
use Illuminate\Http\Request;
use App\Http\Requests\StorePropertyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PropertyFeatures;


class propertyController

{
    public function index(Request $request){
        $user = $request->user()->role_id;
        if($user !== 2){
            return redirect()->route("login");
        }
        $Properties = \auth()->user()
        ->Properties()
        ->with(['PrimaryImage','PropertyRate'])
        ->orderBy("created_at",'desc')
        ->paginate(5);
        return view("property.index" , ['Properties'=>$Properties ]);
    }

    public function create(Request $request){
    {
        $user = $request->user()->role_id;
        if($user !== 2){
            return redirect()->route("login");
        }
        $propertyType = PropertyType::orderBy('name')->get();
        $Cities = City::groupBy('name')->get();
        $Purchases = Property::groupBy('PurchaseType')->get()->wherenotnull('PurchaseType');
        
        return view("property.create" , ['propertyType'=>$propertyType , 'Cities' => $Cities ,'Purchases' => $Purchases]);
    }
}

    public function update(Request $request, Property $property)
    {
        $user = $request->user()->role_id;
        if($user == 1){
            return redirect()->route("login");
        }
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
       
        $property->update($data);
        
        $property->features()->update($featuresData);
   if($user == 3){
    return redirect()->route('ManageProperty',$property)
        ->with('success','Property was updated');
   }
        return redirect()->route('property.edit',$property)
        ->with('success','Property was updated');
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
    'Dealer_id' => Auth::id(),
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
        
        return redirect()->route('locations.create')->with('success','Property has been added you can add it\'s location in the map');
    }

    public function edit(Property $property,Request $request){
        $user = $request->user()->role_id;
        if($user !== 2){
            return redirect()->route("login");
        }
        $propertyType = PropertyType::orderBy('name')->get();
        $Cities = City::groupBy('name')->get();
        $Purchases = Property::groupBy('PurchaseType')->get()->wherenotnull('PurchaseType');
        
      
        return view("property.Edit",['Property'=>$property,'propertyType'=>$propertyType , 'Cities' => $Cities ,'Purchases' => $Purchases]);
    }

    public function show(Property $Property){
        return view("property.details",['Property'=>$Property]);
    }

    public function Destroy(Property $property, Request $request){
       
        $property->delete();
        return redirect()->route('myProperties')
       ;
    }

    public function Comments(Request $request , Property $property){

        $user = User::where(['id'=>$request->user()->id])->first();
       
        if($request->Ananymous == "Ananymous")
        {
            $data = [

                'user_id'=>$request->user()->id,
                'Property_id'=>$property->id,
                'comment_type_id'=>2,
                'Description'=>$request->Description,

            ]; 
            $comment = Comment::create($data);
        }else{
        $data = [

            'user_id'=>$request->user()->id,
            'Property_id'=>$property->id,
            'comment_type_id'=>1,
            'Description'=>$request->Description,
        ];
        $comment = Comment::create($data);
    }
        
        return redirect()->back()->with('success','Comment Has Been Submited');
}
 public function CommentDelete(Comment $Comment , Request $request){
        
        $comment_Delete = Comment::where(['user_id'=>$request->user()->id , 'id' =>$Comment->id])->delete();

        return redirect()->back()->with('Delete','Comment Has Been Deleted');
 }

public function checkout(Request $request , Property $property){
    
    

    if($request['expDate'] > now()){
            
        if(isset($request['From']) && isset($request['To'])){
                 $request->validate([
    'From' => 'required|date',
    'To' => 'required|date|after_or_equal:From',
                    ]);
                    
               }
  
        $data=[
            'user_id'=>$request->user()->id,
            'dealer_id'=>$property->Dealer_id,
            'property_id'=>$property->id,
            'price'=>$property->price,
            'card_number'=>$request['CardNumber'],
            'expDate'=>$request['expDate'],
            'from'=>$request['From'],
            'to'=>$request['To'],
            'cvv'=>$request['cvv'],
            'address'=>$request['address'],
            'date'=>now(),
        ];
     
        
       
        $checkout = checkout::where(
            ['user_id'=>$request->user()->id , 'property_id'=>$property->id]
        )->first();

        if($checkout){
            $checkout->update($data);
        }else{
            $checkout=checkout::create($data);
        }
        $data = ['status'=>'Not Available'];
        $property->update($data);

        return redirect()->back()->with('CheckoutSuccess','Checkout Completed');
     }else{
        return redirect()->back()->with('ExpDate','Expiration date can not be in the past');
    
     }


}



}


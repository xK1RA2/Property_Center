<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Storage;
use App\Models\User;
use App\Models\City;
use App\Models\State;




class ViewProperty
{
    public function index(){
        
        $Properties = Property::
        where('status' , 'Available')
        ->with(['PrimaryImage','city','Features','PropertyRate'])
        ->orderBy('published_at','desc')
        ->get();    
        $Locations = City::groupBy('name')->get();
        $propertyType = PropertyType::groupBy('name')->get();
        $dealers = user::where('role_id','2') 
        ->limit(4)
        ->get();    
        
        return view("property.search",['Properties'=>$Properties ,'dealers' =>$dealers , 'propertyType'=>$propertyType,'Locations'=>$Locations]);
        
}




public function search(Request $request)


    {
        
      
        $PropertyType = $request->integer('Property_type_id'); 
         
        $PurchaseType=null;
        if($request->string('Purchase_Type')=="Sell")
        $PurchaseType ="Sell" ;  
         elseif($request->string('Purchase_Type')=="Rent")
         $PurchaseType ="Rent" ;  
        $city = $request->integer('city_id');  

        $priceFrom = $request->integer('price_from');  
        $priceTo = $request->integer('price_to');  
   
        $sort = $request->input('sort','-published_at');  
    
        $query = Property::where('published_at','<',now())
        ->with(['PrimaryImage','City','propertyType','Features']);
      
        if($city)
        {
            $query->where('city_id',$city); 
        }
        if($PropertyType)
        {
            $query->where('Property_type_id',$PropertyType); 
        }
        
      
       
        if($priceFrom)
        {
            $query->where('price','>=',$priceFrom); 
        }
        if($priceTo)
        {
            $query->where('price','<=',$priceTo); 
        }
      
        if($PurchaseType)
        {
            $query->where('PurchaseType',$PurchaseType); 
        }

            $query->orderBy($sort);
    
        $Properties = $query->get();
        $PropertyTypes = PropertyType::get(); 
        $Locations = City::groupBy('name')->get();

        
        return view('property.search',['Properties'=>$Properties , 'propertyType'=>$PropertyTypes,'Locations'=>$Locations]);
   
    }
  
}

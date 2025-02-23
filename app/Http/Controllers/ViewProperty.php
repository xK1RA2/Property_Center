<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;

class ViewProperty
{
    public function index(){
        $Properties = Property::where('published_at','<',now())
        ->where('status' , 'Available')
        ->with(['PrimaryImage','city','Features'])
        ->orderBy('published_at','desc')
        ->get();    
        
        $propertyType = PropertyType::groupBy('name')->get();
        $dealers = user::where('role_id','2') 
        ->limit(4)
        ->get();    
        
        return view("property.search",['Properties'=>$Properties ,'dealers' =>$dealers , 'propertyType'=>$propertyType]);
        
}
public function search(Request $request)


    {
        
      
        $PropertyType = $request->integer('Property_type_id');  
        $city = $request->integer('city_id');  
        $yearFrom = $request->integer('year_from');  
        $yearTo = $request->integer('year_to');  
        $priceFrom = $request->integer('price_from');  
        $priceTo = $request->integer('price_to');  
   
        $sort = $request->input('sort','-published_at');  
    
        $query = Property::where('published_at','<',now())
        ->with(['PrimaryImage','city','propertyType','Features']);
        if($city)
        {
            $query->where('city_id',$city); 
        }
        if($PropertyType)
        {
            $query->where('Property_type_id',$PropertyType); 
        }
        if($yearFrom)
        {
            $query->where('year','>=',$yearFrom); 
        }
        if($yearTo)
        {
            $query->where('year','<=',$yearTo); 
        }
        if($priceFrom)
        {
            $query->where('price','>=',$priceFrom); 
        }
        if($yearTo)
        {
            $query->where('price','<=',$priceTo); 
        }
        if(str_starts_with($sort,'-'))
        {
            $sort = substr($sort,1);
            $query->orderBy($sort,'desc'); 
        }
        else 
        {
            $query->orderBy($sort);
        }
        $Properties = $query;
        return view("property.search",['Properties'=>$Properties , 'propertyType'=>$PropertyType]);
   
    }
    public function wishList()
    {
        $Properties =User::find(1)
        ->favouriteProperties()
        ->with(['PrimaryImage','city','PropertyType'])
        ->paginate(5);
        return view('Property.wishlist',['Properties'=>$Properties]);
    }
}

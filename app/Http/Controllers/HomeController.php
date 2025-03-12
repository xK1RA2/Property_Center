<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\PropertyFeatures;
use Illuminate\Http\Request;

class HomeController
{
    
    public function index(){
        $Properties = Property::where('published_at','<',now())
        ->where('status' , 'Available')
        ->with(['PrimaryImage','city','Features','propertyType'])
        ->orderBy('published_at','desc')
        ->limit(6)
        ->get();    

        $dealers = User::where('role_id','2') 
        ->limit(4)
        ->get();    
        
        return view("home.index",['Properties'=>$Properties ,'dealers' =>$dealers]);
    }

    public function Orders(){

        return view("home.orders");

    }
    public function dashboard(){

        return view("home.dashboard");

    }
    public function Details(Property $Property){
        return view("home.Details",['Property'=>$Property]);


    }
}

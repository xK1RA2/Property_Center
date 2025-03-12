<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Order;
use App\Models\PropertyFeatures;
use Illuminate\Http\Request;

class AdminController
{
    
    public function index(){
     
        $users= User::get();
        $orders = Order::get();
        $Profit = Order::avg('price');
        $Properties = Property::get();
        


        return view("Admin.index",['Users'=>$users,'Orders'=>$orders,'Properties'=>$Properties,'Profit'=>$Profit]);
    
    }
    public function ManageProperty(){
        $Properties = Property::orderBy('status','asc')
        ->where('published_at','<',now())
        ->with(['PropertyType','PrimaryImage','City'])
        ->get();
        return view("Admin.ManageProperty",['Properties'=>$Properties]);
    }
    public function DestroyProperty(Property $property){

     
        $property->delete();
        return redirect()->route('ManageProperty');

    }


    public function ManageRequests(){
        return view("Admin.ManageRequests");
    }


    public function ManageUsers(){
        $Users = User::with('Role')->get();
        return view("Admin.ManageUser",['Users'=>$Users]);
    }
    public function DestroyUsers(User $user){
        $user->delete();
        return redirect()->route('ManageUsers');
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\book_preview;
use App\Models\PropertyType;
use App\Models\request_trader;
use App\Models\User;
use App\Models\Auth;
use App\Models\Order;
use App\Models\PropertyFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminController
{
    
    public function index(Request $request){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }


        $users= User::get();
        $orders = Order::get();
        $Profit = Order::avg('price');
        $Properties = Property::get();
        


        return view("Admin.index",['Users'=>$users,'Orders'=>$orders,'Properties'=>$Properties,'Profit'=>$Profit]);
    
    }
    public function ManageProperty(Request $request){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }
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


    public function ManageRequests(Request $request){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }
        $Requests = request_trader::with(['user'])->get();
        return view("Admin.ManageRequests",["Requests"=>$Requests]);
    }

    public function Request_Action( Request $request ){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }

        if($request->Action == "Accept"){
            

            
                $data = ['role_id'=>2];
                $request->user()->update($data);
                $request_trader = request_trader::where('id',$request->Request_id)->first();
                $request_trader->delete();

          

             return Redirect()->route('ManageRequests')->with('success','Request Accepted');
        }else{
            $request_trader = request_trader::where('id',$request->Request_id)->first();
            $request_trader->delete();
            return Redirect()->route('ManageRequests')->with('success','Request Declined');
        }
   
    }


    public function ManageUsers(Request $request){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }
        $Users = User::with(['Role'])->get();
        return view("Admin.ManageUser",['Users'=>$Users]);
    }
    public function DestroyUsers(User $User){
       
        if($User->role_id ==1){
            $User->Properties()->delete();
            $User->Comment()->delete();
            $User->Book_Preview()->delete();
            $User->Favourite_Property()->delete();
            $User->delete();
        }else{

        $Property = Property::where('Dealer_id',$User->id)->get();
        foreach($Property as $Prop)
        $Prop->delete();
      
        $Book =book_preview::where('user_id',$User->id)->delete();  
        dd();
        }
        return redirect()->route('ManageUsers');
    }
}

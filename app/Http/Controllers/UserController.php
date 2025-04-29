<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Property;
use App\Models\request_trader;

class UserController
{
   
    public function index(Request $request){
        $user = User::Where(['id'=>$request->user()->id])->first();

        return view("home.profile",["user"=>$user]);
        
    }
    public function Dealer_Profile(User $Dealer){
        $user = User::Where(['id'=>$Dealer->id])->first();
            $Properties = Property::where(['Dealer_id'=>$user->id])->get();
            
        return view("home.DealerProfile",["user"=>$user,'Properties'=>$Properties ]);
        
    }
    public function Request_Trader(Request $request){
       
        $requests = request_trader::Create([
            "user_id"=>$request->user()->id
        ]);

    
    return redirect()->route("user.profile");
    }
   
    public function UpdatePassword(Request $request){
  
        $user = User::where(["id"=>$request->user()->id])->first();
        if($user->password = $request->password){
        $user->password = bcrypt($request->newPassword);
        $user->save();
        return redirect()->back()->with("success","Password Updated");
    
        }
        return redirect()->back()->with("Denied","Wrong Password");
    
        
    }
}

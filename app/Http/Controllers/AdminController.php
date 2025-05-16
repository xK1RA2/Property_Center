<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\book_preview;
use App\Models\PropertyType;
use App\Models\request_trader;
use App\Models\User;
use App\Models\City;
use App\Models\checkout;
use App\Models\Auth;
use App\Models\Order;
use App\Models\PropertyFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class AdminController
{
    
    public function index(Request $request){
        $user = $request->user()->role_id;
        if($user !== 3){
            return redirect()->route("login");
        }


        $users= User::get();
        $orders = checkout::get();
        $Profit = checkout::sum('price');
        $Profit = $Profit * 0.01;
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
    public function EditProperty(Request $request , Property $property){
                $Property = Property::where('id',$property->id)->first();

                return redirect()->route('UpdateProperty',$Property);
    }
    public function UpdateProperty(Request $request , Property $property){
        $propertyType = PropertyType::orderBy('name')->get();
        $Cities = City::groupBy('name')->get();
        $Purchases = Property::groupBy('PurchaseType')->get()->wherenotnull('PurchaseType');
       
            return  view('Admin.UpdateProperty',['Property'=>$property,'propertyType'=>$propertyType ,'Cities'=>$Cities,'Purchases'=>$Purchases]);
    }
    public function DestroyProperty(Property $property){
        $Prop = Property::where('id',$property->id)->with(['Features','Book_Preview','Comment','PropertyRate','location'])->delete();
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
       
        // if($User->role_id ==1){
    

        //     // Make sure we load it with no relations (not needed unless you've eager loaded)
        //     $User->unsetRelations(); // Removes any loaded relations
            
        //     $User?->delete(); 
        // }else{

        //     $User->unsetRelations(); // Removes any loaded relations
            
        //     $User?->delete(); 
        // }
        DB::statement('DELETE FROM users WHERE id = 4');
        return redirect()->route('ManageUsers');
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\request_trader;
use App\Models\User;
use App\Models\City;
use App\Models\checkout;
use Illuminate\Http\Request;

// Access is restricted to admins by the "role:3" middleware group in routes/web.php.
class AdminController
{

    public function index(){
        $users= User::get();
        $orders = checkout::get();
        $Profit = checkout::sum('price');
        $Profit = $Profit * 0.01;
        $Properties = Property::get();

        return view("Admin.index",['Users'=>$users,'Orders'=>$orders,'Properties'=>$Properties,'Profit'=>$Profit]);
    }

    public function ManageProperty(){
        $Properties = Property::orderBy('status','asc')
        ->with(['PropertyType','PrimaryImage','City'])
        ->get();
        return view("Admin.ManageProperty",['Properties'=>$Properties]);
    }

    public function EditProperty(Property $property){
        return redirect()->route('UpdateProperty',$property);
    }

    public function UpdateProperty(Property $property){
        $propertyType = PropertyType::orderBy('name')->get();
        $Cities = City::groupBy('name')->get();
        $Purchases = Property::groupBy('PurchaseType')->get()->wherenotnull('PurchaseType');

        return view('Admin.UpdateProperty',['Property'=>$property,'propertyType'=>$propertyType ,'Cities'=>$Cities,'Purchases'=>$Purchases]);
    }

    public function DestroyProperty(Property $property){
        $property->delete();
        return redirect()->route('ManageProperty');
    }

    public function ManageRequests(){
        $Requests = request_trader::with(['user'])->get();
        return view("Admin.ManageRequests",["Requests"=>$Requests]);
    }

    public function Request_Action(Request $request){
        $request->validate([
            'Action' => 'required|in:Accept,Reject',
            'Request_id' => 'required|integer',
        ]);

        $trader = request_trader::findOrFail($request->Request_id);

        if($request->Action === "Accept"){
            User::where('id', $trader->user_id)->update(['role_id' => User::ROLE_DEALER]);
            $trader->delete();
            return redirect()->route('ManageRequests')->with('success','Request Accepted');
        }

        $trader->delete();
        return redirect()->route('ManageRequests')->with('success','Request Declined');
    }

    public function ManageUsers(){
        $Users = User::with(['Role'])->get();
        return view("Admin.ManageUser",['Users'=>$Users]);
    }

    public function DestroyUsers(Request $request, User $user){
        if($user->role_id == User::ROLE_ADMIN || $user->id === $request->user()->id){
            return redirect()->route('ManageUsers')->with('error','This user can not be deleted');
        }

        $user->delete();
        return redirect()->route('ManageUsers');
    }
}

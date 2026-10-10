<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\TraderRequest;
use App\Models\User;
use App\Models\City;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{

    public function index(){
        $users= User::get();
        $orders = Checkout::get();
        $Profit = Checkout::sum('price');
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
        $Cities = City::orderBy('name')->get()->unique('name')->values();
        $Purchases = Property::select('PurchaseType')->distinct()->whereNotNull('PurchaseType')->get();

        return view('Admin.UpdateProperty',['Property'=>$property,'propertyType'=>$propertyType ,'Cities'=>$Cities,'Purchases'=>$Purchases]);
    }

    public function DestroyProperty(Request $request, Property $property){
        Log::notice('admin.property.deleted', ['admin_id' => $request->user()->id, 'property_id' => $property->id, 'dealer_id' => $property->Dealer_id]);
        $property->delete();
        return redirect()->route('ManageProperty');
    }

    public function ManageRequests(){
        $Requests = TraderRequest::with(['user'])->get();
        return view("Admin.ManageRequests",["Requests"=>$Requests]);
    }

    public function Request_Action(Request $request){
        $request->validate([
            'Action' => 'required|in:Accept,Reject',
            'Request_id' => 'required|integer',
        ]);

        $trader = TraderRequest::findOrFail($request->Request_id);
        Log::notice('admin.dealer_request.' . strtolower($request->Action), ['admin_id' => $request->user()->id, 'user_id' => $trader->user_id]);

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

        if($user->Properties()->withTrashed()->exists()){
            return redirect()->route('ManageUsers')->with('error','This dealer still has listings; remove them first.');
        }

        Log::notice('admin.user.deleted', ['admin_id' => $request->user()->id, 'user_id' => $user->id, 'email' => $user->email]);
        $user->delete();
        return redirect()->route('ManageUsers');
    }
}

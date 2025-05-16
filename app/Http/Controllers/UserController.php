<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Rate;
use Illuminate\Support\Facades\Hash;
use App\Models\Property;
use App\Models\request_trader;

class UserController
{
   
    public function index(Request $request){
        $user = User::Where(['id'=>$request->user()->id])->with('Rate')->first();
        
        $PropertyAvg = $user->Rate->avg('Rate',2);
       
        return view("home.profile",["user"=>$user,'Avg'=>$PropertyAvg]);
        
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

    
    return redirect()->route("user.profile")->with('RequestSuccess','Request Has Been Submited');
    }
   
    public function UpdatePassword(Request $request){
  
        $user = User::where(["id"=>$request->user()->id])->first();
        
        if (Hash::check($request->password, $user->password)) {
            $user->password = bcrypt($request->newPassword); // or Hash::make()
            $user->save();
        return redirect()->back()->with("PasswordUpdated","Password Has Been Updated");
    
        }
        return redirect()->back()->with('error' , 'Wrong Password');
    
        
    }

    public function EditImage(Request $request){
            
            $user = $request->user();
            if($request->hasFile('image'))
            {
                
                if ($user->profile_image && Storage::exists($user->profile_image)) {
                    Storage::delete($user->profile_image);
                }
                $path = $request->file('image')->store('user-profile', 'public');
                $data =[

                    'profile_image'=>$path
                ];
                $user->update($data);
                
                
            }
            return redirect()->back()->with('success');
    }

    public function InformationUpdate(Request $request){
 

            $user = $request->user();
            $request->validate([
                'name'=>'required|string|max:255',
                'username'=>'required|string|max:255',
                'email'=>'required|email',
                'phone' => 'required|string|size:10',
               
            ]);
          
            $data =[
                'username'=> $request->username ,
                'name'=> $request->name,
                'email'=> $request->email,
                'phone'=> $request->phone,

            ];
            $user->update($data);
            return redirect()->back()->with('Personal','Information Updated Successfully');
    }
}

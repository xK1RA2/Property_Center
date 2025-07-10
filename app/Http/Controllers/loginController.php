<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Rate;
use Illuminate\Support\Facades\Hash;
use App\Models\Property;
use App\Models\request_trader;
class loginController
{
    public function create()
    {
        return view("auth.login");
    }
    public function store(Request $request)
    {
        
        $credentials = $request->validate([
            'email'=>['required','email'], 
            'password' => ['required','string']
        ]);
        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate(); 
            return redirect('index')
            ->with('success','Welcome Back, '.Auth::user()->name);
        }
        
      
         return  redirect()->back()->withErrors([
                'email' => 'The provided credentials do not match our records'
            ])->onlyInput('email');
}
public function logout(Request $request){
    Auth::logout();
$request->session()->regenerate();
$request->session()->regenerateToken();
    
    return redirect('index');
}

public function forgot(){
   return view("auth.forgot");
}

public function ForgotUpdate(Request $request){
    $user = User::where(["email"=>$request['email']])->first();
    $password = bcrypt($request['password']);
    $data = ['password'=>$password];
    $user->update($data);
          
    return redirect()->back()->with("PasswordUpdated","Password Has Been Updated");
    
       
  
}
}


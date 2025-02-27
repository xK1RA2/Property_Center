<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class signupController
{
    public function create()
    {
        return view("auth.signup");
    }
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'phone' => 'required|string|size:10|unique:users,phone',
            'password'=>'required','string',
            
        
            
        ]);


       
        $role = Role::where('name','user')->first();
        
        $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role_id'=> 1
   
    ]);
    
    
 
      return redirect('auth.login');
       

    }
}

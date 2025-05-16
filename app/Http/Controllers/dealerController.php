<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class dealerController
{
    
    public function index(Request $request){
       
        $dealers = User::where('role_id',2)->get()->sortByDesc(function ($user) {
            
            return $user->Rate->avg('Rate');
        });
       
        return view("home.Dealers",['Dealers'=>$dealers]);
        
    }
 
}

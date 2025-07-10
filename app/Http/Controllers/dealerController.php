<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class dealerController
{
    
    public function index(Request $request){
       
       $dealers = User::with('Rate') // eager load the Rate relationship
    ->where('role_id', 2)
    ->get()
    ->sortByDesc(function ($user) {
        return $user->Rate->avg('Rate'); // assuming 'Rate' is a numeric field in the related model
    });     
        return view("home.Dealers",['Dealers'=>$dealers]);
        
    }
 
}

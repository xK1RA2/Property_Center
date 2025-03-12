<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController
{
   
    public function index(){
      
        return view("home.profile");
        
    }
}

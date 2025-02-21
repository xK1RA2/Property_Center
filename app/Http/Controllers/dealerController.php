<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dealerController
{
    
    public function index(){
      
        return view("home.index",['dealers'=>$dealers]);
        
        

    }
}

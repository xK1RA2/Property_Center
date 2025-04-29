<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class mapsController
{
    public function index(){
        return view('home.maps');
    }
}

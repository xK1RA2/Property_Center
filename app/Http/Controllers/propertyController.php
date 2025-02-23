<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;


class propertyController

{
    public function index(){
        $Properties = User::find(7)
        ->Properties()
        ->with(['PrimaryImage'])
        ->orderBy("created_at",'desc')
        ->paginate(5);
        return view("property.index" , ['Properties'=>$Properties ]);
    }
}

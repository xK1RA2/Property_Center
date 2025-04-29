<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Comment;
use App\Models\Order;
use App\Models\PropertyFeatures;
use Illuminate\Http\Request;

class HomeController
{
    
    public function index(Request $request){
        $Properties = Property::where('published_at','<',now())
        ->where('status' , 'Available')
        ->with(['PrimaryImage','city','Features','propertyType'])
        ->orderBy('published_at','desc')
        ->limit(6)
        ->get();    
        
        $dealers = User::where('role_id','2') 
        ->limit(4)
        ->get();    
        
        return view("home.index",['Properties'=>$Properties ,'dealers' =>$dealers]);
    }
    public function Book(Request $request){
        dd($request);
    }

    public function Orders(Request $request){
        $orders =$request->user()
        ->orders()
        ->with([
            
            'Property' => function ($query) {
                $query->select('id', 'Dealer_id')
                    ->with([
                        'user:id,name',
                    ]);
            }
        ])
        ->orderBy("created_at",'desc')
        ;
        return view("home.orders",['orders'=> $orders]);

    }
    public function dashboard(Request $request){
        $user = $request->user()->role_id;
        if($user !== 2){
            return redirect()->route("login");
        }
        $user = User::where(["id"=>\Auth::user()->id]);
        $Properties = Property::where(["Dealer_id"=>\Auth::user()->id]);
      
        
        return view("home.dash",["Dealer" =>$user ,"Properties"=>$Properties  ]);

    }
    public function Details(Property $Property){
        $comments = Comment::where(['Property_id'=>$Property->id])->with('user')
        ->get();
        return view("home.Details",['Property'=>$Property,'Comments'=>$comments]);
    }
   
}

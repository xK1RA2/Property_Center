<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\checkout;
use App\Models\book_preview;
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
        
        $dealers = User::where('role_id', 2)
        ->with('Rate') // Load the Rate relationship
        ->get() // Get the collection
        ->sortByDesc(function ($user) {
            return $user->Rate->avg('Rate'); // Sort by average rating
        })
        ->take(4);
        return view("home.index",['Properties'=>$Properties ,'dealers' =>$dealers]);
    }
   

    public function Orders(Request $request){
     $orders = Checkout::where('user_id', $request->user()->id)
    ->with('property', 'user')
    ->get();
     return view("home.orders",['orders'=> $orders]);

    }
    public function dashboard(Request $request){
        $user = $request->user()->role_id;
        if($user !== 2){
            return redirect()->route("login");
        }
        $user = User::where(["id"=>\Auth::user()->id]);
        $Properties = Property::where(["Dealer_id"=>$request->user()->id])->get();
        $Requests = book_preview::where('dealer_id',$request->user()->id)->orderByDesc('status')->with('Property','User')->get();
        $checkouts = checkout::where('dealer_id',$request->user()->id)->get();

        return view("home.dash",["Dealer" =>$user ,"Properties"=>$Properties,'Requests'=>$Requests ,'Checkouts'=>$checkouts ]);

    }
    public function Details(Property $Property){
        $comments = Comment::where(['Property_id'=>$Property->id])->with('user')
        ->get();
        return view("home.Details",['Property'=>$Property,'Comments'=>$comments]);
    }

    public function Booking(Request $request , Property $property){
            if($request->date < now()){
               return redirect()->back()->with('Past','Time Can Not Be In The Past');
            }
        $booking = book_preview::
        where(['property_id'=>$property->id ,
                       'user_id'=>$request->user()->id
                      ])->first();
                      if(!$booking){

                        if($request['description']){
                            $description = $request['description'];
                        }else{
                            $description = "Empty";
                        }
        $data =[
                'user_id'=>$request->user()->id,
                'dealer_id'=>$property->Dealer_id,
                'property_id'=>$property->id,
                'Date'=>$request['date'],
                'description'=>$description,
                'status'=>'Pending'
        ];
        $book = book_preview::create($data);
        return redirect()->back()->with('BookingSuccess','Booking Has Been Submited');
    }else{
        
        if($request->description){
            $description = $request->description;
        }else{
            $description = "Empty";
        }
        $data =[
            'user_id'=>$request->user()->id,
            'dealer_id'=>$property->Dealer_id,
            'property_id'=>$property->id,
            'Date'=>$request->date,
            'description'=>$description,
            'status'=>'Pending'
    ];
    $book = book_preview::where(['property_id'=>$property->id ,
    'user_id'=>$request->user()->id
   ])->update($data);
    return redirect()->back()->with('BookingUpdated','Booking Has Been Updated');
    }
    }

    public function BookAccept(Request $request , book_preview $Req){
        
        $data=[
            'status'=>'Approved'
        ];
        $Request = book_preview::where('id',$Req->id)->update($data);
     
     
     
        return redirect()->back()->with('success','Request Accepted');
   
    }
   
    public function BookRefuse(Request $request , book_preview $Req){
        $data=[
            'status'=>'Rejected'
        ];
        $Request = book_preview::where('id',$Req->id)->update($data);
     
     
     
        return redirect()->back()->with('Rejected','Request Rejected');
   
    }
   
}

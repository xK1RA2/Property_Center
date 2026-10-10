<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Checkout::where('user_id', $request->user()->id)
            ->with(['Property', 'User'])
            ->latest()
            ->get();

        return view('home.orders', ['orders' => $orders]);
    }
}

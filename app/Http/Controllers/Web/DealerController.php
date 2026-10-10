<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = User::with('Rate')
            ->where('role_id', User::ROLE_DEALER)
            ->get()
            ->sortByDesc(fn (User $user) => $user->Rate->avg('Rate'));

        return view('home.Dealers', ['Dealers' => $dealers]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $properties = Property::where('status', 'Available')
            ->with(['PrimaryImage', 'city', 'Features', 'propertyType'])
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('home.index', [
            'Properties' => $properties,
            'dealers' => $this->topDealers(4),
        ]);
    }

    private function topDealers(int $limit)
    {
        return User::where('role_id', User::ROLE_DEALER)
            ->with('Rate')
            ->get()
            ->sortByDesc(fn (User $user) => $user->Rate->avg('Rate'))
            ->take($limit);
    }
}

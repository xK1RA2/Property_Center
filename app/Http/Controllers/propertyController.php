<?php

namespace App\Http\Controllers;
namespace App\Models;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class propertyController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('property.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('property.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $Property)
    {
        if(!$Property->published_at)
        {
            abort(404);
        }
        return view("Property.show",['Property'=>$Property]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

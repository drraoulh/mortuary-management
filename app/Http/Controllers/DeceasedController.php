<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeceasedController extends Controller
{
   public function index(Request $request)
{
    $query = Deceased::query();

    if ($request->search) {
        $query->where('full_name', 'like', '%' . $request->search . '%');
    }

    $deceaseds = $query->get();

    return view('deceased.index', compact('deceaseds'));
}
    public function create()
    {
        return view('deceased.create');
    }

   public function store(Request $request)
   
{
    $request->validate([
        'full_name' => 'required',
        'gender' => 'required',
        'date_of_death' => 'required',
        'admission_date' => 'required',
        'location_address' => 'nullable|string|max:255',
'latitude' => 'nullable|numeric|between:-90,90',
'longitude' => 'nullable|numeric|between:-180,180',
    ]);

    if ($request->room_type == 'vip') {
        $price = 25000;
    } elseif ($request->room_type == 'vvip') {
        $price = 50000;
    } else {
        $price = 10000;
    }
    $uniqueCode = 'DEC-' . strtoupper(substr(md5(time()),0,8));

    $lastId = \App\Models\Deceased::count() + 1;

$identifier = 'MOR-' . date('Y') . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

    $deceased = Deceased::create([
    'user_id' => auth()->id(),
    'full_name' => $request->full_name,
    'gender' => $request->gender,
    'date_of_death' => $request->date_of_death,
    'cause_of_death' => $request->cause_of_death,
    'admission_date' => $request->admission_date,
        'security_key' => Str::random(10),
        'location_address' => $request->location_address,
'latitude' => $request->latitude,
'longitude' => $request->longitude,
    ]);

    return redirect()
->route('deceased.index')
->with(
    'success',
    'Deceased Registered Successfully. Identifier: '
    .$deceased->identifier
);

}

    public function edit(Deceased $deceased)
    {
        return view('deceased.edit', compact('deceased'));
    }

    public function update(Request $request, Deceased $deceased)
    {
        $deceased->update($request->all());
        return redirect()->route('deceased.index');
    }

    public function destroy(Deceased $deceased)
    {
        $deceased->delete();
        return redirect()->route('deceased.index');
    }
    public function verifyForm()
{
    return view('deceased.verify');
}

public function verify(Request $request)
{
    $deceased = Deceased::where('security_key', $request->key)->first();

    if (!$deceased) {
        return back()->with('error', 'Invalid Key');
    }

    return view('deceased.show', compact('deceased'));
}

}
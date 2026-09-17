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

        $deceaseds = $query->latest()->get();

        return view('deceased.index', compact('deceaseds'));
    }

    public function create()
    {
        return view('deceased.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:50',
            'date_of_death' => 'required|date',
            'admission_date' => 'required|date',
            'cause_of_death' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $roomType = $request->room_type ?: 'normal';

        $price = match ($roomType) {
            'vip' => 25000,
            'vvip' => 50000,
            default => 10000,
        };

        $lastId = Deceased::count() + 1;
        $identifier = 'MOR-' . date('Y') . '-' . str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

        $deceased = Deceased::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_death' => $request->date_of_death,
            'cause_of_death' => $request->cause_of_death,
            'admission_date' => $request->admission_date,
            'room_type' => $roomType,
            'price' => $price,
            'identifier' => $identifier,
            'security_key' => Str::random(10),
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()
            ->route('deceased.index')
            ->with(
                'success',
                'Deceased Registered Successfully. Identifier: ' . $deceased->identifier
            );
    }

    public function show(Deceased $deceased)
    {
        return view('deceased.show', compact('deceased'));
    }

    public function edit(Deceased $deceased)
    {
        return view('deceased.edit', compact('deceased'));
    }

    public function update(Request $request, Deceased $deceased)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:50',
            'date_of_death' => 'required|date',
            'admission_date' => 'required|date',
            'cause_of_death' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'full_name',
            'gender',
            'date_of_death',
            'cause_of_death',
            'admission_date',
            'room_type',
            'location_address',
            'latitude',
            'longitude',
        ]);

        if (!empty($data['room_type'])) {
            $data['price'] = match ($data['room_type']) {
                'vip' => 25000,
                'vvip' => 50000,
                default => 10000,
            };
        }

        $deceased->update($data);

        return redirect()->route('deceased.index')->with('success', 'Deceased updated successfully.');
    }

    public function destroy(Deceased $deceased)
    {
        $deceased->delete();

        return redirect()->route('deceased.index')->with('success', 'Deceased deleted successfully.');
    }

    public function verifyForm()
    {
        return view('deceased.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $deceased = Deceased::where('security_key', $request->key)->first();

        if (!$deceased) {
            return back()->with('error', 'Invalid Key');
        }

        return view('deceased.show', compact('deceased'));
    }
}

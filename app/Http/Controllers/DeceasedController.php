<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\StorageRoom;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class DeceasedController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:manager,admin', only: ['destroy']),
        ];
    }

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
        return view('deceased.create', [
            'rooms' => StorageRoom::orderBy('room_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'gender' => 'required',
            'date_of_death' => 'required|date',
            'admission_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'release_date' => 'nullable|date',
            'room_type' => 'nullable|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $price = match ($request->room_type) {
            'vip' => 25000,
            'vvip' => 50000,
            default => 10000,
        };

        $nextNumber = (int) Deceased::max('id') + 1;

        $identifier = 'MOR-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $deceased = Deceased::create([
            'user_id' => auth()->id(),
            'identifier' => $identifier,
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'date_of_death' => $request->date_of_death,
            'cause_of_death' => $request->cause_of_death,
            'admission_date' => $request->admission_date,
            'release_date' => $request->release_date,
            'room_name' => $request->room_name,
            'room_type' => $request->room_type ?? 'normal',
            'price' => $price,
            'security_key' => Str::random(10),
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()
            ->route('deceased.index')
            ->with(
                'success',
                'Deceased registered successfully. Identifier: ' . $deceased->identifier
                . ' | Verification key: ' . $deceased->security_key
            );
    }

    public function show(Deceased $deceased)
    {
        return view('deceased.show', compact('deceased'));
    }

    public function edit(Deceased $deceased)
    {
        return view('deceased.edit', [
            'deceased' => $deceased,
            'rooms' => StorageRoom::orderBy('room_number')->get(),
        ]);
    }

    public function update(Request $request, Deceased $deceased)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'required|date',
            'cause_of_death' => 'required|string|max:255',
            'admission_date' => 'required|date',
            'release_date' => 'nullable|date',
            'room_name' => 'nullable|string|max:50',
            'room_type' => 'nullable|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
        ]);

        $deceased->update($data);

        return redirect()
            ->route('deceased.show', $deceased)
            ->with('success', 'Record updated.');
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

        if (! $deceased) {
            return back()->with('error', 'Invalid Key');
        }

        if ($request->user()->isClient()) {
            $request->user()->verifiedDeceased()->syncWithoutDetaching([$deceased->id]);
        }

        return view('deceased.show', compact('deceased'));
    }
}

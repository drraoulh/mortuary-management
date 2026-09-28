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
        $rooms = StorageRoom::orderBy('room_number')->get();

        return view('deceased.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'required|date',
            'admission_date' => 'required|date',
            'cause_of_death' => 'nullable|string|max:255',
            'room_name' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $roomType = $request->room_type ?: 'normal';

        // max(id) rather than count(): after a deletion count() reuses an existing identifier.
        $nextNumber = (int) Deceased::max('id') + 1;
        $identifier = 'MOR-' . date('Y') . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        $deceased = Deceased::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'date_of_death' => $request->date_of_death,
            'cause_of_death' => $request->cause_of_death,
            'admission_date' => $request->admission_date,
            'room_name' => $request->room_name,
            'room_type' => $roomType,
            'price' => $this->priceFor($roomType),
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
        $rooms = StorageRoom::orderBy('room_number')->get();

        return view('deceased.edit', compact('deceased', 'rooms'));
    }

    public function update(Request $request, Deceased $deceased)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'required|date',
            'admission_date' => 'required|date',
            'release_date' => 'nullable|date',
            'cause_of_death' => 'nullable|string|max:255',
            'room_name' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|in:normal,vip,vvip',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'full_name',
            'gender',
            'date_of_birth',
            'date_of_death',
            'cause_of_death',
            'admission_date',
            'release_date',
            'room_name',
            'room_type',
            'location_address',
            'latitude',
            'longitude',
        ]);

        if (!empty($data['room_type'])) {
            $data['price'] = $this->priceFor($data['room_type']);
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

        // Families keep access to the record (dashboard, payments, faire-part) after verifying once.
        if ($request->user()->isClient()) {
            $request->user()->verifiedDeceased()->syncWithoutDetaching([$deceased->id]);
        }

        return view('deceased.show', compact('deceased'));
    }

    private function priceFor(string $roomType): int
    {
        return match ($roomType) {
            'vip' => 25000,
            'vvip' => 50000,
            default => 10000,
        };
    }
}

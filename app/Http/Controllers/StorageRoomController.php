<?php

namespace App\Http\Controllers;

use App\Models\StorageRoom;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class StorageRoomController extends Controller implements HasMiddleware
{
    public const STATUSES = [
        'available' => 'Available',
        'occupied' => 'Occupied',
        'maintenance' => 'Maintenance',
    ];

    /**
     * Staff can consult rooms; only managers and admins change them.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:manager,admin', except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $rooms = StorageRoom::withCount('deceaseds')->orderBy('room_number')->get();

        return view('storage.index', compact('rooms'));
    }

    public function create()
    {
        return view('storage.create', ['statuses' => self::STATUSES]);
    }

    public function store(Request $request)
    {
        StorageRoom::create($this->validated($request));

        return redirect()->route('storage.index')->with('success', 'Storage room created.');
    }

    public function show(StorageRoom $storage)
    {
        $storage->load('deceaseds');

        return view('storage.show', compact('storage'));
    }

    public function edit(StorageRoom $storage)
    {
        return view('storage.edit', [
            'storage' => $storage,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, StorageRoom $storage)
    {
        $storage->update($this->validated($request, $storage));

        return redirect()->route('storage.index')->with('success', 'Storage room updated.');
    }

    public function destroy(StorageRoom $storage)
    {
        $storage->delete();

        return redirect()->route('storage.index')->with('success', 'Storage room deleted.');
    }

    private function validated(Request $request, ?StorageRoom $storage = null): array
    {
        return $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('storage_rooms', 'room_number')->ignore($storage?->id),
            ],
            'capacity' => 'required|integer|min:1',
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
        ]);
    }
}

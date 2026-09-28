<?php

namespace App\Http\Controllers;

use App\Models\StorageRoom;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StorageRoomController extends Controller implements HasMiddleware
{
    public const STATUSES = [
        'available' => 'Available',
        'occupied' => 'Occupied',
        'maintenance' => 'Maintenance',
    ];

    public static function middleware(): array
    {
        return [
            new Middleware('role:manager,admin', except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $rooms = StorageRoom::with('deceaseds')->orderBy('room_number')->get();

        return view('storage.index', compact('rooms'));
    }

    public function create()
    {
        return view('storage.create', ['statuses' => self::STATUSES]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        StorageRoom::create($data);

        return redirect()->route('storage.index')->with('success', 'Room added.');
    }

    public function show(StorageRoom $storage)
    {
        $storage->load('deceaseds');

        return view('storage.show', compact('storage'));
    }

    public function edit(StorageRoom $storage)
    {
        return view('storage.edit', ['storage' => $storage, 'statuses' => self::STATUSES]);
    }

    public function update(Request $request, StorageRoom $storage)
    {
        $storage->update($this->validated($request, $storage));

        return redirect()->route('storage.index')->with('success', 'Room updated.');
    }

    public function destroy(StorageRoom $storage)
    {
        $storage->delete();

        return redirect()->route('storage.index')->with('success', 'Room deleted.');
    }

    private function validated(Request $request, ?StorageRoom $room = null): array
    {
        return $request->validate([
            'room_number' => 'required|string|max:50|unique:storage_rooms,room_number' . ($room ? ',' . $room->id : ''),
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:' . implode(',', array_keys(self::STATUSES)),
        ]);
    }
}

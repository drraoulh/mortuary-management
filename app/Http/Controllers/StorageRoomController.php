<?php

namespace App\Http\Controllers;

use App\Models\StorageRoom;
use Illuminate\Http\Request;

class StorageRoomController extends Controller
{
    public function index()
    {
        $rooms = StorageRoom::all();
        return view('storage.index', compact('rooms'));
    }

    public function create()
    {
        return view('storage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required',
            'capacity' => 'required|integer',
            'status' => 'required'
        ]);

        StorageRoom::create($request->all());

        return redirect()->route('storage.index');
    }

    public function show(StorageRoom $storage)
    {
        return view('storage.show', compact('storage'));
    }

    public function edit(StorageRoom $storage)
    {
        return view('storage.edit', compact('storage'));
    }

    public function update(Request $request, StorageRoom $storage)
    {
        $request->validate([
            'room_number' => 'required',
            'capacity' => 'required|integer',
            'status' => 'required'
        ]);

        $storage->update($request->all());

        return redirect()->route('storage.index');
    }

    public function destroy(StorageRoom $storage)
    {
        $storage->delete();

        return redirect()->route('storage.index');
    }
}
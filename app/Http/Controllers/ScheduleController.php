<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Deceased;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{ 
    public function confirm($id)
{
    $schedule = Schedule::findOrFail($id);

    $schedule->status = 'confirmed';

    $schedule->save();

    return back()->with(
        'success',
        'Schedule Confirmed'
    );
}
    public function index()
    {
        $schedules = Schedule::all();
        return view('schedule.index', compact('schedules'));
    }

   public function create()
{
    $deceaseds = \App\Models\Deceased::all();

    return view('schedule.create', compact('deceaseds'));
}

    public function store(Request $request)
{
    $request->validate([
    'deceased_id' => 'required',
    'pickup_date' => 'required|date',
    'pickup_time' => 'required',
    'burial_date' => 'required|date',
]);

   Schedule::create([
    'deceased_id' => $request->deceased_id,
    'pickup_date' => $request->pickup_date,
    'pickup_time' => $request->pickup_time,
    'burial_date' => $request->burial_date,
    'release_date' => $request->pickup_date,
    'notes' => $request->notes,
]);

    return redirect()->route('schedule.index')
        ->with('success', 'Schedule created successfully');
}
}
<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Payment;
use App\Models\StorageRoom;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('dashboard', [
            'totalDeceased' => Deceased::where('user_id', $user->id)->count(),

            'availableRooms' => StorageRoom::where(
                'status',
                'available'
            )->count(),

            'pendingPayments' => Payment::where(
                'user_id',
                $user->id
            )->where(
                'status',
                'pending'
            )->count(),
        ]);
    }
}
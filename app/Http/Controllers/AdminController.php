<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\StorageRoom;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalDeceased = Deceased::count();

        $totalPayments = Payment::count();

        $totalRevenue = Payment::sum('amount');

        $totalSchedules = Schedule::count();

        $totalUsers = User::count();

        $availableRooms = StorageRoom::where('status', 'available')
            ->count();

        $pendingPayments = Payment::where('status', 'pending')
            ->count();

        return view('admin.dashboard', compact(
            'totalDeceased',
            'totalPayments',
            'totalRevenue',
            'totalSchedules',
            'totalUsers',
            'availableRooms',
            'pendingPayments'
        ));
    }
}
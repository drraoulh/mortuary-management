<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\StorageRoom;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Send each user to the dashboard that matches their role.
     */
    public function index()
    {
        return redirect()->route(auth()->user()->dashboardRoute());
    }

    /**
     * Mortuary staff: their own registrations, payments and today's work.
     */
    public function staff()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        return view('staff.dashboard', [
            'myDeceasedCount' => $user->deceaseds()->count(),

            'myDeceasedThisMonth' => $user->deceaseds()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),

            'myPaidTotal' => $user->payments()->paid()->sum('amount'),

            'myPendingPayments' => $user->payments()->pending()->count(),

            'availableRooms' => StorageRoom::where('status', 'available')->count(),

            'todayPickups' => Schedule::whereDate('pickup_date', $today)->count(),

            'upcomingPickups' => Schedule::with('deceased')
                ->whereDate('pickup_date', '>=', $today)
                ->orderBy('pickup_date')
                ->orderBy('pickup_time')
                ->limit(5)
                ->get(),

            'recentDeceased' => $user->deceaseds()->latest()->limit(5)->get(),

            'recentPayments' => $user->payments()->with('deceased')->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Family / client: their payments and the deceased they verified.
     */
    public function family()
    {
        $user = auth()->user();

        return view('family.dashboard', [
            'paidTotal' => $user->payments()->paid()->sum('amount'),

            'pendingPayments' => $user->payments()->pending()->count(),

            'paymentsCount' => $user->payments()->count(),

            'myDeceased' => $user->visibleDeceased()
                ->with('schedule')
                ->orderBy('full_name')
                ->get(),

            'recentPayments' => $user->payments()->with('deceased')->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Staff manager: supervises the whole mortuary and the staff team.
     */
    public function manager()
    {
        $today = now()->toDateString();

        $totalRooms = StorageRoom::count();
        $availableRooms = StorageRoom::where('status', 'available')->count();

        return view('manager.dashboard', [
            'totalDeceased' => Deceased::count(),

            'admittedThisMonth' => Deceased::where('created_at', '>=', now()->startOfMonth())->count(),

            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'occupancyRate' => $totalRooms > 0
                ? round((($totalRooms - $availableRooms) / $totalRooms) * 100)
                : 0,

            'revenueThisMonth' => Payment::paid()
                ->where('payment_date', '>=', now()->startOfMonth()->toDateString())
                ->sum('amount'),

            'pendingPayments' => Payment::pending()
                ->with(['deceased', 'user'])
                ->latest()
                ->limit(6)
                ->get(),

            'pendingPaymentsCount' => Payment::pending()->count(),

            'pendingSchedules' => Schedule::with('deceased')
                ->where('status', 'pending')
                ->orderBy('pickup_date')
                ->limit(6)
                ->get(),

            'todayPickups' => Schedule::whereDate('pickup_date', $today)->count(),

            'rooms' => StorageRoom::orderBy('room_number')->get(),

            'staffMembers' => User::where('role', User::ROLE_STAFF)
                ->withCount(['deceaseds', 'payments'])
                ->orderByDesc('deceaseds_count')
                ->limit(8)
                ->get(),
        ]);
    }
}

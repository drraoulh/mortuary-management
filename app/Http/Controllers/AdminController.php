<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\StorageRoom;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'roleCounts' => $roleCounts,

            'totalDeceased' => Deceased::count(),
            'totalSchedules' => Schedule::count(),

            'totalRooms' => StorageRoom::count(),
            'availableRooms' => StorageRoom::where('status', 'available')->count(),

            'totalPayments' => Payment::count(),
            'totalRevenue' => Payment::paid()->sum('amount'),
            'pendingPayments' => Payment::pending()->count(),

            'monthlyRevenue' => $this->monthlyRevenue(),

            'recentUsers' => User::latest()->limit(6)->get(),

            'recentPayments' => Payment::with(['deceased', 'user'])
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }

    /**
     * Paid amounts for the last 6 months, oldest first: ['Apr 2026' => 150000, ...].
     */
    private function monthlyRevenue(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $months[$start->copy()->addMonths($i)->format('M Y')] = 0;
        }

        Payment::paid()
            ->where('payment_date', '>=', $start->toDateString())
            ->get(['amount', 'payment_date'])
            ->each(function (Payment $payment) use (&$months) {
                $key = Carbon::parse($payment->payment_date)->format('M Y');

                if (isset($months[$key])) {
                    $months[$key] += (float) $payment->amount;
                }
            });

        return $months;
    }
}

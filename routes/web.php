<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeceasedController;
use App\Http\Controllers\StorageRoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GeolocationController;
use App\Http\Controllers\FairePartController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/geolocation', [GeolocationController::class, 'index'])->name('geolocation.index');
    Route::post('/geolocation/search', [GeolocationController::class, 'search'])->name('geolocation.search');

    Route::resource('deceased', DeceasedController::class);

    Route::resource('storage-rooms', StorageRoomController::class)
        ->parameters(['storage-rooms' => 'storage'])
        ->names('storage');

    Route::resource('payments', PaymentController::class)->only([
        'index',
        'create',
        'store',
        'show',
    ]);

    Route::get('/payments/{payment}/processing', [PaymentController::class, 'processing'])
        ->name('payments.processing');

    Route::get('/payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])
        ->name('payments.check-status');

    Route::post('/payments/{payment}/simulate-success', [PaymentController::class, 'simulateSuccess'])
        ->name('payments.simulate-success');

    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])
        ->name('payments.receipt');

    Route::resource('schedule', ScheduleController::class)->only([
        'index',
        'create',
        'store',
    ]);

    Route::get('/verify', [DeceasedController::class, 'verifyForm'])->name('deceased.verify.form');
    Route::post('/verify', [DeceasedController::class, 'verify'])->name('deceased.verify');

    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::post('/payment/{id}/confirm', [PaymentController::class, 'confirm'])
        ->name('payment.confirm');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');

    Route::get('/staff-dashboard', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');

    Route::middleware(['staff'])->group(function () {
        Route::get('/staff', function () {
            return view('staff.dashboard');
        })->name('staff.home');
    });

    Route::get('/faire-part', [FairePartController::class, 'create'])->name('faire-part.create');
    Route::post('/faire-part/generate', [FairePartController::class, 'generate'])->name('faire-part.generate');
});

Route::post('/schedule/{id}/confirm', [ScheduleController::class, 'confirm'])
    ->middleware('auth')
    ->name('schedule.confirm');

require __DIR__.'/auth.php';

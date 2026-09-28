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
use App\Http\Controllers\AiAssistantController;
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
| All authenticated users (families, staff, managers, admins)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Sends every user to the dashboard of their role.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/geolocation', [GeolocationController::class, 'index'])->name('geolocation.index');
    Route::post('/geolocation/search', [GeolocationController::class, 'search'])->name('geolocation.search');

    // Families only see and pay for deceased they verified (see User::visibleDeceased()).
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

    Route::get('/verify', [DeceasedController::class, 'verifyForm'])->name('deceased.verify.form');
    Route::post('/verify', [DeceasedController::class, 'verify'])->name('deceased.verify');

    Route::get('/faire-part', [FairePartController::class, 'create'])->name('faire-part.create');
    Route::post('/faire-part/generate', [FairePartController::class, 'generate'])->name('faire-part.generate');
});

/*
|--------------------------------------------------------------------------
| Family / Client
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/family', [DashboardController::class, 'family'])->name('family.dashboard');
});

/*
|--------------------------------------------------------------------------
| Mortuary staff, staff managers and admins
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff,manager,admin'])->group(function () {
    Route::get('/staff', [DashboardController::class, 'staff'])->name('staff.dashboard');
    Route::redirect('/staff-dashboard', '/staff');

    Route::resource('deceased', DeceasedController::class);

    Route::resource('storage-rooms', StorageRoomController::class)
        ->parameters(['storage-rooms' => 'storage'])
        ->names('storage');

    Route::resource('schedule', ScheduleController::class)->only([
        'index',
        'create',
        'store',
    ]);

    Route::get('/ai', [AiAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai/generate', [AiAssistantController::class, 'generate'])->name('ai.generate');
});

/*
|--------------------------------------------------------------------------
| Staff managers and admins
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:manager,admin'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'manager'])->name('manager.dashboard');

    Route::post('/payment/{id}/confirm', [PaymentController::class, 'confirm'])
        ->name('payment.confirm');

    Route::post('/schedule/{id}/confirm', [ScheduleController::class, 'confirm'])
        ->name('schedule.confirm');
});

/*
|--------------------------------------------------------------------------
| Admins
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
});

/*
|--------------------------------------------------------------------------
| CamPay webhook (public)
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/campay', [PaymentController::class, 'webhook'])
    ->name('webhooks.campay');

require __DIR__.'/auth.php';

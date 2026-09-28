<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeceasedController;
use App\Http\Controllers\StorageRoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GeolocationController;
use App\Http\Controllers\FairePartController;


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
| Authenticated User Routes (all roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    | /dashboard sends every user to the dashboard of their role.
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    /*
    | Profile
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /*
    | Geolocation
    */

    Route::get('/geolocation', [GeolocationController::class, 'index'])
        ->name('geolocation.index');

    Route::post('/geolocation/search', [GeolocationController::class, 'search'])
        ->name('geolocation.search');


    /*
    | Payments (families see and pay only for deceased they verified)
    */

    Route::resource('payments', PaymentController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/payments/{payment}/processing', [PaymentController::class, 'processing'])
        ->name('payments.processing');

    Route::get('/payments/{payment}/check-status', [PaymentController::class, 'checkStatus'])
        ->name('payments.check-status');

    /*
    | TEST ONLY: simulate a successful payment
    */

    Route::post('/payments/{payment}/simulate-success', [PaymentController::class, 'simulateSuccess'])
        ->name('payments.simulate-success');

    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])
        ->name('payments.receipt');


    /*
    | Deceased Verification
    */

    Route::get('/verify', [DeceasedController::class, 'verifyForm'])
        ->name('deceased.verify-form');

    Route::post('/verify', [DeceasedController::class, 'verify'])
        ->name('deceased.verify');


    /*
    | Faire-part
    */

    Route::get('/faire-part', [FairePartController::class, 'create'])
        ->name('faire-part.create');

    Route::post('/faire-part/generate', [FairePartController::class, 'generate'])
        ->name('faire-part.generate');
});


/*
|--------------------------------------------------------------------------
| Family / Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/family', [DashboardController::class, 'family'])
        ->name('family.dashboard');
});


/*
|--------------------------------------------------------------------------
| Mortuary Staff Routes (staff, managers and admins)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff,manager,admin'])->group(function () {

    Route::get('/staff', [DashboardController::class, 'staff'])
        ->name('staff.dashboard');

    Route::redirect('/staff-dashboard', '/staff');

    Route::resource('deceased', DeceasedController::class);

    Route::resource('storage', StorageRoomController::class);

    Route::resource('schedule', ScheduleController::class);
});


/*
|--------------------------------------------------------------------------
| Staff Manager + Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:manager,admin'])->group(function () {

    Route::get('/manager', [DashboardController::class, 'manager'])
        ->name('manager.dashboard');

    Route::post('/payment/{id}/confirm', [PaymentController::class, 'confirm'])
        ->name('payment.confirm');

    Route::post('/schedule/{id}/confirm', [ScheduleController::class, 'confirm'])
        ->name('schedule.confirm');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/admin/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateRole'])
        ->name('admin.users.role');
});


require __DIR__.'/auth.php';

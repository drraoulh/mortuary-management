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


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


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
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    auth()->logout();

    return redirect('/');

})->name('logout');


/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get('/geolocation', [GeolocationController::class, 'index'])
    ->name('geolocation.index');

Route::post('/geolocation/search', [GeolocationController::class, 'search'])
    ->name('geolocation.search');


    /*
    |--------------------------------------------------------------------------
    | Deceased
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'deceased',
        DeceasedController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'storage',
        StorageRoomController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'payments',
        PaymentController::class
    );


    /*
    | Payment processing page
    */

    Route::get(
        '/payments/{payment}/processing',
        [PaymentController::class, 'processing']
    )->name('payments.processing');


    /*
    | Check CamPay transaction status
    */

    Route::get(
        '/payments/{payment}/check-status',
        [PaymentController::class, 'checkStatus']
    )->name('payments.check-status');


    /*
    | TEST ONLY:
    | Simulate successful payment
    */

    Route::post(
        '/payments/{payment}/simulate-success',
        [PaymentController::class, 'simulateSuccess']
    )->name('payments.simulate-success');


    /*
    | Download PDF receipt
    |
    | IMPORTANT:
    | This is the ONLY route using payments.receipt
    */

    Route::get(
        '/payments/{payment}/receipt',
        [PaymentController::class, 'downloadReceipt']
    )->name('payments.receipt');


    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'schedule',
        ScheduleController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Deceased Verification
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/verify',
        [DeceasedController::class, 'verifyForm']
    );

    Route::post(
        '/verify',
        [DeceasedController::class, 'verify']
    );


    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin',
        [AdminController::class, 'dashboard']
    )->name('admin.dashboard');


    /*
    | Admin confirms payment
    */

    Route::post(
        '/payment/{id}/confirm',
        [PaymentController::class, 'confirm']
    )->name('payment.confirm');

});


/*
|--------------------------------------------------------------------------
| Staff Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/staff-dashboard',
    function () {
        return view('staff.dashboard');
    }
)->middleware(['auth'])->name('staff.dashboard');


Route::middleware(['auth', 'staff'])->group(function () {

    Route::get(
        '/staff',
        function () {
            return view('staff.dashboard');
        }
    );

});


/*
|--------------------------------------------------------------------------
| Admin Users
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/users',
    [UserController::class, 'index']
)->middleware(['auth']);


/*
|--------------------------------------------------------------------------
| Schedule Confirmation
|--------------------------------------------------------------------------
*/

Route::post(
    '/schedule/{id}/confirm',
    [ScheduleController::class, 'confirm']
)->name('schedule.confirm');


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/faire-part/{id}', [FairePartController::class, 'generate'])
    ->name('faire-part.generate');
    
Route::get('/faire-part', [FairePartController::class, 'create'])
    ->name('faire-part.create');

Route::post('/faire-part/generate', [FairePartController::class, 'generate'])
    ->name('faire-part.generate');

require __DIR__.'/auth.php';
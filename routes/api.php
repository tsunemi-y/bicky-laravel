<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/reservation', [ReservationController::class, 'index'])->name('getReservation');
    Route::post('/saveReservation', [ReservationController::class, 'store'])->name('saveReservation');
    Route::post('/deleteReservation', [ReservationController::class, 'deleteDatetime'])->name('deleteDatetime');

    // Route::get('/getUserInfoSendReciept', [ReservationController::class, 'getUserInfoSendReciept'])->name('getUserInfoSendReciept');
    Route::post('/sendReceipt', [UserController::class, 'sendReceipt'])->name('sendReceipt');

    Route::get('/users', [UserController::class, 'getUsers'])->name('users');
    Route::put('/updateFee/{user}', [UserController::class, 'updateFee'])->name('updateFee');
    Route::post('/sendEvaluation', [UserController::class, 'sendEvaluation'])->name('sendEvaluation');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeeController;

use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\LineMessengerController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// LINE メッセージ送信用
Route::get('/line/sendReservation', [LineMessengerController::class, 'sendReservationListMessage'])->name('line.sendReservation');

// ===============ユーザー画面　ここから============

// トップ画面
Route::get('/', function () {
    return view('pages.top');
})->name('top');

// ご挨拶画面
Route::get('greeting', function () {
    return view('pages.greeting');
})->name('greeting');

// アクセス画面
Route::get('access', function () {
    return view('pages.access');
})->name('access');

// 訓練内容画面
Route::get('overview', function () {
    return view('pages.overview');
})->name('overview');

// 遠方の方へ画面
Route::get('remote', function () {
    return view('pages.remote');
})->name('remote');

// 料金画面
Route::get('fee', [FeeController::class, 'index'])->name('fee');

Route::get('introduction', function () {
    return view('pages.introduction');
})->name('introduction');

Route::get('/reservation', [ReservationController::class, 'dispReservationTop'])->name('reservationTop')->middleware('auth');
Route::get('/reservationForm', [ReservationController::class, 'dispReservationForm'])->name('reservationForm');
Route::get('/reservationFormUsed', [ReservationController::class, 'dispReservationFormUsed'])->name('reservationFormUsed');

// キャンセル画面
Route::get('/dispReservationCancel/{reservation}', [ReservationController::class, 'dispReservationCancel'])->name('dispReservationCancel');
Route::post('/cancelReservation/{reservation}', [ReservationController::class, 'cancelReservation'])->name('cancelReservation');

Route::post('/createReservation', [ReservationController::class, 'createReservation'])->name('createReservation')->middleware('auth');
Route::post('/reservation/sendmail', [ReservationController::class, 'sendMail'])->name('sendMail');

// グーグルカレンダー
Route::post('/googleCalendar/store', [GoogleCalendarController::class, 'store'])->name('googleCalendarStore');

// auth
Auth::routes();

// ===============ユーザー画面　ここまで============

// ===============管理画面画面　ここから============

Route::prefix('admin')->name('admin.')->group(function () {

    //　ログイン画面
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');

    // react画面
    Route::get('/{any}', function () {
        return view('admin.top');
    })->where('any', '^(?!login).+$')->name('top')->middleware('auth:admin');
});

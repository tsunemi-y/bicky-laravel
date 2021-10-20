<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeeController;

use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\LineMessengerController;
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

Route::get('/', function () {
    return view('pages.top');
})->name('top');

Route::get('greeting', function () {
    return view('pages.greeting');
})->name('greeting');

Route::get('access', function () {
    return view('pages.access');
})->name('access');

// 料金画面
Route::get('fee', [FeeController::class, 'index'])->name('fee');

Route::get('introduction', function () {
    return view('pages.introduction');
})->name('introduction');

Route::get('/reservation', [ReservationController::class, 'dispReservationTop'])->name('reservationTop');
Route::get('/reservationForm', [ReservationController::class, 'dispReservationForm'])->name('reservationForm');
Route::get('/reservationFormUsed', [ReservationController::class, 'dispReservationFormUsed'])->name('reservationFormUsed');

// キャンセルコード認証画面
Route::get('/dispCancelCodeVerify', [ReservationController::class, 'dispCancelCodeVerify'])->name('dispCancelCodeVerify');
Route::post('/VerifyCancelCode', [ReservationController::class, 'VerifyCancelCode'])->name('VerifyCancelCode');

// キャンセル画面
Route::get('/dispReservationCancel/{reservation}', [ReservationController::class, 'dispReservationCancel'])->name('dispReservationCancel');
Route::post('/cancelReservation/{reservation}', [ReservationController::class, 'cancelReservation'])->name('cancelReservation');

Route::post('/createReservation', [ReservationController::class, 'createReservation'])->name('createReservation');
Route::post('/reservation/sendmail', [ReservationController::class, 'sendMail'])->name('sendMail');

// ===============ユーザー画面　ここまで============

// ===============管理画面画面　ここから============

Route::prefix('admin')->name('admin.')->group(function () {

    // ルーティング
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/{any}', function () {
        if (!empty(session('loginSession'))) {
            return view('admin.app');
        } else {
            return redirect(url('admin/login'));
        }
    })->where('any', '^(?!login).+$')->name('top');
});

// サイトマップ
// sitemap-indexのルート
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::group(['prefix' => 'sitemaps'], function () {
    // sitemapのルート
    Route::get('basics.xml', [SitemapController::class, 'basics'])->name('sitemap-basics');
    // sitemapを増やす場合はココに追記していく。
});

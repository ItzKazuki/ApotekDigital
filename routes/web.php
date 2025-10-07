<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\CheckIsAlreadyLogin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kasir\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Kasir\MemberController as KasirMemberController;
use App\Http\Controllers\Kasir\ReportController as KasirReportController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\TransactionController as KasirTransactionController;
use App\Http\Controllers\Kasir\ProfileController as KasirProfileController;

Route::get('/', function () {
    return redirect()->route('auth.login');
});

Route::group(['prefix' => 'auth', 'as' => 'auth.', 'middleware' => [CheckIsAlreadyLogin::class]], function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('password/forgot', [ResetPasswordController::class, 'showForgotPasswordForm'])->name('reset-password.request');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('reset-password.form');

    Route::post('login', [LoginController::class, 'login'])->name('login.post');
    Route::post('password/email', [ResetPasswordController::class, 'forgotPassword'])->name('reset-password.email');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('reset-password.update');
});
Route::middleware('auth')->post('auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', IsAdmin::class]], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('report/download/pdf', [ReportController::class, 'downloadPdf'])->name('report.download.pdf');

    Route::get('transaction/{transaction}/{status}', [TransactionController::class, 'updatePaymentStatus'])->name('transaction.updatePaymentStatus');

    Route::resource('drug', DrugController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('member', MemberController::class);
    Route::resource('report', ReportController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('kasir', KasirController::class);

    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::group(['middleware' => 'auth', 'prefix' => 'kasir', 'as' => 'kasir.'], function () {
    Route::get('dashboard', [KasirDashboardController::class, 'index'])->name('index');
    Route::get('dashboard/transaction', [KasirTransactionController::class, 'index'])->name('transaction');
    Route::post('dashboard/transaction/{transaction}/update-payment', [KasirTransactionController::class, 'checkStatus'])->name('transaction.update-payment');

    // profile routes
    Route::get('dashboard/profile', [KasirProfileController::class, 'index'])->name('profile');
    Route::put('dashboardprofile', [KasirProfileController::class, 'update'])->name('profile.update');

    // make a transaction
    Route::post('dashboard/transaction', [KasirTransactionController::class, 'store'])->name('transaction.store');
    Route::get('dashboard/transaction/{transaction}', [KasirTransactionController::class, 'show'])->name('transaction.show');
    Route::post('transactions/sendWhatsapp', [TransactionController::class, 'sendWhatsappMessage'])->name('transaction.send.whatsapp');

    // cart routes
    Route::post('cart/products/add', [CartController::class, 'storeCart'])->name('cart.store');
    Route::post('cart/products/add/barcode', [CartController::class, 'storeCartByBarcode'])->name('cart.store.barcode');
    Route::post('cart/products/remove', [CartController::class, 'removeItemCart'])->name('cart.removeItem');
    Route::post('cart/clear', [CartController::class, 'clearCart'])->name('cart.clearItems');
    Route::post('cart/timeout/cart', [CartController::class, 'clearTimeout'])->name('cart.clearTimeout');
    Route::post('cart/products/increment', [CartController::class, 'incrementItemCart'])->name('cart.incrementItem');
    Route::post('cart/products/decrement', [CartController::class, 'decrementItemCart'])->name('cart.decrementItem');
    Route::get('cart/products/show', [CartController::class, 'showCart'])->name('cart.show');

    //reports
    Route::get('dashboard/reports/transaction', [KasirReportController::class, 'index'])->name('report.index');
    Route::get('dashboard/reports/export/transaction/excel', [KasirReportController::class, 'exportTransactionsWithChart'])->name('report.export.excel');

    // member
    Route::get('dashboard/member', [KasirMemberController::class, 'index'])->name('member.index');
    Route::post('dashboard/member/store', [KasirMemberController::class, 'store'])->name('member.create');

    // get member detail using phone number
    Route::post('members/search', [KasirMemberController::class, 'searchMember'])->name('member.search');
});

// public routes
Route::get('/struk/{invoice}', [TransactionController::class, 'streamStruk'])->name('struk.search');

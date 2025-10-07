<?php

use App\Http\Controllers\Api\Member\Auth\LoginController;
use App\Http\Controllers\Api\Member\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Member\DrugController;
use App\Http\Controllers\Api\Member\ProfileController;
use App\Http\Controllers\Api\Member\TransactionController;

Route::group(['middleware' => ['api'], 'prefix' => 'v1'], function () {

    Route::get('drugs', [DrugController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);

    Route::group(['prefix' => 'auth'], function () {
        Route::post('otp/send', [LoginController::class, 'sendOtp'])->name('send-otp');
        Route::post('otp/verify', [LoginController::class, 'verifyOtp'])->name('verify-otp');
    });

    Route::group(['middleware' => ['auth:api'], 'prefix' => 'member'], function () {
        Route::apiResource('products', DrugController::class);

        Route::get('transactions', [TransactionController::class, 'index']);
        Route::get('transactions/{transaction_id}', [TransactionController::class, 'show']);

        Route::get('me', [ProfileController::class, 'index'])->name('profile');
        Route::get('auth/logout', [LoginController::class, 'logout'])->name('logout');
    });
});

<?php

use App\Http\Controllers\CryptoTransaction\DepositController;
use App\Http\Controllers\CryptoTransaction\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/deposit',  DepositController::class);
    Route::post('/withdraw', WithdrawController::class);
});

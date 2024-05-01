<?php

use App\Http\Controllers\CurrencyExchangeGet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/currency-exchange', CurrencyExchangeGet::class)->name('api.currency_exchange.get');

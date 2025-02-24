<?php

use Illuminate\Support\Facades\Route;
use App\Actions\{GetMortgage, MatchProducts};
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('match', MatchProducts::class);
Route::get('mortgage', GetMortgage::class)->name('mortgage');

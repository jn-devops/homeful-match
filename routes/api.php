<?php

use Illuminate\Support\Facades\Route;
use App\Actions\MatchProducts;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('match', MatchProducts::class);

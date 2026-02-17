<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuponController;



Route::get('/ofertas', [OfertaController::class, 'index']);
Route::get('/ofertas/{id}', [OfertaController::class, 'show']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// Rotte API per i contatti
Route::get('/contacts', [ContactController::class, 'index']); 
Route::get('/contacts/{id}', [ContactController::class, 'show']); 
Route::post('/contacts', [ContactController::class, 'store']); 
Route::put('/contacts/{id}', [ContactController::class, 'update']); 
Route::delete('/contacts/{id}', [ContactController::class, 'destroy']); 
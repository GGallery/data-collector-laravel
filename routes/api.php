<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactDetailsController;
use App\Http\Controllers\Api\SyncPointersController;
use App\Http\Controllers\Api\SystemLogController;
use Illuminate\Support\Facades\Route;

// Route::get('/contacts', [ContactController::class, 'index']); 
// Route::get('/contacts/{id}', [ContactController::class, 'show']); 
// Route::post('/contacts', [ContactController::class, 'store']); 
// Route::put('/contacts/{id}', [ContactController::class, 'update']); 
// Route::delete('/contacts/{id}', [ContactController::class, 'destroy']); 

Route::middleware([\App\Http\Middleware\AuthenticateWithToken::class])->group(function () {
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('contacts_details', ContactDetailsController::class);
    Route::apiResource('sync_pointers', SyncPointersController::class);
    Route::apiResource('system_log', SystemLogController::class);
});
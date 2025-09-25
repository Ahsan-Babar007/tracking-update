<?php

use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TrackingController1;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->group(function () {
   
// });
    Route::post('/trackings', [TrackingController1::class, 'create']);
    // ->middleware(['throttle:30,1', 'check_api_password']); // use underscore

    Route::post('/trackings/{tracking_number}/delay', [TrackingController1::class, 'delay']);
    Route::get('/trackings/{tracking_number}', [TrackingController1::class, 'show']);
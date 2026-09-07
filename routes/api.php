<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SupplierAuthController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Default authenticated user route
Route::middleware('auth:sanctum')->get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
);


// Chatbot API
Route::post(
    '/save-chatbot-inquiry',
    [ChatbotController::class, 'store']
);


// Supplier APIs
Route::prefix('supplier')->group(function () {

    Route::post(
        '/register',
        [SupplierAuthController::class, 'register']
    );

    Route::post(
        '/login',
        [SupplierAuthController::class, 'login']
    );

    Route::middleware('auth:sanctum')->group(function () {

        Route::get(
            '/me',
            [SupplierAuthController::class, 'me']
        );

    });

});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\PenaltyController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Middleware\AdminMiddleware;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/cars/available', [CarController::class, 'available']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{id}', [CarController::class, 'show']);

    Route::prefix('rentals')->group(function () {
        Route::get('/', [RentalController::class, 'index']);
        Route::post('/', [RentalController::class, 'store']);
        Route::get('/my-rentals', [RentalController::class, 'myRentals']);
        Route::get('/{id}', [RentalController::class, 'show']);
        Route::put('/{id}', [RentalController::class, 'update']);
        Route::delete('/{id}', [RentalController::class, 'destroy']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/my-payments', [PaymentController::class, 'myPayments']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::put('/{id}/status', [PaymentController::class, 'updateStatus']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
    });

    Route::prefix('returns')->group(function () {
        Route::get('/', [ReturnController::class, 'index']);
        Route::post('/', [ReturnController::class, 'store']);
        Route::get('/my-returns', [ReturnController::class, 'myReturns']);
        Route::get('/{id}', [ReturnController::class, 'show']);
        Route::delete('/{id}', [ReturnController::class, 'destroy']);
    });

    Route::prefix('penalties')->group(function () {
        Route::get('/', [PenaltyController::class, 'index']);
        Route::get('/my-penalties', [PenaltyController::class, 'myPenalties']);
        Route::get('/unpaid', [PenaltyController::class, 'unpaid']);
        Route::get('/{id}', [PenaltyController::class, 'show']);
        Route::put('/{id}/mark-paid', [PenaltyController::class, 'markAsPaid']);
        Route::delete('/{id}', [PenaltyController::class, 'destroy']);
    });

    Route::middleware([AdminMiddleware::class])->group(function () {
        
        Route::post('/cars', [CarController::class, 'store']);
        Route::put('/cars/{id}', [CarController::class, 'update']);
        Route::delete('/cars/{id}', [CarController::class, 'destroy']);

        Route::prefix('reports')->group(function () {
            Route::get('/dashboard', [ReportController::class, 'dashboard']);
            Route::get('/rentals', [ReportController::class, 'rentalReport']);
            Route::get('/payments', [ReportController::class, 'paymentReport']);
            Route::get('/penalties', [ReportController::class, 'penaltyReport']);
            Route::get('/most-rented-cars', [ReportController::class, 'mostRentedCars']);
        });
    });
});
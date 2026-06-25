<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Residents
    Route::apiResource('/residents', ResidentController::class);

    // Houses
    Route::get('/houses/{id}/history', [HouseController::class, 'history']);
    Route::apiResource('/houses', HouseController::class);

    // Payments
    Route::apiResource('/payments', PaymentController::class)->only(['index', 'store', 'destroy']);

    // Bills
    Route::get('/bills', [BillController::class, 'index']);
    Route::post('/bills/generate', [BillController::class, 'generate']);
    Route::get('/bills/summary', [BillController::class, 'summary']);

    // Expenses
    Route::apiResource('/expenses', ExpenseController::class);

    // Reports
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/reports/chart', [ReportController::class, 'chart']);
    Route::get('/reports/detail', [ReportController::class, 'detail']);

    // Payment Types
    Route::get('/payment-types', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\PaymentType::active()->get(),
        ]);
    });

    // Expense Categories
    Route::get('/expense-categories', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\ExpenseCategory::active()->get(),
        ]);
    });
});
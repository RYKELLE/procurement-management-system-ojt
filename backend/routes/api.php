<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\PurchaseRequestController;
use Illuminate\Support\Facades\Route;
use App\Models\Item;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/items', function () {
    return response()->json(Item::all());
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/purchase-requests', [PurchaseRequestController::class, 'index']);
    Route::post('/purchase-requests', [PurchaseRequestController::class, 'store']);
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show']);
    Route::post(('/purchase-requests/{id}/submit'), [PurchaseRequestController::class, 'submit']);

    Route::post(('/purchase-requests/{id}/approve'), [ApprovalController::class, 'approve']);
    Route::post('/purchase-requests/{id}/reject', [ApprovalController::class, 'reject']);

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show']);
});
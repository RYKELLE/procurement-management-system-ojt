<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\PurchaseRequestController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\RoleManagementController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Models\Item;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SupplierController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/items', function () {
    return response()->json(Item::all());
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/purchase-requests', [PurchaseRequestController::class, 'index']);
    Route::post('/purchase-requests', [PurchaseRequestController::class, 'store']);
    Route::get('/purchase-requests/{id}', [PurchaseRequestController::class, 'show']);
    Route::post(('/purchase-requests/{id}/submit'), [PurchaseRequestController::class, 'submit']);

    Route::post(('/purchase-requests/{id}/approve'), [ApprovalController::class, 'approve']);
    Route::post('/purchase-requests/{id}/reject', [ApprovalController::class, 'reject']);

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show']);
    Route::post('/purchase-orders/{id}/complete', [PurchaseOrderController::class, 'markAsCompleted']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
    Route::post('/invoices/{id}/paid', [InvoiceController::class, 'markAsPaid']);

    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);

    Route::get('/users', [UserManagementController::class, 'index']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole']);
    Route::patch('/users/{user}/permissions', [UserManagementController::class, 'updatePermissions']);
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);

    Route::get('/roles', [RoleManagementController::class, 'index']);
    Route::patch('/roles/{role}/permissions', [RoleManagementController::class, 'updatePermissions']);
});

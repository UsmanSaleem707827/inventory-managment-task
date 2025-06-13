<?php
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::controller(InventoryController::class)->group(function () {
    Route::get('/inventory', 'index');
    Route::get('/inventory/{id}', 'show');
    Route::put('/inventory/{id}', 'update');
});

Route::post('/transaction', [TransactionController::class, 'store']);

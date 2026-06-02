<?php

use App\Http\Controllers\Admin\CostCenterController as AdminCostCenterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplyRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Supply Requests
    Route::resource('requests', SupplyRequestController::class)
        ->except(['destroy'])
        ->parameters(['requests' => 'supplyRequest']);
    Route::post('requests/{supplyRequest}/submit', [SupplyRequestController::class, 'submit'])->name('requests.submit');
    Route::post('requests/{supplyRequest}/cancel-request', [SupplyRequestController::class, 'cancelRequest'])->name('requests.cancelRequest');
    Route::post('requests/{supplyRequest}/advance-status', [RequestManagementController::class, 'advanceStatus'])->name('requests.advanceStatus');
    Route::post('requests/{supplyRequest}/approve-cancellation', [RequestManagementController::class, 'approveCancellation'])->name('requests.approveCancellation');
    Route::post('requests/{supplyRequest}/refuse-cancellation', [RequestManagementController::class, 'refuseCancellation'])->name('requests.refuseCancellation');

    // Items
    Route::resource('items', ItemController::class)->except(['show', 'destroy']);
    Route::get('lookup/items', [ItemController::class, 'search'])->name('items.suggest');
    Route::post('lookup/items', [ItemController::class, 'apiStore'])->name('items.inline');

    // Suppliers (buyer + admin)
    Route::resource('suppliers', SupplierController::class)->except(['show', 'destroy']);

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
        Route::resource('costCenters', AdminCostCenterController::class)->except(['show', 'destroy']);
    });
});

require __DIR__.'/auth.php';

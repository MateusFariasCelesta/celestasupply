<?php

use App\Http\Controllers\Admin\CostCenterController as AdminCostCenterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
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

    // Suppliers (buyer + admin)
    Route::resource('suppliers', SupplierController::class)->except(['show', 'destroy']);

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
        Route::resource('costCenters', AdminCostCenterController::class)->except(['show', 'destroy']);
    });
});

require __DIR__.'/auth.php';

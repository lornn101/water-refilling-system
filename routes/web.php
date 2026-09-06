<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cashier User Management
    Route::get('/cashier/users', [UserManagementController::class, 'index'])->name('cashier.users');
    Route::post('/cashier/users/{id}/approve', [UserManagementController::class, 'approve'])->name('cashier.users.approve');
    Route::post('/cashier/users/{id}/reject', [UserManagementController::class, 'reject'])->name('cashier.users.reject');

    // Edit & Update User
    Route::get('/cashier/users/{id}/edit', [UserManagementController::class, 'edit'])->name('cashier.users.edit');
    Route::put('/cashier/users/{id}', [UserManagementController::class, 'update'])->name('cashier.users.update');

    // Delete, Restore, Force Delete User
    Route::delete('/cashier/users/{id}', [UserManagementController::class, 'destroy'])->name('cashier.users.destroy');
    Route::post('/cashier/users/{id}/restore', [UserManagementController::class, 'restore'])->name('cashier.users.restore');
    Route::delete('/cashier/users/{id}/force-delete', [UserManagementController::class, 'forceDelete'])->name('cashier.users.force-delete');

    // Create Rider (using controller method instead of closure)
    Route::get('/cashier/create-rider', [UserManagementController::class, 'createRider'])->name('cashier.create-rider');
    Route::post('/cashier/store-rider', [UserManagementController::class, 'storeRider'])->name('cashier.store-rider');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', [App\Http\Controllers\UserProfileController::class, 'edit'])->name('my-profile');
    Route::put('/my-profile', [App\Http\Controllers\UserProfileController::class, 'update'])->name('my-profile.update');
});

require __DIR__.'/auth.php';
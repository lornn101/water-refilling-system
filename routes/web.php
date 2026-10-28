<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/owner/reports', [ReportController::class, 'index'])->name('owner.reports');
    Route::get('/owner/reports/export-csv', [ReportController::class, 'exportCsv'])->name('owner.reports.export-csv');
    
    // Cashier User Management
    Route::get('/cashier/users', [UserManagementController::class, 'index'])->name('cashier.users');
    Route::post('/cashier/users/{id}/approve', [UserManagementController::class, 'approve'])->name('cashier.users.approve');
    Route::post('/cashier/users/{id}/reject', [UserManagementController::class, 'reject'])->name('cashier.users.reject');
    Route::get('/cashier/users/{id}/edit', [UserManagementController::class, 'edit'])->name('cashier.users.edit');
    Route::put('/cashier/users/{id}', [UserManagementController::class, 'update'])->name('cashier.users.update');
    Route::delete('/cashier/users/{id}', [UserManagementController::class, 'destroy'])->name('cashier.users.destroy');
    Route::post('/cashier/users/{id}/restore', [UserManagementController::class, 'restore'])->name('cashier.users.restore');
    Route::delete('/cashier/users/{id}/force-delete', [UserManagementController::class, 'forceDelete'])->name('cashier.users.force-delete');
    Route::get('/cashier/create-rider', [UserManagementController::class, 'createRider'])->name('cashier.create-rider');
    Route::post('/cashier/store-rider', [UserManagementController::class, 'storeRider'])->name('cashier.store-rider');

    // Customer
    Route::get('/customer/place-order', [OrderController::class, 'create'])->name('customer.place-order');
    Route::post('/customer/place-order', [OrderController::class, 'store'])->name('customer.store-order');
    Route::get('/customer/orders', [OrderController::class, 'customerOrders'])->name('customer.orders');
    Route::get('/customer/orders/{id}/edit', [OrderController::class, 'customerEdit'])->name('customer.edit-order');
    Route::put('/customer/orders/{id}', [OrderController::class, 'customerUpdate'])->name('customer.update-order');
    Route::post('/customer/orders/{id}/cancel', [OrderController::class, 'customerCancel'])->name('customer.cancel-order');

    // Owner / Cashier
    Route::get('/owner/orders', [OrderController::class, 'ownerOrders'])->name('owner.orders');
    Route::get('/owner/orders/{id}', [OrderController::class, 'show'])->name('owner.order-detail');
    Route::post('/owner/orders/{id}/assign-rider', [OrderController::class, 'assignRider'])->name('owner.assign-rider');
    Route::post('/owner/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('owner.update-status');

    // Walk-in
    Route::get('/owner/walk-in-order', [OrderController::class, 'walkInCreate'])->name('owner.walk-in-create');
    Route::post('/owner/walk-in-order', [OrderController::class, 'walkInStore'])->name('owner.walk-in-store');

    // A6 - Billing & Payment
    Route::post('/orders/{id}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::post('/orders/{id}/unmark-paid', [OrderController::class, 'unmarkPaid'])->name('orders.unmark-paid');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

    // A7 - Real-time Tracking
    Route::get('/orders/{id}/track', [OrderController::class, 'trackOrder'])->name('orders.track');
    Route::get('/orders/{id}/status-json', [OrderController::class, 'statusJson'])->name('orders.status-json');

    // Owner Settings
    Route::get('/owner/settings', [SettingController::class, 'index'])->name('owner.settings');
    Route::put('/owner/settings', [SettingController::class, 'update'])->name('owner.settings.update');
    Route::get('/owner/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('owner.reports');
    Route::get('/owner/reports/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('owner.reports.export-csv');
    Route::get('/owner/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('owner.activity-log');

    // Rider
    Route::get('/rider/orders', [OrderController::class, 'riderOrders'])->name('rider.orders');
    Route::post('/rider/orders/{id}/mark-delivered', [OrderController::class, 'markDelivered'])->name('rider.mark-delivered');
    Route::post('/rider/orders/{id}/mark-on-delivery', [OrderController::class, 'markOnDelivery'])->name('rider.mark-on-delivery');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', [UserProfileController::class, 'edit'])->name('my-profile');
    Route::put('/my-profile', [UserProfileController::class, 'update'])->name('my-profile.update');
});

require __DIR__.'/auth.php';
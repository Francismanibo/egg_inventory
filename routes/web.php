<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Public storefront. Ordering operations are protected below. */
Route::get('/', [CustomerOrderController::class, 'home'])->name('home');

/* One sign-in form handles Customer, Admin, and User accounts. */
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
});

/* Backward-compatible customer login URL; it leads to the shared login panel. */
Route::get('/customer/login', fn () => redirect()->route('login'))->name('customer.login');
Route::post('/customer/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('customer.login.submit');

Route::get('/customer/register', [CustomerAuthController::class, 'showRegister'])
    ->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('customer.register.submit');

/* Legacy customer logout URL now uses the shared logout logic. */
Route::post('/customer/logout', [AuthController::class, 'logout'])->name('customer.logout');

/* Customer order placement and order tracking require customer authentication. */
Route::middleware('customer.auth')->group(function (): void {
    Route::get('/order', [CustomerOrderController::class, 'create'])->name('customer.order');
    Route::post('/order', [CustomerOrderController::class, 'store'])->name('customer.order.store');
    Route::get('/order/{order}/confirmation', [CustomerOrderController::class, 'confirmation'])
        ->name('customer.confirmation');
    Route::get('/my-orders', [CustomerOrderController::class, 'myOrders'])
        ->name('customer.my-orders');
    Route::get('/track-order', [CustomerOrderController::class, 'trackForm'])
        ->name('customer.track.form');
    Route::post('/track-order', [CustomerOrderController::class, 'track'])
        ->middleware('throttle:10,1')
        ->name('customer.track');
});

/* Shared logout is POST + CSRF protected; a direct GET just returns to sign-in. */
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function (Request $request) {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    if ($request->session()->has('customer_account_id')) {
        return redirect()->route('customer.order');
    }

    return redirect()->route('login');
})->name('logout.get');

/* Admin and User can access core production/inventory functions. */
Route::middleware(['auth', 'role:admin,user'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/production', [ProductionController::class, 'index'])->name('admin.production');
    Route::post('/production', [ProductionController::class, 'store'])->name('admin.production.store');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('admin.inventory.history');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
});

/* Admin-only controls, including customer order acceptance/completion. */
Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/admin/orders/{status?}', [AdminOrderController::class, 'index'])
        ->where('status', 'pending|outgoing|done|cancelled')
        ->name('admin.orders');
    Route::post('/admin/orders/{order}/accept', [AdminOrderController::class, 'accept'])
        ->name('admin.orders.accept');
    Route::post('/admin/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])
        ->name('admin.orders.cancel');
    Route::post('/admin/orders/{order}/complete', [AdminOrderController::class, 'complete'])
        ->name('admin.orders.complete');

    Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
});

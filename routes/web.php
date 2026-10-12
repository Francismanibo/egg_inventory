<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AdminOrderController,AuthController,CustomerOrderController,DashboardController,InventoryController,ProductController,ProductionController,ReportController,UserController};
Route::get('/',[CustomerOrderController::class,'home'])->name('home');
Route::get('/order',[CustomerOrderController::class,'create'])->name('customer.order'); Route::post('/order',[CustomerOrderController::class,'store'])->name('customer.order.store');
Route::get('/order/{order}/confirmation',[CustomerOrderController::class,'confirmation'])->name('customer.confirmation'); Route::get('/my-orders',[CustomerOrderController::class,'myOrders'])->name('customer.my-orders'); Route::post('/track-order',[CustomerOrderController::class,'track'])->name('customer.track'); Route::get('/my-orders/{order}',[CustomerOrderController::class,'show'])->name('customer.order.details');
Route::get('/login',[AuthController::class,'showLogin'])->middleware('guest')->name('login'); Route::post('/login',[AuthController::class,'login'])->middleware('guest')->name('login.submit'); Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){
 Route::get('/dashboard',DashboardController::class)->middleware('role:admin,production,inventory')->name('dashboard');
 Route::get('/orders/{status?}',[AdminOrderController::class,'index'])->middleware('role:admin')->where('status','pending|outgoing|done')->name('admin.orders');
 Route::post('/orders/{order}/accept',[AdminOrderController::class,'accept'])->middleware('role:admin')->name('admin.orders.accept'); Route::post('/orders/{order}/done',[AdminOrderController::class,'done'])->middleware('role:admin')->name('admin.orders.done');
 Route::get('/production',[ProductionController::class,'index'])->middleware('role:admin,production')->name('admin.production'); Route::post('/production',[ProductionController::class,'store'])->middleware('role:admin,production')->name('admin.production.store');
 Route::get('/inventory/history',[InventoryController::class,'history'])->middleware('role:admin,inventory')->name('admin.inventory.history');
 Route::get('/inventory',[InventoryController::class,'index'])->middleware('role:admin,inventory')->name('admin.inventory'); Route::post('/inventory',[InventoryController::class,'store'])->middleware('role:admin,inventory')->name('admin.inventory.store');
 Route::get('/reports',[ReportController::class,'index'])->middleware('role:admin,production,inventory')->name('admin.reports');
 Route::get('/products/{product}/edit',[ProductController::class,'edit'])->middleware('role:admin')->name('admin.products.edit');
 Route::get('/products',[ProductController::class,'index'])->middleware('role:admin')->name('admin.products'); Route::post('/products',[ProductController::class,'store'])->middleware('role:admin')->name('admin.products.store'); Route::put('/products/{product}',[ProductController::class,'update'])->middleware('role:admin')->name('admin.products.update');
 Route::get('/users/{user}/edit',[UserController::class,'edit'])->middleware('role:admin')->name('admin.users.edit');
 Route::get('/users',[UserController::class,'index'])->middleware('role:admin')->name('admin.users'); Route::post('/users',[UserController::class,'store'])->middleware('role:admin')->name('admin.users.store'); Route::put('/users/{user}',[UserController::class,'update'])->middleware('role:admin')->name('admin.users.update');
});

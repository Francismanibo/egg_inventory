<?php
namespace App\Http\Controllers;
use App\Models\ProductionRecord; use Illuminate\Support\Carbon; use App\Services\InventoryService;
class DashboardController { public function __invoke(InventoryService $inventory){$today=Carbon::today();$products=$inventory->allBalances();return view('admin.dashboard',['todayProduction'=>(int)ProductionRecord::whereDate('production_date',$today)->sum('quantity_produced'),'currentInventory'=>$products->sum('current_stock'),'lowStockCount'=>$products->where('low_stock',true)->count(),'productionSummary'=>ProductionRecord::with('product')->latest('production_date')->limit(8)->get()]);} }

<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use App\Models\InventoryTransaction;
use App\Models\ProductionRecord;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, InventoryService $inventory)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $type = $request->input('type', 'production');
        $productId = $request->input('product_id');

        $products = EggProduct::where('status', 'active')
            ->orderBy('product_name')
            ->get();

        $productionQuery = ProductionRecord::with('product')
            ->whereBetween('production_date', [$from, $to]);
        $transactionQuery = InventoryTransaction::with('product', 'recorder')
            ->whereBetween('transaction_date', [$from, $to]);

        if ($productId) {
            $productionQuery->where('product_id', $productId);
            $transactionQuery->where('product_id', $productId);
        }

        $productions = $productionQuery->latest('production_date')->get();
        $transactions = $transactionQuery->latest('transaction_date')->get();

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'productId' => $productId,
            'products' => $products,
            'productions' => $productions,
            'transactions' => $transactions,
            'totalProduction' => $productions->sum('quantity_produced'),
            'saleableEggs' => $productions->sum('saleable_quantity'),
            'damagedEggs' => $productions->sum('damaged_quantity'),
            'rejectedEggs' => $productions->sum('rejected_quantity'),
            'currentInventory' => $inventory->allBalances()->sum('current_stock'),
        ]);
    }
}

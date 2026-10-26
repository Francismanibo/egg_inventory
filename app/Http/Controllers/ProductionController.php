<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use App\Models\InventoryTransaction;
use App\Models\ProductionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        return view('admin.production.index', [
            'products' => EggProduct::where('status', 'active')
                ->orderBy('product_name')
                ->get(),
            'records' => ProductionRecord::with('product', 'recorder')
                ->latest('production_date')
                ->paginate(12),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:egg_products,id'],
            'production_date' => ['required', 'date'],
            'quantity_produced' => ['required', 'integer', 'min:0'],
            'saleable_quantity' => ['required', 'integer', 'min:0'],
            'damaged_quantity' => ['required', 'integer', 'min:0'],
            'rejected_quantity' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $classifiedQuantity = $data['saleable_quantity']
            + $data['damaged_quantity']
            + $data['rejected_quantity'];

        if ($classifiedQuantity > $data['quantity_produced']) {
            return back()
                ->withErrors(['saleable_quantity' => 'Classified quantities cannot exceed total production.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $request): void {
            EggProduct::query()
                ->whereKey((int) $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $record = ProductionRecord::create($data + [
                'recorded_by' => $request->user()->id,
            ]);

            if ($record->saleable_quantity > 0) {
                InventoryTransaction::create([
                    'product_id' => $record->product_id,
                    'production_id' => $record->id,
                    'transaction_type' => 'addition',
                    'quantity' => $record->saleable_quantity,
                    'reason' => 'Production',
                    'transaction_date' => $record->production_date,
                    'recorded_by' => $request->user()->id,
                    'remarks' => 'Automatic addition from production entry.',
                ]);
            }
        }, 3);

        return back()->with('success', 'Production record saved.');
    }
}

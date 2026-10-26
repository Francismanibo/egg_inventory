<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => EggProduct::orderBy('product_name')->paginate(12),
        ]);
    }

    public function edit(EggProduct $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->productRules());
        EggProduct::create($data);

        return back()->with('success', 'Egg product created.');
    }

    public function update(Request $request, EggProduct $product)
    {
        $data = $request->validate($this->productRules());
        $product->update($data);

        return back()->with('success', 'Egg product updated.');
    }

    private function productRules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:100'],
            'egg_type' => ['required', 'string', 'max:50'],
            'size_grade' => ['required', 'string', 'max:50'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\EggProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => EggProduct::orderBy('product_name')->paginate(12),
        ]);
    }

    public function edit(EggProduct $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->productRules());
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        EggProduct::create($data);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Egg product created successfully.');
    }

    public function update(Request $request, EggProduct $product): RedirectResponse
    {
        $data = $request->validate($this->productRules());
        unset($data['image'], $data['remove_image']);

        $oldImagePath = $product->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        $product->update($data);

        // Remove the previous file only after the product has been updated.
        if (
            $oldImagePath &&
            $oldImagePath !== $product->image_path
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()
            ->route('admin.products')
            ->with('success', 'Egg product updated successfully.');
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
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}

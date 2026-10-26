@extends('layouts.admin')

@section('title', 'Egg Products')
@section('page-title', 'Egg Products')
@section('page-subtitle', 'Manage egg products, prices, stock thresholds, and photos')

@section('content')
    <section class="card p-6 sm:p-7">
        <h2 class="text-xl font-bold">Add Egg Product</h2>
        <p class="mt-1 text-sm text-slate-500">Upload a JPG, PNG, or WEBP photo up to 4 MB.</p>

        <form method="POST"
              action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data"
              class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf

            <div>
                <label for="product_name" class="mb-1 block text-sm font-semibold">Product Name</label>
                <input id="product_name" class="input" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. Large Eggs (Tray)" maxlength="100" required>
            </div>
            <div>
                <label for="egg_type" class="mb-1 block text-sm font-semibold">Egg Type</label>
                <input id="egg_type" class="input" name="egg_type" value="{{ old('egg_type', 'Chicken Eggs') }}" maxlength="50" required>
            </div>
            <div>
                <label for="size_grade" class="mb-1 block text-sm font-semibold">Size / Grade</label>
                <input id="size_grade" class="input" name="size_grade" value="{{ old('size_grade') }}" placeholder="e.g. Large" maxlength="50" required>
            </div>
            <div>
                <label for="unit" class="mb-1 block text-sm font-semibold">Unit</label>
                <input id="unit" class="input" name="unit" value="{{ old('unit', 'tray') }}" maxlength="30" required>
            </div>
            <div>
                <label for="price" class="mb-1 block text-sm font-semibold">Price (₱)</label>
                <input id="price" class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
            </div>
            <div>
                <label for="reorder_level" class="mb-1 block text-sm font-semibold">Reorder Level</label>
                <input id="reorder_level" class="input" type="number" min="0" step="1" name="reorder_level" value="{{ old('reorder_level', 20) }}" required>
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-semibold">Status</label>
                <select id="status" class="input" name="status" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <label for="image" class="mb-1 block text-sm font-semibold">Egg Photo (Optional)</label>
                <input id="image" class="input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </div>

            <div class="sm:col-span-2 xl:col-span-4">
                <button type="submit" class="btn-primary">Add Product</button>
            </div>
        </form>
    </section>

    <section class="card mt-6 overflow-hidden">
        <div class="border-b border-[#d8e5f3] p-6">
            <h2 class="text-xl font-bold">Product Catalogue</h2>
            <p class="mt-1 text-sm text-slate-500">Product photos will also appear on the customer storefront and ordering page.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-4 py-4">Photo</th>
                        <th class="px-4 py-4">Product</th>
                        <th class="px-4 py-4">Type</th>
                        <th class="px-4 py-4">Grade</th>
                        <th class="px-4 py-4">Unit</th>
                        <th class="px-4 py-4">Price</th>
                        <th class="px-4 py-4">Reorder Level</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-t border-[#eef3f8]">
                            <td class="px-4 py-3">
                                <img
                                    src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('images/egg-hero.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    class="h-14 w-16 rounded-lg border border-[#d8e5f3] object-cover"
                                    loading="lazy"
                                >
                            </td>
                            <td class="px-4 py-4 font-semibold">{{ $product->product_name }}</td>
                            <td class="px-4 py-4">{{ $product->egg_type }}</td>
                            <td class="px-4 py-4">{{ $product->size_grade }}</td>
                            <td class="px-4 py-4">{{ $product->unit }}</td>
                            <td class="px-4 py-4">₱{{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-4 py-4">{{ number_format($product->reorder_level) }}</td>
                            <td class="px-4 py-4">{{ ucfirst($product->status) }}</td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-secondary inline-flex">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">No egg products have been added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5">{{ $products->links() }}</div>
    </section>
@endsection

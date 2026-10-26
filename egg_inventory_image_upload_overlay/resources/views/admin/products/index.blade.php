@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage egg products, classification, and product photos')

@section('content')
    <section class="card p-6 sm:p-7">
        <h2 class="text-xl font-bold">Add Egg Product</h2>
        <p class="mt-1 text-sm text-slate-500">Upload a clear photo of the egg product. JPG, PNG, and WEBP files up to 4 MB are supported.</p>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf

            <div>
                <label for="product-name" class="mb-1 block text-sm font-semibold text-slate-600">Product Name</label>
                <input id="product-name" class="input" name="product_name" value="{{ old('product_name') }}" placeholder="Regular Eggs (Tray)" required maxlength="100">
            </div>
            <div>
                <label for="egg-type" class="mb-1 block text-sm font-semibold text-slate-600">Egg Type</label>
                <input id="egg-type" class="input" name="egg_type" value="{{ old('egg_type', 'Chicken Eggs') }}" required maxlength="50">
            </div>
            <div>
                <label for="size-grade" class="mb-1 block text-sm font-semibold text-slate-600">Size / Grade</label>
                <input id="size-grade" class="input" name="size_grade" value="{{ old('size_grade') }}" placeholder="Regular, Large, Jumbo" required maxlength="50">
            </div>
            <div>
                <label for="unit" class="mb-1 block text-sm font-semibold text-slate-600">Unit</label>
                <input id="unit" class="input" name="unit" value="{{ old('unit', 'tray') }}" required maxlength="30">
            </div>
            <div>
                <label for="price" class="mb-1 block text-sm font-semibold text-slate-600">Price (₱)</label>
                <input id="price" class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
            </div>
            <div>
                <label for="reorder-level" class="mb-1 block text-sm font-semibold text-slate-600">Reorder Level</label>
                <input id="reorder-level" class="input" type="number" min="0" name="reorder_level" value="{{ old('reorder_level', 20) }}" required>
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-semibold text-slate-600">Status</label>
                <select id="status" class="input" name="status" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <label for="new-product-image" class="mb-1 block text-sm font-semibold text-slate-600">Product Photo</label>
                <input id="new-product-image" class="input" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <img id="new-product-image-preview" class="mt-3 hidden h-24 w-32 rounded-xl border border-[#d8e5f3] object-cover" alt="Selected product photo preview">
            </div>
            <div class="sm:col-span-2 xl:col-span-4">
                <button class="btn-primary" type="submit">Add Product</button>
            </div>
        </form>
    </section>

    <section class="card mt-6 overflow-hidden">
        <div class="border-b border-[#d8e5f3] px-6 py-5">
            <h2 class="text-xl font-bold">Product Catalogue</h2>
            <p class="mt-1 text-sm text-slate-500">Use Edit to upload, replace, or remove a product photo.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#f8fbff] text-left text-slate-500">
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
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}" class="h-16 w-20 rounded-lg border border-[#e4edf7] object-cover" loading="lazy">
                                @else
                                    <div class="flex h-16 w-20 items-center justify-center rounded-lg bg-[#f8fbff] text-3xl" title="No photo uploaded">🥚</div>
                                @endif
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
                        <tr><td colspan="9" class="px-6 py-10 text-center text-slate-500">No products have been added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5">{{ $products->links() }}</div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('new-product-image');
            const preview = document.getElementById('new-product-image-preview');

            if (!input || !preview) return;

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (!file) {
                    preview.src = '';
                    preview.classList.add('hidden');
                    return;
                }

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            });
        });
    </script>
@endsection

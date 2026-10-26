@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-subtitle', 'Update egg product information and replace its photo')

@section('content')
    <section class="card max-w-4xl p-6 sm:p-7">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="product-name" class="mb-1 block text-sm font-semibold text-slate-600">Product Name</label>
                <input id="product-name" class="input" name="product_name" value="{{ old('product_name', $product->product_name) }}" required maxlength="100">
            </div>
            <div>
                <label for="egg-type" class="mb-1 block text-sm font-semibold text-slate-600">Egg Type</label>
                <input id="egg-type" class="input" name="egg_type" value="{{ old('egg_type', $product->egg_type) }}" required maxlength="50">
            </div>
            <div>
                <label for="size-grade" class="mb-1 block text-sm font-semibold text-slate-600">Size / Grade</label>
                <input id="size-grade" class="input" name="size_grade" value="{{ old('size_grade', $product->size_grade) }}" required maxlength="50">
            </div>
            <div>
                <label for="unit" class="mb-1 block text-sm font-semibold text-slate-600">Unit</label>
                <input id="unit" class="input" name="unit" value="{{ old('unit', $product->unit) }}" required maxlength="30">
            </div>
            <div>
                <label for="price" class="mb-1 block text-sm font-semibold text-slate-600">Price (₱)</label>
                <input id="price" class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required>
            </div>
            <div>
                <label for="reorder-level" class="mb-1 block text-sm font-semibold text-slate-600">Reorder Level</label>
                <input id="reorder-level" class="input" type="number" min="0" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" required>
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-semibold text-slate-600">Status</label>
                <select id="status" class="input" name="status" required>
                    <option value="active" @selected(old('status', $product->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="product-image" class="mb-2 block text-sm font-semibold text-slate-600">Product Photo</label>
                @if ($product->image_path)
                    <div class="mb-3 flex flex-wrap items-start gap-4">
                        <img id="current-product-image" src="{{ asset('storage/' . $product->image_path) }}" alt="Current photo of {{ $product->product_name }}" class="h-32 w-44 rounded-xl border border-[#d8e5f3] object-cover">
                        <label class="flex items-center gap-2 text-sm text-red-700">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-red-300">
                            Remove current photo
                        </label>
                    </div>
                @else
                    <p class="mb-3 text-sm text-slate-500">No photo has been uploaded for this product.</p>
                @endif

                <input id="product-image" class="input" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <p class="mt-2 text-xs text-slate-500">Choose a new JPG, PNG, or WEBP image up to 4 MB. Uploading a new image replaces the current photo.</p>
                <img id="product-image-preview" class="mt-3 hidden h-32 w-44 rounded-xl border border-[#d8e5f3] object-cover" alt="New product photo preview">
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-3">
                <button class="btn-primary" type="submit">Save Changes</button>
                <a class="btn-secondary" href="{{ route('admin.products') }}">Cancel</a>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('product-image');
            const preview = document.getElementById('product-image-preview');

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

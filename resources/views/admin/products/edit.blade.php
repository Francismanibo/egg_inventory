@extends('layouts.admin')

@section('title', 'Edit Egg Product')
@section('page-title', 'Edit Egg Product')
@section('page-subtitle', 'Update product details and manage its photo')

@section('content')
    <section class="card max-w-4xl p-6 sm:p-7">
        <form method="POST"
              action="{{ route('admin.products.update', $product) }}"
              enctype="multipart/form-data"
              class="grid gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <h2 class="text-xl font-bold">Product Photo</h2>
                <p class="mt-1 text-sm text-slate-500">Upload a JPG, PNG, or WEBP photo up to 4 MB.</p>
                <div class="mt-4 flex flex-wrap items-start gap-5">
                    <div class="h-36 w-44 overflow-hidden rounded-xl border border-[#d8e5f3] bg-[#f8fbff]">
                        <img id="product-image-preview"
                             src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('images/egg-hero.jpg') }}"
                             alt="{{ $product->product_name }}"
                             class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <label for="image" class="mb-1 block text-sm font-semibold">Upload / Replace Photo</label>
                        <input id="image" class="input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <p class="mt-2 text-xs text-slate-500">Choosing a new photo replaces the current photo when you save.</p>
                        @if ($product->image_path)
                            <label class="mt-4 flex items-center gap-2 text-sm text-red-700">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300">
                                Remove the current photo when saving
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label for="product_name" class="mb-1 block text-sm font-semibold">Product Name</label>
                <input id="product_name" class="input" name="product_name" value="{{ old('product_name', $product->product_name) }}" maxlength="100" required>
            </div>

            <div>
                <label for="egg_type" class="mb-1 block text-sm font-semibold">Egg Type</label>
                <input id="egg_type" class="input" name="egg_type" value="{{ old('egg_type', $product->egg_type) }}" maxlength="50" required>
            </div>

            <div>
                <label for="size_grade" class="mb-1 block text-sm font-semibold">Size / Grade</label>
                <input id="size_grade" class="input" name="size_grade" value="{{ old('size_grade', $product->size_grade) }}" maxlength="50" required>
            </div>

            <div>
                <label for="unit" class="mb-1 block text-sm font-semibold">Unit</label>
                <input id="unit" class="input" name="unit" value="{{ old('unit', $product->unit) }}" maxlength="30" required>
            </div>

            <div>
                <label for="price" class="mb-1 block text-sm font-semibold">Price (₱)</label>
                <input id="price" class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required>
            </div>

            <div>
                <label for="reorder_level" class="mb-1 block text-sm font-semibold">Reorder Level</label>
                <input id="reorder_level" class="input" type="number" min="0" step="1" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" required>
            </div>

            <div>
                <label for="status" class="mb-1 block text-sm font-semibold">Status</label>
                <select id="status" class="input" name="status" required>
                    <option value="active" @selected(old('status', $product->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-3 border-t border-[#eef3f8] pt-5">
                <button type="submit" class="btn-primary">Save Product Changes</button>
                <a class="btn-secondary inline-flex" href="{{ route('admin.products') }}">Cancel</a>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('image');
            const preview = document.getElementById('product-image-preview');

            if (!input || !preview) return;

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (!file) return;

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    input.value = '';
                    alert('Please choose a JPG, PNG, or WEBP image.');
                    return;
                }

                if (file.size > 4 * 1024 * 1024) {
                    input.value = '';
                    alert('The image must be 4 MB or smaller.');
                    return;
                }

                preview.src = URL.createObjectURL(file);
            });
        });
    </script>
@endsection

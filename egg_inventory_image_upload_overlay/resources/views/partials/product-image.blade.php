@php
    $imageContainerClass = $imageContainerClass ?? 'h-36';
@endphp

<div class="{{ $imageContainerClass }} w-full overflow-hidden rounded-2xl border border-[#e4edf7] bg-[#f8fbff]">
    @if ($product->image_path)
        <img
            src="{{ asset('storage/' . $product->image_path) }}"
            alt="{{ $product->product_name }}"
            class="h-full w-full object-cover"
            loading="lazy"
        >
    @else
        <div class="flex h-full w-full flex-col items-center justify-center gap-1 text-[#8aa2be]">
            <span class="text-5xl" aria-hidden="true">🥚</span>
            <span class="text-xs">Upload a product photo</span>
        </div>
    @endif
</div>

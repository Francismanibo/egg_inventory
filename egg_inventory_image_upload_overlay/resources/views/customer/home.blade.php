@extends('layouts.customer')

@section('title', 'Fresh Eggs | Home')

@section('content')
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
        <section class="grid overflow-hidden rounded-3xl border border-[#d8e5f3] bg-white shadow-sm lg:grid-cols-2">
            <div class="bg-gradient-to-br from-white to-[#f1f7ff] px-7 py-12 sm:px-12 sm:py-16">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#2878d4]">Farm-fresh quality</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight text-[#163765] sm:text-5xl">Fresh Eggs<br>For Your Home</h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-slate-600">
                    Create a customer account or sign in to place orders and securely follow your own order from review to delivery completion.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('customer.order') }}" class="btn-primary inline-flex items-center">Order Eggs <span class="ml-2">→</span></a>
                    <a href="{{ route('customer.track.form') }}" class="btn-secondary inline-flex items-center">Track an Order</a>
                </div>
            </div>
            <div class="flex items-center justify-center bg-[#e9f3ff] p-8 sm:p-12">
                <div class="w-full max-w-md rounded-3xl border border-white bg-white/70 p-7 shadow-sm sm:p-10">
                    @if ($products->isNotEmpty() && $products->first()->image_path)
                        @include('partials.product-image', ['product' => $products->first(), 'imageContainerClass' => 'h-64'])
                    @else
                        <div class="grid grid-cols-4 gap-4 rounded-3xl bg-[#d8cbbb] p-5 sm:grid-cols-5">
                            @for ($i = 0; $i < 15; $i++)
                                <span class="aspect-square rounded-full border-2 border-[#d0c2b0] bg-gradient-to-br from-white to-[#efdcc7]"></span>
                            @endfor
                        </div>
                    @endif
                    <p class="mt-5 text-center text-sm font-semibold text-[#163765]">Simple ordering. Clear status updates.</p>
                </div>
            </div>
        </section>

        <section class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach ([['Choose products', 'View current egg products and select the quantities you need.'], ['Admin review', 'Every request starts as Pending and is checked by the administrator.'], ['Track delivery', 'Sign in and enter the reference number to view your own order status: pending, out for delivery, or completed.']] as $feature)
                <article class="card p-6 sm:p-7">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e8f1ff] font-bold text-[#2878d4]">✓</div>
                    <h2 class="text-xl font-bold">{{ $feature[0] }}</h2>
                    <p class="mt-2 leading-6 text-slate-600">{{ $feature[1] }}</p>
                </article>
            @endforeach
        </section>

        @if ($products->isNotEmpty())
            <section class="mt-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wider text-[#2878d4]">Available products</p>
                        <h2 class="mt-2 text-3xl font-bold">Choose your eggs</h2>
                    </div>
                    <a href="{{ route('customer.order') }}" class="font-semibold text-[#2878d4]">View all products →</a>
                </div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <article class="card p-6">
                            @include('partials.product-image', ['product' => $product, 'imageContainerClass' => 'h-36'])
                            <h3 class="mt-4 text-lg font-bold">{{ $product->product_name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $product->size_grade }} · {{ $product->unit }}</p>
                            <p class="mt-4 text-xl font-bold text-[#163765]">₱{{ number_format((float) $product->price, 2) }}</p>
                            @if ((int) $product->available_to_order > 0)
                                <span class="mt-3 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ number_format((int) $product->available_to_order) }} available</span>
                            @elseif ((int) $product->current_stock <= 0)
                                <span class="mt-3 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Out of stock</span>
                            @else
                                <span class="mt-3 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Temporarily unavailable</span>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

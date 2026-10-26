@extends('layouts.customer')

@section('title', 'Azupardo Eggventory | Fresh Eggs')

@section('content')
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">

        {{-- HERO SECTION --}}
        <section class="grid overflow-hidden rounded-3xl border border-[#d8e5f3] bg-white shadow-sm lg:grid-cols-2">

            <div class="flex flex-col justify-center bg-gradient-to-br from-white to-[#f1f7ff] px-7 py-12 sm:px-12 sm:py-16">

                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#2878d4]">
                    Azupardo Eggventory
                </p>

                <h1 class="mt-4 text-4xl font-bold leading-tight text-[#163765] sm:text-5xl">
                    Fresh Eggs
                    <br>
                    For Your Home
                </h1>

                <p class="mt-6 max-w-lg text-lg leading-8 text-slate-600">
                    Find the eggs you need with a convenient ordering
                    experience. Place your order, wait for Admin review,
                    and track its progress until completion.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a
                        href="{{ route('customer.order') }}"
                        class="btn-primary inline-flex items-center"
                    >
                        Order Eggs
                        <span class="ml-2">→</span>
                    </a>

                    <a
                        href="{{ route('customer.track.form') }}"
                        class="btn-secondary inline-flex items-center"
                    >
                        Track an Order
                    </a>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">

                    <div>
                        <span class="text-2xl text-[#2878d4]">✓</span>
                        <p class="mt-2 font-bold text-[#163765]">
                            Quality
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Egg products for everyday needs
                        </p>
                    </div>

                    <div>
                        <span class="text-2xl text-[#2878d4]">⌖</span>
                        <p class="mt-2 font-bold text-[#163765]">
                            Locally Owned
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            May-Anao, Tigaon
                        </p>
                    </div>

                    <div>
                        <span class="text-2xl text-[#2878d4]">♡</span>
                        <p class="mt-2 font-bold text-[#163765]">
                            Easy Tracking
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Follow your order status
                        </p>
                    </div>

                </div>
            </div>

            {{-- REALISTIC EGG PHOTO --}}
            <div class="relative min-h-[300px] overflow-hidden bg-[#e9f3ff] sm:min-h-[420px] lg:min-h-full">

                <img
                    src="{{ asset('images/egg-hero.jpg') }}"
                    alt="Fresh brown eggs arranged in an egg tray"
                    class="absolute inset-0 h-full w-full object-cover"
                >

                <div class="absolute inset-x-4 bottom-4 rounded-2xl border border-white/70 bg-white/90 px-5 py-4 shadow-sm backdrop-blur-sm sm:inset-x-6 sm:bottom-6">
                    <p class="font-bold text-[#163765]">
                        Azupardo Eggventory
                    </p>

                    <p class="mt-1 text-sm text-slate-600">
                        Fresh eggs for your home and everyday needs.
                    </p>
                </div>

            </div>
        </section>


        {{-- HOW IT WORKS --}}
        <section class="mt-10 grid gap-5 md:grid-cols-3">

            @foreach ([
                [
                    'number' => '01',
                    'title' => 'Choose Products',
                    'description' => 'Browse available egg products and select the quantities you need.',
                ],
                [
                    'number' => '02',
                    'title' => 'Admin Review',
                    'description' => 'Your order begins as Pending while the administrator reviews it.',
                ],
                [
                    'number' => '03',
                    'title' => 'Track Delivery',
                    'description' => 'Check your order status as it progresses from approval to completion.',
                ],
            ] as $feature)

                <article class="card p-6 sm:p-7">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#e8f1ff] text-sm font-bold text-[#2878d4]">
                        {{ $feature['number'] }}
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-[#163765]">
                        {{ $feature['title'] }}
                    </h2>

                    <p class="mt-2 leading-6 text-slate-600">
                        {{ $feature['description'] }}
                    </p>
                </article>

            @endforeach

        </section>


        {{-- AVAILABLE PRODUCTS --}}
        @if ($products->isNotEmpty())

            <section class="mt-12">

                <div class="flex flex-wrap items-end justify-between gap-4">

                    <div>
                        <p class="text-sm font-bold uppercase tracking-wider text-[#2878d4]">
                            Available Products
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-[#163765]">
                            Choose Your Eggs
                        </h2>

                        <p class="mt-2 text-slate-500">
                            Browse our available egg selections.
                        </p>
                    </div>

                    <a
                        href="{{ route('customer.order') }}"
                        class="font-semibold text-[#2878d4] hover:underline"
                    >
                        View All Products →
                    </a>

                </div>


                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($products as $product)

                        <article class="card overflow-hidden p-4 transition duration-200 hover:-translate-y-1 hover:shadow-md">

                            {{-- PRODUCT PHOTO --}}
                            <div class="overflow-hidden rounded-2xl bg-[#f8fbff]">

                                <img
                                    src="{{ !empty($product->image)
                                        ? asset('storage/' . $product->image)
                                        : asset('images/egg-hero.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    class="h-56 w-full object-cover"
                                    loading="lazy"
                                >

                            </div>

                            <div class="p-2">

                                <h3 class="mt-3 text-lg font-bold text-[#163765]">
                                    {{ $product->product_name }}
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $product->size_grade }}
                                    ·
                                    {{ $product->unit }}
                                </p>

                                <p class="mt-4 text-2xl font-bold text-[#163765]">
                                    ₱{{ number_format((float) $product->price, 2) }}
                                </p>


                                {{-- STOCK STATUS --}}
                                @if ((int) $product->available_to_order > 0)

                                    <span class="mt-3 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ number_format((int) $product->available_to_order) }}
                                        available
                                    </span>

                                    <a
                                        href="{{ route('customer.order') }}"
                                        class="btn-primary mt-4 flex w-full items-center justify-center"
                                    >
                                        Order Now →
                                    </a>

                                @elseif ((int) $product->current_stock <= 0)

                                    <span class="mt-3 inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Out of Stock
                                    </span>

                                    <button
                                        type="button"
                                        disabled
                                        class="mt-4 w-full cursor-not-allowed rounded-lg bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-500"
                                    >
                                        Currently Unavailable
                                    </button>

                                @else

                                    <span class="mt-3 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Temporarily Unavailable
                                    </span>

                                    <button
                                        type="button"
                                        disabled
                                        class="mt-4 w-full cursor-not-allowed rounded-lg bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-500"
                                    >
                                        Currently Unavailable
                                    </button>

                                @endif

                            </div>

                        </article>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- ABOUT US --}}
        <section
            id="about"
            class="mt-12 grid gap-6 lg:grid-cols-5"
        >

            {{-- STORE DESCRIPTION --}}
            <div class="card p-7 sm:p-10 lg:col-span-3">

                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#2878d4]">
                    Get to Know Us
                </p>

                <h2 class="mt-3 text-3xl font-bold leading-tight text-[#163765] sm:text-4xl">
                    About
                    <span class="text-[#2878d4]">
                        Azupardo Eggventory
                    </span>
                </h2>

                <p class="mt-5 leading-8 text-slate-600">
                    Azupardo Eggventory is a locally owned egg store
                    based in May-Anao, Tigaon, Camarines Sur, owned by
                    Jehm Venus Azupardo. The store aims to make buying
                    eggs more convenient by providing customers with
                    an accessible way to view available products,
                    place orders, and monitor their order status.
                </p>

                <p class="mt-4 leading-8 text-slate-600">
                    We believe that ordering everyday essentials should
                    be simple and straightforward. Through our online
                    ordering page, customers can explore egg selections,
                    submit their requests, and follow updates while
                    their orders are being reviewed and prepared for
                    completion.
                </p>

                <p class="mt-4 leading-8 text-slate-600">
                    Whether you are purchasing for your household or
                    looking for eggs for your small business, Azupardo
                    Eggventory strives to provide a convenient ordering
                    experience with clear product details and helpful
                    order-status information.
                </p>

                <a
                    href="{{ route('customer.order') }}"
                    class="btn-primary mt-7 inline-flex items-center"
                >
                    Explore Our Products
                    <span class="ml-2">→</span>
                </a>

            </div>


            {{-- STORE INFORMATION --}}
            <div class="card relative overflow-hidden bg-gradient-to-br from-white to-[#f1f7ff] p-7 sm:p-9 lg:col-span-2">

                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-[#e8f1ff]"></div>

                <div class="relative">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e8f1ff] text-2xl">
                        🥚
                    </div>

                    <h3 class="mt-5 text-2xl font-bold text-[#163765]">
                        Our Store
                    </h3>

                    <p class="mt-2 leading-6 text-slate-600">
                        Your local egg store in the community.
                    </p>


                    <div class="mt-7 border-t border-[#d8e5f3] pt-6">

                        <div class="flex items-start gap-3">
                            <span class="text-xl">📍</span>

                            <div>
                                <h4 class="font-bold text-[#163765]">
                                    Location
                                </h4>

                                <p class="mt-1 leading-6 text-slate-600">
                                    May-Anao, Tigaon,
                                    Camarines Sur, Philippines
                                </p>
                            </div>
                        </div>


                        <div class="mt-6 flex items-start gap-3">
                            <span class="text-xl">👤</span>

                            <div>
                                <h4 class="font-bold text-[#163765]">
                                    Store Owner
                                </h4>

                                <p class="mt-1 text-slate-600">
                                    Jehm Venus Azupardo
                                </p>
                            </div>
                        </div>


                        <div class="mt-6 flex items-start gap-3">
                            <span class="text-xl">🛒</span>

                            <div>
                                <h4 class="font-bold text-[#163765]">
                                    Online Ordering
                                </h4>

                                <p class="mt-1 leading-6 text-slate-600">
                                    Browse products, submit orders,
                                    and follow their status online.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>
@endsection
@extends('layouts.admin')

@section('title', 'Inventory')
@section('page-title', 'Inventory')
@section('page-subtitle', 'Add, deduct, adjust, and view available stock')

@section('content')

    {{-- INVENTORY TRANSACTION AND CURRENT STOCK --}}
    <div class="grid gap-6 xl:grid-cols-2">

        {{-- RECORD INVENTORY TRANSACTION --}}
        <section class="card p-6 sm:p-7">
            <h2 class="text-xl font-bold text-[#163765]">
                Record Inventory Transaction
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Record stock additions, deductions, and corrections.
            </p>

            @if ($products->isEmpty())

                <div class="mt-5 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                    No active egg products are available. Add or activate a product first.
                </div>

            @else

                <form
                    method="POST"
                    action="{{ route('admin.inventory.store') }}"
                    class="mt-6 space-y-5"
                >
                    @csrf

                    {{-- EGG PRODUCT --}}
                    <div>
                        <label
                            for="product_id"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Egg Product
                        </label>

                        <select
                            id="product_id"
                            name="product_id"
                            class="input"
                            required
                        >
                            @foreach ($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    @selected(
                                        (string) old('product_id', $products->first()->id)
                                        === (string) $product->id
                                    )
                                >
                                    {{ $product->product_name }}
                                    — Stock: {{ number_format($product->current_stock) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TRANSACTION TYPE --}}
                    <div>
                        <label
                            for="transaction_type"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Transaction Type
                        </label>

                        <select
                            id="transaction_type"
                            name="transaction_type"
                            class="input"
                            required
                        >
                            <option
                                value="addition"
                                @selected(old('transaction_type', 'addition') === 'addition')
                            >
                                Addition — Add Stock
                            </option>

                            <option
                                value="deduction"
                                @selected(old('transaction_type') === 'deduction')
                            >
                                Deduction — Remove Stock
                            </option>

                            <option
                                value="adjustment"
                                @selected(old('transaction_type') === 'adjustment')
                            >
                                Adjustment — Correct Stock
                            </option>
                        </select>
                    </div>

                    {{-- QUANTITY --}}
                    <div>
                        <label
                            for="quantity"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Quantity
                        </label>

                        <input
                            id="quantity"
                            class="input"
                            type="number"
                            name="quantity"
                            min="1"
                            step="1"
                            value="{{ old('quantity') }}"
                            required
                        >
                    </div>

                    {{-- ADJUSTMENT DIRECTION --}}
                    <div>
                        <label
                            for="adjustment_sign"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Adjustment Direction
                        </label>

                        <select
                            id="adjustment_sign"
                            name="adjustment_sign"
                            class="input"
                        >
                            <option
                                value="1"
                                @selected(old('adjustment_sign', '1') === '1')
                            >
                                Increase (+)
                            </option>

                            <option
                                value="-1"
                                @selected(old('adjustment_sign') === '-1')
                            >
                                Decrease (-)
                            </option>
                        </select>

                        <p class="mt-1 text-xs text-slate-500">
                            Used only for an Adjustment transaction.
                        </p>
                    </div>

                    {{-- REASON --}}
                    <div>
                        <label
                            for="reason"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Reason
                        </label>

                        <input
                            id="reason"
                            class="input"
                            type="text"
                            name="reason"
                            maxlength="150"
                            value="{{ old('reason') }}"
                            placeholder="e.g. New production or damaged eggs"
                            required
                        >
                    </div>

                    {{-- REMARKS --}}
                    <div>
                        <label
                            for="remarks"
                            class="mb-1 block text-sm font-semibold text-[#163765]"
                        >
                            Remarks (Optional)
                        </label>

                        <textarea
                            id="remarks"
                            class="input"
                            name="remarks"
                            rows="3"
                            placeholder="Additional transaction details"
                        >{{ old('remarks') }}</textarea>
                    </div>

                    {{-- SUBMIT --}}
                    <button
                        type="submit"
                        class="btn-primary w-full justify-center"
                    >
                        Save Inventory Transaction
                    </button>
                </form>

            @endif
        </section>


        {{-- CURRENT STOCK --}}
        <section class="card overflow-hidden">

            <div class="border-b border-[#d8e5f3] p-6">
                <h2 class="text-xl font-bold text-[#163765]">
                    Current Stock
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Product photos, stock balances, and available order quantities.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">

                    <thead class="bg-[#f8fbff] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Product</th>
                            <th class="px-4 py-3 font-semibold">Stock</th>
                            <th class="px-4 py-3 font-semibold">Available to Order</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $product)

                            <tr class="border-t border-[#eef3f8]">

                                {{-- PRODUCT PHOTO AND NAME --}}
                                <td class="px-4 py-3">
                                    <div class="flex min-w-[190px] items-center gap-3">

                                        <img
                                            src="{{ $product->image_path
                                                ? asset('storage/' . $product->image_path)
                                                : asset('images/egg-hero.jpg') }}"
                                            alt="{{ $product->product_name }}"
                                            class="h-12 w-14 rounded-lg border border-[#d8e5f3] object-cover"
                                            loading="lazy"
                                        >

                                        <div>
                                            <p class="font-semibold text-[#163765]">
                                                {{ $product->product_name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Reorder at
                                                {{ number_format($product->reorder_level) }}
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                {{-- CURRENT STOCK --}}
                                <td class="px-4 py-4 font-medium">
                                    {{ number_format($product->current_stock) }}
                                </td>

                                {{-- AVAILABLE STOCK --}}
                                <td class="px-4 py-4 font-medium">
                                    {{ number_format($product->available_to_order) }}
                                </td>

                                {{-- STOCK STATUS --}}
                                <td class="px-4 py-4">

                                    @if ($product->current_stock <= 0)

                                        <span class="font-semibold text-red-600">
                                            Out of Stock
                                        </span>

                                    @elseif ($product->available_to_order <= 0)

                                        <span class="font-semibold text-amber-600">
                                            Reserved
                                        </span>

                                    @elseif ($product->low_stock)

                                        <span class="font-semibold text-amber-600">
                                            Low Stock
                                        </span>

                                    @else

                                        <span class="font-semibold text-emerald-600">
                                            Available
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="4"
                                    class="px-5 py-8 text-center text-slate-500"
                                >
                                    No active egg products found.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- ADMIN-ONLY PRODUCT MANAGEMENT BUTTON --}}
            @if (auth()->user()->role === 'admin')

                <div class="border-t border-[#eef3f8] p-5">
                    <a
                        href="{{ route('admin.products') }}"
                        class="btn-secondary inline-flex"
                    >
                        Manage Products &amp; Photos
                    </a>
                </div>

            @endif

        </section>

    </div>


    {{-- RECENT INVENTORY TRANSACTIONS --}}
    <section class="card mt-6 overflow-hidden">

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#d8e5f3] p-6">

            <div>
                <h2 class="text-xl font-bold text-[#163765]">
                    Recent Inventory Transactions
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Review stock movements recorded by authorized users.
                </p>
            </div>

            <a
                href="{{ route('admin.inventory.history') }}"
                class="btn-secondary inline-flex"
            >
                View Full History
            </a>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">

                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Date</th>
                        <th class="px-5 py-4 font-semibold">Product</th>
                        <th class="px-5 py-4 font-semibold">Type</th>
                        <th class="px-5 py-4 font-semibold">Quantity</th>
                        <th class="px-5 py-4 font-semibold">Reason</th>
                        <th class="px-5 py-4 font-semibold">Recorded By</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transactions as $transaction)

                        <tr class="border-t border-[#eef3f8]">

                            {{-- DATE --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $transaction->transaction_date->format('M d, Y') }}
                            </td>

                            {{-- PRODUCT --}}
                            <td class="px-5 py-4 font-semibold">
                                {{ $transaction->product->product_name ?? 'Deleted Product' }}
                            </td>

                            {{-- TYPE --}}
                            <td class="px-5 py-4 capitalize">
                                {{ $transaction->transaction_type }}
                            </td>

                            {{-- QUANTITY --}}
                            <td class="px-5 py-4 font-semibold whitespace-nowrap">

                                @if ($transaction->transaction_type === 'deduction')

                                    <span class="text-red-600">
                                        -{{ number_format($transaction->quantity) }}
                                    </span>

                                @elseif (
                                    $transaction->transaction_type === 'adjustment'
                                    && $transaction->quantity > 0
                                )

                                    <span class="text-emerald-600">
                                        +{{ number_format($transaction->quantity) }}
                                    </span>

                                @elseif (
                                    $transaction->transaction_type === 'adjustment'
                                    && $transaction->quantity < 0
                                )

                                    <span class="text-red-600">
                                        {{ number_format($transaction->quantity) }}
                                    </span>

                                @else

                                    <span class="text-emerald-600">
                                        +{{ number_format($transaction->quantity) }}
                                    </span>

                                @endif

                            </td>

                            {{-- REASON --}}
                            <td class="px-5 py-4">
                                {{ $transaction->reason }}
                            </td>

                            {{-- RECORDED BY --}}
                            <td class="px-5 py-4">
                                {{ $transaction->recorder->name ?? 'Unknown' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-5 py-8 text-center text-slate-500"
                            >
                                No inventory transactions recorded yet.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="border-t border-[#eef3f8] p-5">
            {{ $transactions->links() }}
        </div>

    </section>

@endsection
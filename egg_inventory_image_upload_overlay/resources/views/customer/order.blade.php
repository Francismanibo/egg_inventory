@extends('layouts.customer')

@section('title', 'Order Eggs')

@section('content')
    <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-wider text-[#2878d4]">Customer ordering</p>
            <h1 class="mt-2 text-4xl font-bold">Place an Order</h1>
            <p class="mt-3 leading-7 text-slate-600">
                Select quantities up to the stock available to order. Products with no available stock are marked unavailable.
            </p>
        </div>

        @if ($products->isEmpty())
            <div class="card mt-8 p-8 text-slate-600">There are no active egg products available for ordering right now. Please check again later.</div>
        @else
            <form id="customer-order-form" method="POST" action="{{ route('customer.order.store') }}" class="mt-8 grid items-start gap-6 xl:grid-cols-3">
                @csrf
                <section class="space-y-5 xl:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach ($products as $index => $product)
                            @php
                                $available = (int) $product->available_to_order;
                                $onHand = (int) $product->current_stock;
                            @endphp
                            <article class="product-card card p-5 {{ $available <= 0 ? 'bg-slate-50' : '' }}" data-price="{{ (float) $product->price }}">
                                @include('partials.product-image', ['product' => $product, 'imageContainerClass' => 'h-36'])
                                <div class="mt-4 flex flex-wrap items-start justify-between gap-2">
                                    <h2 class="text-lg font-bold">{{ $product->product_name }}</h2>
                                    @if ($available > 0)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ number_format($available) }} available</span>
                                    @elseif ($onHand <= 0)
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Out of stock</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Temporarily unavailable</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $product->size_grade }} · {{ $product->unit }}</p>
                                <p class="mt-3 text-xl font-bold">₱{{ number_format((float) $product->price, 2) }}</p>
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}">
                                <label for="quantity-{{ $product->id }}" class="mt-5 block text-sm font-semibold text-slate-600">Quantity ({{ $product->unit }})</label>
                                @if ($available > 0)
                                    <input id="quantity-{{ $product->id }}"
                                           class="input quantity-input mt-2"
                                           type="number"
                                           name="items[{{ $index }}][quantity]"
                                           min="0"
                                           max="{{ $available }}"
                                           step="1"
                                           value="{{ min($available, max(0, (int) old('items.' . $index . '.quantity', 0))) }}"
                                           data-name="{{ $product->product_name }}"
                                           data-unit="{{ $product->unit }}"
                                           inputmode="numeric">
                                    <p class="mt-2 text-xs text-slate-500">Maximum order quantity: {{ number_format($available) }}.</p>
                                @else
                                    <input id="quantity-{{ $product->id }}" class="input quantity-input mt-2 cursor-not-allowed bg-slate-100 text-slate-500" type="number" value="0" min="0" max="0" disabled aria-describedby="stock-note-{{ $product->id }}">
                                    <input type="hidden" name="items[{{ $index }}][quantity]" value="0">
                                    <p id="stock-note-{{ $product->id }}" class="mt-2 text-xs text-slate-500">
                                        {{ $onHand <= 0 ? 'This product will become orderable automatically when stock is added.' : 'Current stock is reserved for orders awaiting admin review.' }}
                                    </p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>

                <aside class="card p-6 xl:sticky xl:top-6">
                    <h2 class="text-xl font-bold">Your Order</h2>
                    <p class="mt-1 text-sm text-slate-500">Estimated product total</p>
                    <div id="order-summary" class="mt-4 space-y-3">
                        <p class="rounded-xl bg-[#f8fbff] p-4 text-sm text-slate-500">Choose a quantity above to begin.</p>
                    </div>
                    <div class="mt-5 flex items-center justify-between border-t border-[#d8e5f3] pt-4">
                        <span class="font-semibold text-slate-500">Estimated Total</span>
                        <strong id="estimated-total" class="text-xl">₱0.00</strong>
                    </div>

                    <div class="mt-7 border-t border-[#d8e5f3] pt-6">
                        <h3 class="text-lg font-bold">Customer &amp; Delivery Details</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-semibold" for="customer-name">Full Name</label>
                                <input id="customer-name" class="input" name="customer_name" value="{{ old('customer_name', $customer->name) }}" maxlength="100" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold" for="contact">Contact Number</label>
                                <input id="contact" class="input" name="contact" value="{{ old('contact', $customer->contact) }}" maxlength="30" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold" for="email">Email (optional)</label>
                                <input id="email" class="input" type="email" name="email" value="{{ old('email', $customer->email) }}" maxlength="150">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold" for="address">Delivery Address</label>
                                <textarea id="address" class="input" name="address" rows="3" maxlength="255" required>{{ old('address', $customer->address) }}</textarea>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold" for="notes">Notes (optional)</label>
                                <textarea id="notes" class="input" name="notes" rows="2" maxlength="1000">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <button id="submit-order" type="submit" class="btn-primary mt-6 w-full" disabled>Submit Order Request</button>
                        <p id="stock-submit-note" class="mt-3 text-xs leading-5 text-slate-500">Submitting creates a pending request. Stock is checked again by the server and by the administrator before acceptance.</p>
                    </div>
                </aside>
            </form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const quantityInputs = [...document.querySelectorAll('.quantity-input')];
            const summary = document.getElementById('order-summary');
            const totalElement = document.getElementById('estimated-total');
            const submitButton = document.getElementById('submit-order');
            const submitNote = document.getElementById('stock-submit-note');

            if (!summary || !totalElement || !submitButton) {
                return;
            }

            const currency = amount => new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(amount);

            function normalizedQuantity(input) {
                if (input.disabled) {
                    return 0;
                }

                const max = Math.max(0, Number.parseInt(input.max || '0', 10) || 0);
                const parsed = Number.parseInt(input.value || '0', 10);
                const quantity = Number.isFinite(parsed) ? Math.max(0, Math.min(max, parsed)) : 0;
                input.value = String(quantity);

                return quantity;
            }

            function updateSummary() {
                summary.replaceChildren();
                let total = 0;
                let selectedCount = 0;

                quantityInputs.forEach(input => {
                    const quantity = normalizedQuantity(input);
                    if (quantity <= 0) {
                        return;
                    }

                    const price = Number(input.closest('.product-card').dataset.price);
                    selectedCount += 1;
                    total += quantity * price;

                    const row = document.createElement('div');
                    row.className = 'flex items-start justify-between gap-3 rounded-xl bg-[#f8fbff] p-3 text-sm';

                    const label = document.createElement('span');
                    label.className = 'text-slate-600';
                    label.textContent = `${input.dataset.name} × ${quantity} ${input.dataset.unit}`;

                    const amount = document.createElement('strong');
                    amount.className = 'shrink-0';
                    amount.textContent = currency(quantity * price);

                    row.append(label, amount);
                    summary.append(row);
                });

                if (selectedCount === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'rounded-xl bg-[#f8fbff] p-4 text-sm text-slate-500';
                    empty.textContent = 'Choose a quantity above to begin.';
                    summary.append(empty);
                }

                totalElement.textContent = currency(total);
                submitButton.disabled = selectedCount === 0;
                submitButton.classList.toggle('cursor-not-allowed', selectedCount === 0);
                submitButton.classList.toggle('opacity-50', selectedCount === 0);
                if (submitNote) {
                    submitNote.textContent = selectedCount === 0
                        ? 'Select at least one product with stock available to order.'
                        : 'The server checks available stock again when you submit. Pending customer orders reserve their requested quantities.';
                }
            }

            quantityInputs.forEach(input => input.addEventListener('input', updateSummary));
            updateSummary();
        });
    </script>
@endsection

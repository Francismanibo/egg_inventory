@extends('layouts.customer')

@section('title', 'Track an Order')

@section('content')
    <div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 sm:py-14">
        <div class="mx-auto max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-wider text-[#2878d4]">Order status</p>
            <h1 class="mt-2 text-4xl font-bold">Track Your Order</h1>
            <p class="mt-3 leading-7 text-slate-600">Enter the reference number from an order placed through your signed-in customer account. You can only view orders associated with this account.</p>

            <form method="POST" action="{{ route('customer.track') }}" class="card mt-7 space-y-5 p-6 sm:p-8">
                @csrf
                <div>
                    <label for="order-number" class="mb-1 block text-sm font-semibold">Order Number</label>
                    <input id="order-number" class="input" name="order_number" value="{{ old('order_number') }}" placeholder="ORD-20261013000000-ABCDE" maxlength="30" required>
                </div>
                <button class="btn-primary w-full" type="submit">Check Order Status</button>
            </form>
        </div>

        @isset($order)
            @php
                $steps = [
                    ['pending', 'Order received', 'Your order request is recorded and waiting for administrator review.'],
                    ['outgoing', 'Accepted / Out for Delivery', 'The administrator accepted your order and its status is now out for delivery.'],
                    ['done', 'Delivered / Completed', 'The order has been marked as delivered and completed.'],
                ];
                $statusIndex = array_search($order->status, ['pending', 'outgoing', 'done'], true);
            @endphp

            <section class="card mx-auto mt-8 max-w-2xl p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Order reference</p>
                        <h2 class="mt-1 break-all text-xl font-bold">{{ $order->order_number }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Submitted {{ $order->order_date->format('M d, Y h:i A') }}</p>
                    </div>
                    @if ($order->status === 'pending')
                        <span class="badge-pending">Pending</span>
                    @elseif ($order->status === 'outgoing')
                        <span class="badge-outgoing">Out for Delivery</span>
                    @elseif ($order->status === 'done')
                        <span class="badge-done">Completed</span>
                    @else
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Cancelled</span>
                    @endif
                </div>

                @if ($order->status === 'cancelled')
                    <div class="mt-7 rounded-xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-800">This order was cancelled by the administrator. Its requested quantities are no longer reserved, so you can place a new order for the stock currently available.</div>
                @else
                <div class="mt-7 space-y-5">
                    @foreach ($steps as $index => $step)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $index <= $statusIndex ? 'bg-[#2878d4] text-white' : 'bg-slate-100 text-slate-400' }}">{{ $index < $statusIndex ? '✓' : $index + 1 }}</span>
                                @if ($index < count($steps) - 1)
                                    <span class="mt-1 min-h-6 w-px flex-1 {{ $index < $statusIndex ? 'bg-[#2878d4]' : 'bg-slate-200' }}"></span>
                                @endif
                            </div>
                            <div class="pb-4">
                                <h3 class="font-bold {{ $index <= $statusIndex ? 'text-[#163765]' : 'text-slate-400' }}">{{ $step[1] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $step[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <div class="mt-5 border-t border-[#d8e5f3] pt-5">
                    <h3 class="font-bold">Order Summary</h3>
                    <div class="mt-3 space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-start justify-between gap-4 text-sm">
                                <span class="text-slate-600">{{ $item->product->product_name }} × {{ $item->quantity }}</span>
                                <strong>₱{{ number_format((int) $item->quantity * (float) $item->unit_price, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex justify-between border-t border-[#d8e5f3] pt-4 font-bold">
                        <span>Estimated Total</span>
                        <span>₱{{ number_format((float) $order->estimated_total, 2) }}</span>
                    </div>
                    <div class="mt-5 rounded-xl bg-[#f8fbff] p-4 text-sm leading-6 text-slate-600">
                        <strong class="text-[#163765]">Delivery address:</strong> {{ $order->customer->address }}
                    </div>
                </div>
            </section>
        @endisset
    </div>
@endsection

@extends('layouts.customer')

@section('title', 'Order Details')

@section('content')
    <div class="mx-auto max-w-[1100px] px-6 py-10 sm:px-8">
        <a href="{{ route('customer.my-orders') }}" class="font-semibold text-[#2878d4]">← Back to My Orders</a>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-4xl font-bold">Order Details</h2>
            <span class="{{ $order->status === 'pending' ? 'badge-pending' : ($order->status === 'outgoing' ? 'badge-outgoing' : 'badge-done') }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="card mt-7 p-7">
            <span class="text-sm text-slate-500">Inquiry No.</span>
            <div class="font-bold">{{ $order->order_number }}</div>

            <div class="mt-6 border-t border-[#d8e5f3] pt-5">
                <h3 class="font-bold">Items</h3>
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-4 border-b border-[#eef3f8] py-3">
                        <span>{{ $item->product->product_name }} · Qty: {{ $item->quantity }}</span>
                        <b>₱{{ number_format($item->quantity * $item->unit_price, 2) }}</b>
                    </div>
                @endforeach

                <div class="mt-5 flex justify-between text-lg font-bold">
                    <span>Estimated Total</span>
                    <span>₱{{ number_format($order->estimated_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Customer Orders')
@section('page-title', 'Customer Orders')
@section('page-subtitle', 'Review requests, accept orders, and mark deliveries complete')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([['pending', 'Pending review', $counts['pending']], ['outgoing', 'Accepted / Out for delivery', $counts['outgoing']], ['done', 'Delivered / Completed', $counts['done']], ['cancelled', 'Cancelled', $counts['cancelled']]] as [$key, $label, $count])
            <a href="{{ route('admin.orders', $key) }}" class="card p-5 transition hover:-translate-y-0.5 hover:shadow-md {{ $status === $key ? 'ring-2 ring-[#2878d4]' : '' }}">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold">{{ number_format($count) }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach ([['pending', 'Pending'], ['outgoing', 'Out for Delivery'], ['done', 'Completed'], ['cancelled', 'Cancelled']] as [$key, $label])
            <a href="{{ route('admin.orders', $key) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $status === $key ? 'bg-[#2878d4] text-white' : 'border border-[#d8e5f3] bg-white text-slate-600 hover:bg-[#f8fbff]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-5 space-y-5">
        @forelse ($orders as $order)
            <article class="card overflow-hidden">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#d8e5f3] p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Order reference</p>
                        <h2 class="mt-1 break-all text-lg font-bold">{{ $order->order_number }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $order->order_date->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
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
                </div>

                <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-2">
                    <div>
                        <h3 class="font-bold">Customer Details</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="inline text-slate-500">Name: </dt><dd class="inline font-semibold">{{ $order->customer->name }}</dd></div>
                            <div><dt class="inline text-slate-500">Contact: </dt><dd class="inline font-semibold">{{ $order->customer->contact }}</dd></div>
                            @if ($order->customer->email)
                                <div><dt class="inline text-slate-500">Email: </dt><dd class="inline">{{ $order->customer->email }}</dd></div>
                            @endif
                            <div><dt class="inline text-slate-500">Delivery address: </dt><dd class="inline">{{ $order->customer->address }}</dd></div>
                        </dl>
                        @if ($order->notes)
                            <div class="mt-4 rounded-xl bg-[#f8fbff] p-3 text-sm"><span class="font-semibold">Customer notes:</span> {{ $order->notes }}</div>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold">Requested Items</h3>
                        <div class="mt-3 divide-y divide-[#eef3f8]">
                            @foreach ($order->items as $item)
                                <div class="flex items-start justify-between gap-3 py-3 text-sm">
                                    <div>
                                        <p class="font-semibold">{{ $item->product->product_name }}</p>
                                        <p class="mt-1 text-slate-500">Qty: {{ $item->quantity }} × ₱{{ number_format((float) $item->unit_price, 2) }}</p>
                                    </div>
                                    <strong>₱{{ number_format((int) $item->quantity * (float) $item->unit_price, 2) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 flex justify-between border-t border-[#d8e5f3] pt-4 font-bold">
                            <span>Estimated Total</span>
                            <span>₱{{ number_format((float) $order->estimated_total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-[#d8e5f3] bg-[#fbfdff] p-5 sm:px-6">
                    @if ($order->status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.accept', $order) }}" onsubmit="return confirm('Accept this order and deduct the requested quantities from inventory?')">
                            @csrf
                            <button class="btn-primary" type="submit">Accept Order &amp; Prepare Delivery</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Cancel this pending order and release its reserved stock?')">
                            @csrf
                            <button class="btn-secondary" type="submit">Cancel Order</button>
                        </form>
                    @elseif ($order->status === 'outgoing')
                        <form method="POST" action="{{ route('admin.orders.complete', $order) }}" onsubmit="return confirm('Confirm that this order has been delivered and completed?')">
                            @csrf
                            <button class="btn-primary" type="submit">Mark Delivered / Completed</button>
                        </form>
                    @elseif ($order->status === 'done')
                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">This order is complete.</span>
                    @else
                        <span class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">This order was cancelled and its reserved stock was released.</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="card p-12 text-center">
                <div class="text-4xl">📦</div>
                <h2 class="mt-4 text-xl font-bold">No {{ $status === 'outgoing' ? 'orders awaiting delivery' : ($status === 'done' ? 'completed orders' : ($status === 'cancelled' ? 'cancelled orders' : 'pending orders')) }}</h2>
                <p class="mt-2 text-slate-500">Customer orders in this status will appear here.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
